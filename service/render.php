<?php

/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\service;

use phpbb\event\dispatcher;
use phpbb\language\language;
use marttiphpbb\calendarweekview\render\row_container;
use marttiphpbb\calendarweekview\value\topic;
use marttiphpbb\calendarweekview\value\dayspan;
use marttiphpbb\calendarweekview\value\calendar_event;
use marttiphpbb\calendarweekview\service\store;
use marttiphpbb\calendarweekview\service\user_today;
use marttiphpbb\calendarweekview\service\user_time;
use marttiphpbb\calendarweekview\util\cnst;
use marttiphpbb\calendarweekview\util\moon_phase;

class render
{
	const DAYS = 7;

	protected $dispatcher;
	protected $php_ext;
	protected $language;
	protected $root_path;
	protected $store;
	protected $user_today;
	protected $user_time;

	protected $row_count;
	protected $var;

	public function __construct(
		dispatcher $dispatcher,
		string $php_ext,
		language $language,
		string $root_path,
		store $store,
		user_today $user_today,
		user_time $user_time
	)
	{
		$this->dispatcher = $dispatcher;
		$this->php_ext = $php_ext;
		$this->language = $language;
		$this->root_path = $root_path;
		$this->store = $store;
		$this->user_today = $user_today;
		$this->user_time = $user_time;
	}

	public function add_lang():void
	{
		$this->language->add_lang('weekview', cnst::FOLDER);
	}

	public function get_var():array
	{
		if (isset($this->var))
		{
			return $this->var;
		}

		$start_jd = $this->user_today->get_jd();
		$end_jd = $start_jd + self::DAYS;

		if ($this->store->get_show_moon_phase())
		{
			$moon_phase = new moon_phase();
			$moon_phases = $moon_phase->find($start_jd, $end_jd);
			$mphase = reset($moon_phases);
		}
		else
		{
			$mphase = [];
		}

		$events = [];

		/**
		 * Event to fetch the calendar events for the view
		 *
		 * @event
		 * @var int 	start_jd	start julian day of the view
		 * @var int 	end_jd		end julian day of the view
		 * @var array   events      items should contain
		 * start_jd, end_jd, topic_id, forum_id, topic_title
		 */
		$vars = ['start_jd', 'end_jd', 'events'];
		extract($this->dispatcher->trigger_event('marttiphpbb.calendar.view', compact($vars)));

		$row_container = new row_container($this->store->get_min_rows(), $this->store->get_max_rows());

		foreach($events as $e)
		{
			$topic = new topic($e['topic_id'], $e['forum_id'], $e['topic_title']);
			$calendar_event = new calendar_event($e['start_jd'], $e['end_jd'], $topic);
			$row_container->add_calendar_event($calendar_event);
		}

		$col = 0;
		$rows = $row_container->get_rows();

		$this->var = [
			'days'		=> [],
			'months'	=> [],
			'eventrows'	=> [],
		];

		$week_dayspan = new dayspan($start_jd, $end_jd);

		foreach($rows as $row)
		{
			$row_ary = [];
			$segments = $row->get_segments($week_dayspan);

			foreach($segments as $segment)
			{
				if ($segment instanceof calendar_event)
				{
					$topic = $segment->get_topic();

					$params = [
						't'		=> $topic->get_topic_id(),
						'f'		=> $topic->get_forum_id(),
					];

					$link = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, $params);

					$segment_ary = [
						'topic_id'		=> $topic->get_topic_id(),
						'forum_id'		=> $topic->get_forum_id(),
						'topic_title'	=> $topic->get_topic_title(),
						'topic_link'	=> $link,
						'flex'			=> $segment->get_overlap_day_count($week_dayspan),
						's_start'		=> $week_dayspan->contains_day($segment->get_start_jd()),
						's_end'			=> $week_dayspan->contains_day($segment->get_end_jd()),
					];
				}
				else if ($segment instanceof dayspan)
				{
					$segment_ary = [
						'flex'	=> $segment->get_overlap_day_count($week_dayspan),
					];
				}

				$row_ary[] = $segment_ary;
			}

			$this->var['eventrows'][] = $row_ary;
		}

		for ($jd = $start_jd; $jd <= $end_jd; $jd++)
		{
			$first_day = !$col;
			$day = cal_from_jd($jd, CAL_GREGORIAN);

			if ($day['dayname'] === 'Monday' || $first_day)
			{
				$isoweek = gmdate('W', jdtounix($jd));
			}

			if ($day['day'] === 1 || $first_day)
			{
				$month_abbrev = $day['abbrevmonth'] === 'May' ? 'May_short' : $day['abbrevmonth'];
				$month_abbrev = $this->language->lang(['datetime', $month_abbrev]);
				$month_name = $this->language->lang(['datetime', $day['monthname']]);

				$days_in_month = cal_days_in_month(CAL_GREGORIAN, $day['month'], $day['year']);

				$this->var['months'][] = [
					'flex'	=> min(self::DAYS, $days_in_month - $day['day'] + 1),
					'month_abbrev'	=> $month_abbrev,
					'month_name'	=> $month_name,
					'month'			=> $day['month'],
					'year'			=> $day['year'],
				];
			}

			if (isset($mphase['jd']) && $mphase['jd'] === $jd)
			{
				$phase = $mphase['phase'];
				$moon_time = $this->user_time->get($mphase['time']);
				$moon_title = $this->language->lang(cnst::L . '_' . cnst::MOON_LANG[$phase], $moon_time);
				$moon_icon = cnst::MOON_ICON[$phase];
				$mphase = next($moon_phases);
			}
			else
			{
				$moon_title = false;
				$moon_icon = false;
			}

			$this->var['days'][] = [
				'jd'				=> $jd,
				'weekday'			=> $day['dow'],
				'weekday_name'		=> $this->language->lang(['datetime', $day['dayname']]),
				'weekday_abbrev'	=> $this->language->lang(['datetime', $day['abbrevdayname']]),
				'weekday_class'		=> strtolower($day['abbrevdayname']),
				'monthday'			=> $day['day'],
				'month'				=> $day['month'],
				'month_name'		=> $month_name,
				'month_abbrev'		=> $month_abbrev,
				'year'				=> $day['year'],
				'yearday'			=> $year_begin_jd - $jd + 1,
				'isoweek'			=> $isoweek,
				'moon_title'		=> $moon_title,
				'moon_icon'			=> $moon_icon,
				'col'				=> $col,
			];

			$col++;
		}

		$this->var['render'] = [
			'show_isoweek'		=> $this->store->get_show_isoweek(),
			'show_moon_phase'	=> $this->store->get_show_moon_phase(),
			'load_stylesheet'	=> $this->store->get_load_stylesheet(),
			'extra_stylesheet'	=> $this->store->get_extra_stylesheet(),
			'height_offset'		=> $this->store->get_height_offset_cont(),
			'height_event_row'	=> $this->store->get_height_event_row(),
			'row_count'			=> count($rows),
		];

		return $this->var;
	}
}
