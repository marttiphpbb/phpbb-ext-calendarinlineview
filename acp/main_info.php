<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\acp;

use marttiphpbb\calendarinlineview\util\cnst;

class main_info
{
	function module()
	{
		return [
			'filename'	=> '\marttiphpbb\calendarinlineview\acp\main_module',
			'title'		=> cnst::L_ACP,
			'modes'		=> [
				'index'	=> [
					'title' => cnst::L_ACP . '_INDEX',
					'auth' => 'ext_marttiphpbb/calendarinlineview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
				'forums'	=> [
					'title' => cnst::L_ACP . '_FORUMS',
					'auth' => 'ext_marttiphpbb/calendarinlineview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
				'rendering'	=> [
					'title' => cnst::L_ACP . '_RENDERING',
					'auth' => 'ext_marttiphpbb/calendarinlineview && acl_a_board',
					'cat' => [cnst::L_ACP],
				],
			],
		];
	}
}
