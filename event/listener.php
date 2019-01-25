<?php
/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\event;

use phpbb\event\data as event;
use marttiphpbb\calendarweekview\util\cnst;
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

	public function get_date():array
	{
		$now = $this->user->create_datetime();
		$time_offset = $now->getOffset();
		return phpbb_gmgetdate($now->getTimestamp() + $time_offset);
	}

	public function get_jd():int
	{
		$now = $this->get_date();
		return cal_to_jd(CAL_GREGORIAN, $now['mon'], $now['mday'], $now['year']);
	}
}
