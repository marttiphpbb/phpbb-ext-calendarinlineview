<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\util;

class cnst
{
	const FOLDER = 'marttiphpbb/calendarinlineview';
	const ID = 'marttiphpbb_calendarinlineview';
	const PREFIX = self::ID . '_';
	const CACHE_ID = '_' . self::ID;
	const L = 'MARTTIPHPBB_CALENDARINLINEVIEW';
	const L_ACP = 'ACP_' . self::L;
	const L_MCP = 'MCP_' . self::L;
	const TPL = '@' . self::ID . '/';
	const EXT_PATH = 'ext/' . self::FOLDER . '/';

	const MOON_ICON = [
		0 	=> 'fa-circle',
		1	=> 'fa-adjust fa-rotate-180',
		2	=> 'fa-circle-o',
		3	=> 'fa-adjust',
	];

	const MOON_LANG = [
		0	=> 'NEW_MOON',
		1	=> 'FIRST_QUARTER_MOON',
		2	=> 'FULL_MOON',
		3	=> 'THIRD_QUARTER_MOON',
	];

	const DEFAULT_SETTINGS = [
		'index' => [
			'days_num' 	=> 10,
			'min_rows' 	=> 2,
			'max_rows' 	=> 30,
			'template' 	=> 'abbrev_moon_lg_bottom',
		],
		'forums' => [
			'local_events' 	=> true,
			'days_num' 		=> 10,
			'min_rows' 		=> 2,
			'max_rows' 		=> 30,
			'template' 		=> 'abbrev_moon_lg_bottom',
			'en_ary'	=> [
				'viewforum'	=> [],
				'viewtopic'	=> [],
				'posting'	=> [],
			],
		],
		'load_stylesheet' 			=> true,
		'extra_stylesheet' 			=> '',
		'derive_user_time_format' 	=> true,
		'default_time_format' 		=> 'H:i',
	];

	const TEMPLATES = [
		'clean',
		'moon_sm_bottom',
		'moon_sm_top',

		'day_sm_bottom',
		'day_sm_top',
		'day_moon_sm_bottom',
		'day_moon_sm_top',

		'abbrev_moon_sm_bottom',
		'abbrev_moon_sm_top',
		'abbrev_sm_bottom',
		'abbrev_sm_top',

		'abbrev_lg_bottom',
		'abbrev_lg_top',
		'abbrev_moon_lg_bottom',
		'abbrev_moon_lg_top',

		'full_lg_bottom',
		'full_lg_top',
		'full_moon_lg_bottom',
		'full_moon_lg_top',
	];
}
