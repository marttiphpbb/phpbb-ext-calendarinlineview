<?php

/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [

	'ACP_MARTTIPHPBB_CALENDARWEEKVIEW'
	=> 'Calendar Week View',
	'ACP_MARTTIPHPBB_CALENDARWEEKVIEW_PLACEMENT_INDEX'
	=> 'Placement On Index',
	'ACP_MARTTIPHPBB_CALENDARWEEKVIEW_RENDERING'
	=> 'Rendering',
]);