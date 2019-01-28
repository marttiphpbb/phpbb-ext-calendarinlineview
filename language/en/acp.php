<?php

/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

use marttiphpbb\calendarinlineview\util\cnst;

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_SETTINGS_SAVED'
	=> 'The settings have been saved successfully!',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_OVERALLPAGEBLOCKS_NOT_ENABLED'
	=> 'This extension depends on the %1$sOverall Page Blocks extension%2$s to be installed
	in order to function.',

// page_rendering
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_ISOWEEK'
	=> 'Display the week number (ISO 1806)',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_MOON_PHASE'
	=> 'Display the moon phase',

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_DAYS_NUM'
	=> 'Number of days',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_DAYS_NUM_EXPLAIN'
	=> 'Although this extension is called Calendar Inline View
	it is possible to set another period length than 7 days.',

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_TIME_FORMAT'
	=> 'Time format',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_TIME_FORMAT_EXPLAIN'
	=> 'This is the format used for displaying the time of the moon phases.',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_DERIVE_USER_TIME_FORMAT'
	=> 'Derive user time format',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_DERIVE_USER_TIME_FORMAT_EXPLAIN'
	=> 'Try to derive the time format from the user datetime configuration. Fallback on the default setting below when this fails.',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_DEFAULT_TIME_FORMAT'
	=> 'Default time format',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_DEFAULT_TIME_FORMAT_EXPLAIN'
	=> 'See the %1$sPHP date() function%2$s for defining the format.',

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_EVENT_ROWS'
	=> 'Event rows',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_MIN_ROWS'
	=> 'Minimum event rows',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_MAX_ROWS'
	=> 'Maximum event rows',

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_HEIGHT_CONTAINER'
	=> 'Height of the container',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_HEIGHT_OFFSET'
	=> 'Height offset (em * 10)',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_HEIGHT_OFFSET_EXPLAIN'
	=> 'For week container header and footer',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_HEIGHT_EVENT_ROW'
	=> 'Height of one event row (em * 10)',

	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_STYLESHEET'
	=> 'Stylesheet',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_LOAD_STYLESHEET'
	=> 'Load stylesheet',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_LOAD_STYLESHEET_EXPLAIN'
	=> 'Disable when you load your own stylesheet',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_EXTRA_STYLESHEET'
	=> 'Extra stylesheet',
	'ACP_MARTTIPHPBB_CALENDARINLINEVIEW_EXTRA_STYLESHEET_EXPLAIN'
	=> 'Location of your own stylesheet to overwrite or replace the
	default one. Leave empty when not used.',
]);
