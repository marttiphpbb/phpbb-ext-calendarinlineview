<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\event;

use phpbb\event\data as event;
use phpbb\auth\auth;
use marttiphpbb\calendarinlineview\service\render;
use marttiphpbb\calendarinlineview\service\store;
use marttiphpbb\calendarinlineview\util\cnst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $auth;
	protected $render;
	protected $store;
	protected $var;
	protected $forum_id;
	protected $page;

	public function __construct(
		auth $auth,
		render $render,
		store $store
	)
	{
		$this->auth = $auth;
		$this->render = $render;
		$this->store = $store;
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
		$this->page = 'index';
	}

	public function core_viewforum_modify_page_title(event $event):void
	{
		$this->forum_id = $event['forum_id'];
		$this->page = 'viewforum';
	}

	public function core_viewtopic_modify_page_title(event $event):void
	{
		$this->forum_id = $event['forum_id'];
		$this->page = 'viewtopic';
	}

	public function core_posting_modify_template_vars(event $event):void
	{
		$this->forum_id = $event['forum_id'];
		$this->page = 'posting';
	}

	public function add_blocks(event $event):void
	{
		if (!isset($this->page))
		{
			return;
		}

		if (!count($this->auth->acl_getf('f_read')))
		{
			return;
		}

		$blocks = $event['blocks'];
		$template_events = $event['template_events'];

		if (!isset($template_events[cnst::FOLDER]))
		{
			return;
		}

		switch ($this->page)
		{
			case 'viewforum':
				if (!$this->store->get_forums_viewforum_en($this->forum_id))
				{
					return;
				}
			break;

			case 'viewtopic':
				if (!$this->store->get_forums_viewtopic_en($this->forum_id))
				{
					return;
				}
			break;

			case 'posting':
				if (!$this->store->get_forums_posting_en($this->forum_id))
				{
					return;
				}
			break;

			default:
			break;
		}

		switch($this->page)
		{
			case 'index':
				$this->render->add_lang();
				$this->var = $this->render->get_var(
					$this->store->get_index_days_num(),
					$this->store->get_index_min_rows(),
					$this->store->get_index_max_rows(),
					-1
				);
				$days_template = $this->store->get_index_template();
				$days_key = 'index_days';
				$months_key = 'index_months';

			break;

			case 'viewforum':
			case 'viewtopic':
			case 'posting':
				$this->render->add_lang();
				$this->var = $this->render->get_var(
					$this->store->get_forums_days_num(),
					$this->store->get_forums_min_rows(),
					$this->store->get_forums_max_rows(),
					$this->store->get_forums_local_events() ? $this->forum_id : -1
				);
				$days_template = $this->store->get_forums_template();
				$days_key = 'forums_days';
				$months_key = 'forums_months';

			break;

			default:
				return;
			break;
		}

		error_log('tpl: ' . $days_template);


		if (isset($template_events[cnst::FOLDER][$days_key]))
		{
			$blocks[cnst::FOLDER][$days_key] = [
				'include'	=> cnst::TPL . 'days/' . $days_template . '.html',
				'var'		=> $this->var,
			];
		}

		if (isset($template_events[cnst::FOLDER][$months_key]))
		{
			$blocks[cnst::FOLDER][$months_key] = [
				'include'	=> cnst::TPL . 'months.html',
				'var'		=> $this->var,
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

		$context = $event['context'];

		$context['marttiphpbb_calendarinlineview'] = [
			'load_stylesheet'	=> $this->store->get_load_stylesheet(),
			'extra_stylesheet'	=> $this->store->get_extra_stylesheet(),
		];

		$event['context'] = $context;
	}
}
