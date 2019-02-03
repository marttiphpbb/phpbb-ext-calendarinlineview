<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\migrations;

use marttiphpbb\calendarinlineview\util\cnst;

class mgr_2 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\marttiphpbb\calendarinlineview\migrations\mgr_1',
		];
	}

	public function update_data()
	{
		$data = [
			'days_num'					=> 7,
			'min_rows'					=> 5,
			'max_rows'					=> 30,
			'show_isoweek'				=> false,
			'show_moon_phase'			=> false,
			'load_stylesheet'			=> true,
			'extra_stylesheet'			=> '',
			'derive_user_time_format'	=> true,
			'default_time_format'		=> 'H:i',
		];

		return [
			['config_text.add', [cnst::ID, serialize($data)]],
		];
	}
}
