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
			case 'placement_index':
				$this->tpl_name = 'placement_index';
				$this->page_title = $language->lang(cnst::L_ACP . '_PLACEMENT_INDEX');

				if ($request->is_set_post('submit'))
				{
					if (!check_form_key(cnst::FOLDER))
					{
						trigger_error('FORM_INVALID');
					}

					$overallpageblocks_acp->process_form(cnst::FOLDER, 'index');

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
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
					$store->set_show_isoweek($request->variable('show_isoweek', 0) ? true : false);
					$store->set_show_moon_phase($request->variable('show_moon_phase', 0) ? true : false);
					$store->set_days_num($request->variable('days_num', 0));
					$store->set_derive_user_time_format($request->variable('derive_user_time_format', 0) ? true : false);
					$store->set_default_time_format($request->variable('default_time_format', ''));
					$store->set_min_rows($request->variable('min_rows', 0));
					$store->set_max_rows($request->variable('max_rows', 0));
					$store->set_load_stylesheet($request->variable('load_stylesheet', 0) ? true : false);
					$store->set_extra_stylesheet($request->variable('extra_stylesheet', ''));
					$store->transaction_end();

					trigger_error($language->lang(cnst::L_ACP . '_SETTINGS_SAVED') . adm_back_link($this->u_action));
				}

				$template->assign_vars([
					'SHOW_ISOWEEK'				=> $store->get_show_isoweek(),
					'SHOW_MOON_PHASE'			=> $store->get_show_moon_phase(),
					'DAYS_NUM'					=> $store->get_days_num(),
					'DERIVE_USER_TIME_FORMAT'	=> $store->get_derive_user_time_format(),
					'DEFAULT_TIME_FORMAT'		=> $store->get_default_time_format(),
					'MIN_ROWS'					=> $store->get_min_rows(),
					'MAX_ROWS'					=> $store->get_max_rows(),
					'LOAD_STYLESHEET'			=> $store->get_load_stylesheet(),
					'EXTRA_STYLESHEET'			=> $store->get_extra_stylesheet(),
				]);

			break;
		}

		$overallpageblocks_acp->assign_to_template(cnst::FOLDER);
		$template->assign_var('U_ACTION', $this->u_action);
	}
}
