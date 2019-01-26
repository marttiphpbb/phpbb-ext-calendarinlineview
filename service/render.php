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
	protected $dispatcher;
	protected $php_ext;
	protected $language;
	protected $root_path;
	protected $store;
	protected $user_today;
	protected $user_time;

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

	public function get_var():array
	{
		$this->language->add_lang('weekview', cnst::FOLDER);

		$start_jd = $this->user_today->get_jd();
		$end_jd = $start_jd + 7;
		$days_num = 7;

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
		$total_dayspan = new dayspan($start_jd, $end_jd);
		$rows = $row_container->get_rows();

		for ($jd = $start_jd; $jd <= $end_jd; $jd++)
		{
			$first_day = !$col;
			$weekcol = $col % 7;
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

				if ($month === $day['month'])
				{
					$this->template->assign_vars([
						'MONTH'				=> $month,
						'MONTH_NAME'		=> $this->language->lang(['datetime', $day['monthname']]),
						'MONTH_ABBREV'		=> $this->language->lang(['datetime', $month_abbrev]),
						'YEAR'				=> $year,
						'TODAY_JD'			=> $today_jd,
						'SHOW_ISOWEEK'		=> $this->store->get_show_isoweek(),
						'SHOW_MOON_PHASE'	=> $this->store->get_show_moon_phase(),
						'LOAD_STYLESHEET'	=> $this->store->get_load_stylesheet(),
						'EXTRA_STYLESHEET'	=> $this->store->get_extra_stylesheet(),
						'HEIGHT_OFFSET'		=> $this->store->get_height_offset_week_cont(),
						'HEIGHT_EVENT_ROW'	=> $this->store->get_height_event_row(),
						'EVENT_ROW_COUNT'	=> count($rows),
					]);
				}
			}

			if (!$weekcol)
			{
				$this->template->assign_block_vars('weeks', []);

				foreach($rows as $row)
				{
					$this->template->assign_block_vars('weeks.eventrows', []);

					$week_end_jd = $jd + 6;

					$week_dayspan = new dayspan($jd, $week_end_jd);
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

							$this->template->assign_block_vars('weeks.eventrows.eventsegments', [
								'TOPIC_ID'			=> $topic->get_topic_id(),
								'FORUM_ID'			=> $topic->get_forum_id(),
								'TOPIC_TITLE'		=> $topic->get_topic_title(),
								'TOPIC_LINK'		=> $link,
								'FLEX'				=> $segment->get_overlap_day_count($week_dayspan),
								'S_START'			=> $week_dayspan->contains_day($segment->get_start_jd()),
								'S_END'				=> $week_dayspan->contains_day($segment->get_end_jd()),
							]);
						}
						else if ($segment instanceof dayspan)
						{
							$this->template->assign_block_vars('weeks.eventrows.eventsegments', [
								'FLEX'		=> $segment->get_overlap_day_count($week_dayspan),
							]);
						}
					}
				}
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

			$this->template->assign_block_vars('weeks.weekdays', [
				'JD'				=> $jd,
				'WEEKDAY'			=> $day['dow'],
				'WEEKDAY_NAME'		=> $this->language->lang(['datetime', $day['dayname']]),
				'WEEKDAY_ABBREV'	=> $this->language->lang(['datetime', $day['abbrevdayname']]),
				'WEEKDAY_CLASS'		=> strtolower($day['abbrevdayname']),
				'MONTHDAY'			=> $day['day'],
				'MONTH'				=> $day['month'],
				'MONTH_NAME'		=> $month_name,
				'MONTH_ABBREV'		=> $month_abbrev,
				'YEAR'				=> $day['year'],
				'YEARDAY'			=> $year_begin_jd - $jd + 1,
				'ISOWEEK'			=> $isoweek,
				'MOON_TITLE'		=> $moon_title,
				'MOON_ICON'			=> $moon_icon,
				'COL'				=> $col,
				'WEEKCOL'			=> $weekcol,
			]);

			$col++;
		}

		return $this->helper->render('month.html', $title);
	}

	public function get_days_var():array
	{
		return [];
	}

	public function get_header_var():array
	{
		return [];
	}

	public function get_render_var():array
	{
		return [];
	}
}
