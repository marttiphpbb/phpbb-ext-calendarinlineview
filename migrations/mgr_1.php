<?php
/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\migrations;

use marttiphpbb\calendarweekview\util\cnst;

class mgr_1 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\phpbb\db\migration\data\v32x\v321',
		];
	}

	public function update_data()
	{
		return [
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				cnst::L_ACP
			]],
			['module.add', [
				'acp',
				cnst::L_ACP,
				[
					'module_basename'	=> '\marttiphpbb\calendarweekview\acp\main_module',
					'modes'				=> [
						'placement_index',
						'rendering',
					],
				],
			]],
		];
	}
}
