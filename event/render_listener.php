<?php
/**
* phpBB Extension - marttiphpbb calendarweekview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarweekview\event;

use phpbb\controller\helper;
use phpbb\event\data as event;
use phpbb\auth\auth;
use marttiphpbb\calendarweekview\service\render;
use marttiphpbb\calendarweekview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class render_listener implements EventSubscriberInterface
{
	protected $auth;
	protected $render;

	public function __construct(
		auth $auth,
		render $render
	)
	{
		$this->auth = $auth;
		$this->render = $render;
	}

	static public function getSubscribedEvents()
	{
		return [
			'marttiphpbb.overallpageblocks'	=> 'add_blocks',
		];
	}

	public function add_blocks(event $event):void
	{
		$blocks = $event['blocks'];

		if (!count($this->auth->acl_getf('f_read')))
		{
			return;
		}

		$blocks[cnst::FOLDER]['index'] = [
			'include'	=> cnst::TPL . 'calendarweekview.html',
			'var'		=> $this->render->get_vars(),
		];

		$event['blocks'] = $blocks;
	}
}
