<?php

/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\service;

use phpbb\event\dispatcher;
use phpbb\language\language;
use marttiphpbb\calendarinlineview\render\row_container;
use marttiphpbb\calendarinlineview\value\topic;
use marttiphpbb\calendarinlineview\value\dayspan;
use marttiphpbb\calendarinlineview\value\calendar_event;
use marttiphpbb\calendarinlineview\service\user_today;
use marttiphpbb\calendarinlineview\service\user_time;
use marttiphpbb\calendarinlineview\util\cnst;
use marttiphpbb\calendarinlineview\util\moon_phase;

class render
{
	protected $dispatcher;
	protected $php_ext;
	protected $language;
	protected $root_path;
	protected $user_today;
	protected $user_time;

	protected $row_count;
	protected $var;

	public function __construct(
		dispatcher $dispatcher,
		string $php_ext,
		language $language,
		string $root_path,
		user_today $user_today,
		user_time $user_time
	)
	{
		$this->dispatcher = $dispatcher;
		$this->php_ext = $php_ext;
		$this->language = $language;
		$this->root_path = $root_path;
		$this->user_today = $user_today;
		$this->user_time = $user_time;
	}

	public function add_lang():void
	{
		$this->language->add_lang('inlineview', cnst::FOLDER);
	}

	public function get_var(
		int $days_num,
		int $min_rows,
		int $max_rows,
		int $forum_id
	):array
	{
		if (isset($this->var))
		{
			return $this->var;
		}

		$start_jd = $this->user_today->get_jd();
		$end_jd = $start_jd + $days_num - 1;

		$moon_phase = new moon_phase();
		$moon_phases = $moon_phase->find($start_jd, $end_jd);
		$mphase = reset($moon_phases);

		$events = [];

		if ($forum_id > 0)
		{
			/**
			 * Event to fetch the calendar events of a forum
			 *
			 * @event
			 * @var int 	start_jd	start julian day of the view
			 * @var int 	end_jd		end julian day of the view
			 * @var int 	forum_id	the forum
			 * @var array   events      items should contain
			 * start_jd, end_jd, topic_id, forum_id, topic_title
			 */
			$vars = ['start_jd', 'end_jd', 'forum_id', 'events'];
			extract($this->dispatcher->trigger_event('marttiphpbb.calendar.view_forum', compact($vars)));
		}
		else
		{
			/**
			 * Event to fetch the calendar events
			 *
			 * @event
			 * @var int 	start_jd	start julian day of the view
			 * @var int 	end_jd		end julian day of the view
			 * @var array   events      items should contain
			 * start_jd, end_jd, topic_id, forum_id, topic_title
			 */
			$vars = ['start_jd', 'end_jd', 'events'];
			extract($this->dispatcher->trigger_event('marttiphpbb.calendar.view', compact($vars)));
		}

		$row_container = new row_container($min_rows, $max_rows);

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

		foreach($rows as $row)
		{
			$row_ary = [];
			$segments = $row->get_segments($week_dayspan);

			foreach($segments as $segment)
			{
				$segment_ary = [];

				if ($segment instanceof calendar_event)
				{
					$topic = $segment->get_topic();

					$params = [
						't'		=> $topic->get_topic_id(),
						'f'		=> $topic->get_forum_id(),
					];

					$topic_link = append_sid($this->root_path . 'viewtopic.' . $this->php_ext, $params);

					$segment_ary = [
						'topic_id'		=> $topic->get_topic_id(),
						'forum_id'		=> $topic->get_forum_id(),
						'topic_title'	=> $topic->get_topic_title(),
						'topic_link'	=> $topic_link,
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

		for ($jd = $start_jd; $jd <= $end_jd; $jd++) {
			$first_day = !$col;
			$day = cal_from_jd($jd, CAL_GREGORIAN);

			$link = '';
			$year = $day['year'];
			$month = $day['month'];
			$monthday = $day['day'];

			/**
			 * Event to get a link to the calendar view page (if available)
			 *
			 * @event
			 * @var int		jd
			 * @var int 	year
			 * @var int 	month
			 * @var int		monthday
			 * @var string	link
			 */
			$vars = ['jd', 'year', 'month', 'monthday', 'link'];
			extract($this->dispatcher->trigger_event('marttiphpbb.calendar.view_link', compact($vars)));

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
					'flex'			=> min($days_num - $col, $days_in_month - $day['day'] + 1),
					'month_abbrev'	=> $month_abbrev,
					'month_name'	=> $month_name,
					'month'			=> $day['month'],
					'year'			=> $day['year'],
					'link'			=> $link,
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
				'isoweek'			=> $isoweek,
				'moon_title'		=> $moon_title,
				'moon_icon'			=> $moon_icon,
				'col'				=> $col,
				'link'				=> $link,
			];

			$col++;
		}

		return $this->var;
	}
}
