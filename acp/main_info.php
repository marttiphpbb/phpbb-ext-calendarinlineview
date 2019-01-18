<?php
/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\acp;

use marttiphpbb\calendarweekview\util\cnst;

class main_info
{
	function module()
	{
		return [
			'filename'	=> '\marttiphpbb\calendarweekview\acp\main_module',
			'title'		=> cnst::L_ACP,
			'modes'		=> [
				'rendering'	=> [
					'title' => cnst::L_ACP . '_RENDERING',
					'auth' => 'ext_marttiphpbb/calendarweekview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
			],
		];
	}
}
