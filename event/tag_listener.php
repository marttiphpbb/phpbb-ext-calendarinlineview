<?php
/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\event;

use phpbb\controller\helper;
use marttiphpbb\calendarweekview\service\store;
use phpbb\event\data as event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class tag_listener implements EventSubscriberInterface
{
	protected $helper;

	public function __construct(helper $helper, store $store)
	{
		$this->helper = $helper;
		$this->store = $store;
	}

	static public function getSubscribedEvents()
	{
		return [
			'marttiphpbb.calendartag.link'	=> 'link',
		];
	}

	public function link(event $event)
	{
		$link = $event['link'];

		if ($link)
		{
			return;
		}

		$params = [
			'year'	=> $event['year'],
			'month'	=> $event['month'],
		];

		if ($this->store->get_topic_hilit())
		{
			$params['t'] = $event['topic_id'];
		}

		$link = $this->helper->route('marttiphpbb_calendarweekview_page_controller', $params);

		$event['link'] = $link;
	}
}
