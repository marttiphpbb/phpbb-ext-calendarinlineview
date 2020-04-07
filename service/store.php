<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 - 2020 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\service;

use phpbb\config\db_text as config_text;
use phpbb\cache\driver\driver_interface as cache;
use marttiphpbb\calendarinlineview\util\cnst;

class store
{
	protected $config_text;
	protected $cache;
	protected $local_cache;
	protected $transaction = false;

	public function __construct(
		config_text $config_text,
		cache $cache
	)
	{
		$this->config_text = $config_text;
		$this->cache = $cache;
	}

	private function get_all():array
	{
		if (isset($this->local_cache) && is_array($this->local_cache))
		{
			return $this->local_cache;
		}

		$settings = $this->cache->get(cnst::CACHE_ID);

		if ($settings)
		{
			$this->local_cache = $settings;
			return $settings;
		}

		$this->local_cache = unserialize($this->config_text->get(cnst::ID));
		$this->cache->put(cnst::CACHE_ID, $this->local_cache);

		return $this->local_cache;
	}

	private function set(array $ary):void
	{
		if ($ary === $this->local_cache)
		{
			return;
		}
		$this->local_cache = $ary;

		if (!$this->transaction)
		{
			$this->write($ary);
		}
	}

	private function write(array $ary):void
	{
		$this->cache->put(cnst::CACHE_ID, $ary);
		$this->config_text->set(cnst::ID, serialize($ary));
	}

	public function transaction_start():void
	{
		$this->transaction = true;
	}

	public function transaction_end():void
	{
		$this->transaction = false;
		$this->write($this->local_cache);
	}

	private function get_array(string $name):array
	{
		return $this->get_all()[$name];
	}

