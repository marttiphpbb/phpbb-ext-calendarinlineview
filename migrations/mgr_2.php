<?php
/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\migrations;

use marttiphpbb\calendarweekview\util\cnst;

class mgr_2 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\marttiphpbb\calendarweekview\migrations\mgr_1',
		];
	}

	public function update_data()
	{
		$data = [
			'min_rows'					=> 5,
			'max_rows'					=> 30,
			'first_weekday'				=> 0,
			'show_today'				=> true,
			'show_isoweek'				=> false,
			'show_moon_phase'			=> false,
			'topic_hilit'				=> false,
			'pag_neighbours'			=> 2,
			'pag_show_prev_next'		=> true,
			'load_stylesheet'			=> true,
			'extra_stylesheet'			=> '',
			'height_event_row'			=> 20,
			'height_offset_week_cont'	=> 50,
			'derive_user_time_format'	=> true,
			'default_time_format'		=> 'H:i',
		];

		return [
			['config_text.add', [cnst::ID, serialize($data)]],
		];
	}
}
