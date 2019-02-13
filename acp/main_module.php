<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\acp;

use marttiphpbb\calendarinlineview\util\cnst;

class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $phpbb_container;

		$template = $phpbb_container->get('template');
		$request = $phpbb_container->get('request');
		$store = $phpbb_container->get('marttiphpbb.calendarinlineview.store');
		$language = $phpbb_container->get('language');
		$ext_manager = $phpbb_container->get('ext.manager');

		$language->add_lang('acp', cnst::FOLDER);

		if (!$ext_manager->is_enabled('marttiphpbb/overallpageblocks'))
		{
			$msg = $language->lang(cnst::L_ACP . '_OVERALLPAGEBLOCKS_NOT_ENABLED',
				'<a href="https://github.com/marttiphpbb/phpbb-ext-overallpageblocks">',
				'</a>');
			trigger_error($msg, E_USER_WARNING);
		}

		$overallpageblocks_acp = $phpbb_container->get('marttiphpbb.overallpageblocks.acp');

		add_form_key(cnst::FOLDER);

		switch($mode)
		{
			case 'index':
				$this->tpl_name = 'index';
				$this->page_title = $language->lang(cnst::L_ACP . '_INDEX');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$store->transaction_start();
					$store->set_index_days_num($request->variable('index_days_num', 0));
					$store->set_index_min_rows($request->variable('index_min_rows', 0));
					$store->set_index_max_rows($request->variable('index_max_rows', 0));
					$store->set_index_template($request->variable('index_template', ''));
					$store->transaction_end();

					$overallpageblocks_acp->process_form(cnst::FOLDER, 'index_days');
					$overallpageblocks_acp->process_form(cnst::FOLDER, 'index_months');

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'DAYS_NUM'	=> $store->get_index_days_num(),
					'MIN_ROWS'	=> $store->get_index_min_rows(),
					'MAX_ROWS'	=> $store->get_index_max_rows(),
					'TEMPLATE'	=> $store->get_index_template(),
				]);

			break;

			case 'forums':
				$this->tpl_name = 'forums';
				$this->page_title = $language->lang(cnst::L_ACP . '_FORUMS');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$viewforum_en_ary =  array_fill_keys(array_keys($request->variable('viewforum_en', [0 => ''])), true);
					$viewtopic_en_ary =  array_fill_keys(array_keys($request->variable('viewtopic_en', [0 => ''])), true);
					$posting_en_ary =  array_fill_keys(array_keys($request->variable('posting_en', [0 => ''])), true);

					$store->transaction_start();
					$store->set_forums_local_events($request->variable('forums_local_events', 0) ? true : false);
					$store->set_forums_days_num($request->variable('forums_days_num', 0));
					$store->set_forums_min_rows($request->variable('forums_min_rows', 0));
					$store->set_forums_max_rows($request->variable('forums_max_rows', 0));
					$store->set_forums_template($request->variable('forums_template', ''));
					$store->set_forums_viewforum_en_ary($viewforum_en_ary);
					$store->set_forums_viewtopic_en_ary($viewtopic_en_ary);
					$store->set_forums_posting_en_ary($posting_en_ary);
					$store->transaction_end();

					$overallpageblocks_acp->process_form(cnst::FOLDER, 'forums_days');
					$overallpageblocks_acp->process_form(cnst::FOLDER, 'forums_months');

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'FORUMS_LOCAL_EVENTS'	=> $store->get_forums_local_events(),
					'DAYS_NUM'				=> $store->get_forums_days_num(),
					'MIN_ROWS'				=> $store->get_forums_min_rows(),
					'MAX_ROWS'				=> $store->get_forums_max_rows(),
					'TEMPLATE'				=> $store->get_forums_template(),
				]);

				$cforums = make_forum_select(false, false, false, false, true, false, true);

				foreach ($cforums as $forum)
				{
					$forum_id = $forum['forum_id'];

					$template->assign_block_vars('cforums', [
						'NAME'		=> $forum['padding'] . $forum['forum_name'],
						'ID'		=> $forum_id,
						'VIEWFORUM_EN'	=> $store->get_forums_viewforum_en($forum_id),
						'VIEWTOPIC_EN'	=> $store->get_forums_viewtopic_en($forum_id),
						'POSTING_EN'	=> $store->get_forums_posting_en($forum_id),
					]);
				}

			break;

			case 'rendering':

				$this->tpl_name = 'rendering';
				$this->page_title = $language->lang(cnst::L_ACP . '_RENDERING');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$store->transaction_start();
					$store->set_derive_user_time_format($request->variable('derive_user_time_format', 0) ? true : false);
					$store->set_default_time_format($request->variable('default_time_format', ''));
					$store->set_load_stylesheet($request->variable('load_stylesheet', 0) ? true : false);
					$store->set_extra_stylesheet($request->variable('extra_stylesheet', ''));
					$store->transaction_end();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'DERIVE_USER_TIME_FORMAT'	=> $store->get_derive_user_time_format(),
					'DEFAULT_TIME_FORMAT'		=> $store->get_default_time_format(),
					'LOAD_STYLESHEET'			=> $store->get_load_stylesheet(),
					'EXTRA_STYLESHEET'			=> $store->get_extra_stylesheet(),
				]);

			break;
		}

		$overallpageblocks_acp->assign_to_template(cnst::FOLDER);
		$template->assign_var('U_ACTION', $this->u_action);
	}
}
