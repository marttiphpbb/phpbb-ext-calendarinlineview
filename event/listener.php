<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\event;

use phpbb\controller\helper;
use phpbb\event\data as event;
use phpbb\auth\auth;
use marttiphpbb\calendarinlineview\service\render;
use marttiphpbb\calendarinlineview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $auth;
	protected $render;
	protected $var;

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
			'core.index_modify_page_title'
				=> 'core_index_modify_page_title',
			'core.viewforum_modify_page_title'
				=> 'core_viewforum_modify_page_title',
			'core.viewtopic_modify_page_title'
				=> 'core_viewtopic_modify_page_title',
			'core.posting_modify_template_vars'
				=> 'core_posting_modify_template_vars',
			'marttiphpbb.overallpageblocks'
				=> 'add_blocks',
			'core.twig_environment_render_template_before'
				=> 'core_twig_environment_render_template_before',
		];
	}

	public function core_index_modify_page_title(event $event):void
	{
		error_log('Index');
	}

	public function core_viewforum_modify_page_title(event $event):void
	{
		$forum_id = $event['forum_id'];

		error_log('Viewforum, forum id: ' . $forum_id);
	}

	public function core_viewtopic_modify_page_title(event $event):void
	{
		$forum_id = $event['forum_id'];

		error_log('Viewtopic, forum id: ' . $forum_id);
	}

	public function core_posting_modify_template_vars(event $event):void
	{
		$forum_id = $event['forum_id'];

		error_log('Posting, forum id: ' . $forum_id);
	}

	public function add_blocks(event $event):void
	{
		$blocks = $event['blocks'];
		$template_events = $event['template_events'];

		if (!isset($template_events[cnst::FOLDER]))
		{
			return;
		}

		if (!count($this->auth->acl_getf('f_read')))
		{
			return;
		}

		$this->render->add_lang();

		$this->var = $this->render->get_var();

		if (isset($template_events[cnst::FOLDER]['index']))
		{
			$blocks[cnst::FOLDER]['index'] = [
				'include'	=> cnst::TPL . 'inlineview.html',
				'var'		=> $this->var['days'],
			];
		}

		if (isset($template_events[cnst::FOLDER]['header']))
		{
			$blocks[cnst::FOLDER]['header'] = [
				'include'	=> cnst::TPL . 'header.html',
				'var'		=> $this->var['months'],
			];
		}

		$event['blocks'] = $blocks;
	}

	public function core_twig_environment_render_template_before(event $event):void
	{
		if (!isset($this->var))
		{
			return;
		}

		error_log(json_encode($this->var));

		$context = $event['context'];
		$context['marttiphpbb_calendarinlineview'] = $this->var['render'];
		$event['context'] = $context;
	}
}
