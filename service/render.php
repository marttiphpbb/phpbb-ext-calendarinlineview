<?php

/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\service;

use phpbb\event\dispatcher;
use phpbb\language\language;
use marttiphpbb\calendarinlineview\render\row_container;
use marttiphpbb\calendarinlineview\value\topic;
use marttiphpbb\calendarinlineview\value\dayspan;
use marttiphpbb\calendarinlineview\value\calendar_event;
use marttiphpbb\calendarinlineview\service\store;
use marttiphpbb\calendarinlineview\service\user_today;
use marttiphpbb\calendarinlineview\service\user_time;
use marttiphpbb\calendarinlineview\util\cnst;
use marttiphpbb\calendarinlineview\util\moon_phase;

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
		$this->language->add_lang('inlineview', cnst::FOLDER);
	}

	public function get_var():array
	{
		if (isset($this->var))
		{
			return $this->var;
		}

		$days_num = $this->store->get_days_num();

		$start_jd = $this->user_today->get_jd();
		$end_jd = $start_jd + $days_num - 1;

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

		error_log(json_encode($events));

		foreach($events as $event)
		{
			$topic = new topic($event['topic_id'], $event['forum_id'], $event['topic_title']);
			$calendar_event = new calendar_event($event['start_jd'], $event['end_jd'], $topic);
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

		error_log(json_encode($rows));

		foreach($rows as $row)
		{
			$row_ary = [];
			$segments = $row->get_segments($week_dayspan);

			error_log('row segments: ' . json_encode($segments));

			foreach($segments as $segment)
			{
				$segment_ary = [];

				if ($segment instanceof calendar_event)
				{
					error_log('calendar');
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

		error_log('eventrows: ' . json_encode($this->var['eventrows']));

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
					'flex'			=> min($days_num, $days_in_month - $day['day'] + 1),
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
			'event_row_count'	=> count($rows),
		];

		return $this->var;
	}
}
