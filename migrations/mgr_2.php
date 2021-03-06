<?php

/**
 * phpBB Extension - marttiphpbb calendarinlineview
 * @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace marttiphpbb\calendarinlineview\migrations;

use marttiphpbb\calendarinlineview\util\cnst;

class mgr_2 extends \phpbb\db\migration\migration
{
	static public function depends_on():array
	{
		return [
			'\marttiphpbb\calendarinlineview\migrations\mgr_1',
		];
	}

	public function update_data():array
	{
		return [
			['config_text.add', [cnst::ID, serialize(cnst::DEFAULT_SETTINGS)]],
		];
	}
}
