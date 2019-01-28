<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\event;

use phpbb\event\data as event;
use marttiphpbb\calendarinlineview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'	=> 'core_user_setup',
		];
	}

	public function core_user_setup(event $event)
	{
		$lang_set_ext = $event['lang_set_ext'];

		$lang_set_ext[] = [
			'ext_name' => cnst::FOLDER,
			'lang_set' => 'common',
		];

		$event['lang_set_ext'] = $lang_set_ext;
	}
}
