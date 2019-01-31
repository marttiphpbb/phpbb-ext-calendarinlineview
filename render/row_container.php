<?php

/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\render;

use marttiphpbb\calendarinlineview\value\calendar_event;
use marttiphpbb\calendarinlineview\value\dayspan;
use marttiphpbb\calendarinlineview\render\calendar_event_row;

class row_container
{
	protected $rows = [];
	protected $max_rows;

	public function __construct(int $min_rows, int $max_rows)
	{
		for ($row_index = 0; $row_index < $min_rows; $row_index++)
		{
			$this->get_row($row_index);
		}

		$this->max_rows = $max_rows;
	}

	private function get_row(int $row_index):calendar_event_row
	{
		if (!$this->rows[$row_index])
		{
			$this->rows[$row_index] = new calendar_event_row();
		}

		return $this->rows[$row_index];
	}

	public function add_calendar_event(calendar_event $calendar_event):void
	{
		error_log('add event');

		for($row_index = 0; $row_index < $this->max_rows; $row_index++)
		{
			$row = $this->get_row($row_index);

			error_log('search row index: ' . $row_index);

			if (!is_null($free_segment_index = $row->get_free_segment_index($calendar_event)))
			{
				error_log('free_segment: ' . $free_segment_index);
				$row->insert($free_segment_index, $calendar_event);
				return;
			}
		}
	}

	public function get_row_count():int
	{
		return count($this->rows);
	}

	public function get_rows():array
	{
		return $this->rows;
	}
}
