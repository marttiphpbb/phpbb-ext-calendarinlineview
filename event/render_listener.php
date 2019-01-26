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
	protected $render_var = [];

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
			'include'	=> cnst::TPL . 'weekview.html',
			'var'		=> $this->render->get_days_var(),
		];

		$blocks[cnst::FOLDER]['header'] = [
			'include'	=> cnst::TPL . 'header.html',
			'var'		=> $this->render->get_header_var(),
		];

		$this->render_var = $this->render->get_render_var();

		$event['blocks'] = $blocks;
	}
}
