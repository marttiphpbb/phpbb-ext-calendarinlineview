<?php

/**
* phpBB Extension - marttiphpbb calendarinlineview
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

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW'
	=> 'Calendar Inline View',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_INDEX'
	=> 'Index',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_FORUMS'
	=> 'Forums',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_RENDERING'
	=> 'Rendering',
]);