	private function set_array(string $name, array $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function set_string(string $name, string $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function get_string(string $name):string
	{
		return $this->get_all()[$name];
	}

	private function set_index_string(string $name, string $value):void
	{
		$ary = $this->get_all();
		$ary['index'][$name] = $value;
		$this->set($ary);
	}

	private function get_index_string(string $name):string
	{
		return $this->get_all()['index'][$name];
	}

	private function set_forums_string(string $name, string $value):void
	{
		$ary = $this->get_all();
		$ary['forums'][$name] = $value;
		$this->set($ary);
	}

	private function get_forums_string(string $name):string
	{
		return $this->get_all()['forums'][$name];
	}

	private function set_int(string $name, int $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function get_int(string $name):int
	{
		return $this->get_all()[$name];
	}

	private function set_index_int(string $name, int $value):void
	{
		$ary = $this->get_all();
		$ary['index'][$name] = $value;
		$this->set($ary);
	}

	private function get_index_int(string $name):int
	{
		return $this->get_all()['index'][$name];
	}

	private function set_forums_int(string $name, int $value):void
	{
		$ary = $this->get_all();
		$ary['forums'][$name] = $value;
		$this->set($ary);
	}

	private function get_forums_int(string $name):int
	{
		return $this->get_all()['forums'][$name];
	}

	private function set_boolean(string $name, bool $value):void
	{
		$ary = $this->get_all();
		$ary[$name] = $value;
		$this->set($ary);
	}

	private function get_boolean(string $name):bool
	{
		return $this->get_all()[$name];
	}

	private function set_forums_boolean(string $name, bool $value):void
	{
		$ary = $this->get_all();
		$ary['forums'][$name] = $value;
		$this->set($ary);
	}

	private function get_forums_boolean(string $name):bool
	{
		return $this->get_all()['forums'][$name];
	}

	private function set_forums_int_bool_ary_item(string $name, int $key, bool $val):void
	{
		if (!$val)
		{
			return;
		}

		$ary = $this->get_all();
		$ary['forums']['en_ary'][$name][$key] = $val;
		$this->set($ary);
	}

	private function set_forums_int_bool_ary(string $name, array $new_ary):void
	{
		$ary = $this->get_all();
		$ary['forums']['en_ary'][$name] = [];
		$this->set($ary);

		foreach ($new_ary as $int_key => $bool_value)
		{
			$this->set_forums_int_bool_ary_item($name, $int_key, $bool_value);
		}
	}

	public function set_forums_viewforum_en_ary(array $viewforum_en_ary):void
	{
		$this->set_forums_int_bool_ary('viewforum', $viewforum_en_ary);
	}

	public function get_forums_viewforum_en(int $forum_id):bool
	{
		$ary = $this->get_all();
		return isset($ary['forums']['en_ary']['viewforum'][$forum_id]);
	}

	public function set_forums_viewtopic_en_ary(array $viewtopic_en_ary):void
	{
		$this->set_forums_int_bool_ary('viewtopic', $viewtopic_en_ary);
	}

	public function get_forums_viewtopic_en(int $forum_id):bool
	{
		$ary = $this->get_all();
		return isset($ary['forums']['en_ary']['viewtopic'][$forum_id]);
	}

	public function set_forums_posting_en_ary(array $posting_en_ary):void
	{
		$this->set_forums_int_bool_ary('posting', $posting_en_ary);
	}

	public function get_forums_posting_en(int $forum_id):bool
	{
		$ary = $this->get_all();
		return isset($ary['forums']['en_ary']['posting'][$forum_id]);
	}

	public function set_index_days_num(int $days_num):void
	{
		$this->set_index_int('days_num', $days_num);
	}

	public function get_index_days_num():int
	{
		return $this->get_index_int('days_num');
	}

	public function set_forums_days_num(int $days_num):void
	{
		$this->set_forums_int('days_num', $days_num);
	}

	public function get_forums_days_num():int
	{
		return $this->get_forums_int('days_num');
	}

	public function set_index_min_rows(int $min_rows):void
	{
		$this->set_index_int('min_rows', $min_rows);
	}

	public function get_index_min_rows():int
	{
		return $this->get_index_int('min_rows');
	}

	public function set_forums_min_rows(int $min_rows):void
	{
		$this->set_forums_int('min_rows', $min_rows);
	}

	public function get_forums_min_rows():int
	{
		return $this->get_forums_int('min_rows');
	}

	public function set_index_max_rows(int $max_rows):void
	{
		$this->set_index_int('max_rows', $max_rows);
	}

	public function get_index_max_rows():int
	{
		return $this->get_index_int('max_rows');
	}

	public function set_forums_max_rows(int $max_rows):void
	{
		$this->set_forums_int('max_rows', $max_rows);
	}

	public function get_forums_max_rows():int
	{
		return $this->get_forums_int('max_rows');
	}

	public function set_index_template(string $template):void
	{
		$this->set_index_string('template', $template);
	}

	public function get_index_template():string
	{
		return $this->get_index_string('template');
	}

	public function set_forums_template(string $template):void
	{
		$this->set_forums_string('template', $template);
	}

	public function get_forums_template():string
	{
		return $this->get_forums_string('template');
	}

	public function set_forums_local_events(bool $local_events):void
	{
		$this->set_forums_boolean('local_events', $local_events);
	}

	public function get_forums_local_events():bool
	{
		return $this->get_forums_boolean('local_events');
	}

	public function set_load_stylesheet(bool $load_stylesheet):void
	{
		$this->set_boolean('load_stylesheet', $load_stylesheet);
	}

	public function get_load_stylesheet():bool
	{
		return $this->get_boolean('load_stylesheet');
	}

	public function set_extra_stylesheet(string $extra_stylesheet):void
	{
		$this->set_string('extra_stylesheet', $extra_stylesheet);
	}

	public function get_extra_stylesheet():string
	{
		return $this->get_string('extra_stylesheet');
	}

	public function set_derive_user_time_format(bool $derive_user_time_format):void
	{
		$this->set_boolean('derive_user_time_format', $derive_user_time_format);
	}

	public function get_derive_user_time_format():bool
	{
		return $this->get_boolean('derive_user_time_format');
	}

	public function set_default_time_format(string $default_time_format):void
	{
		$this->set_string('default_time_format', $default_time_format);
	}

	public function get_default_time_format():string
	{
		return $this->get_string('default_time_format');
	}
}
