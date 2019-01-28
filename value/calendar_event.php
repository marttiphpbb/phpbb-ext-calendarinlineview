<?php
/**
* phpBB Extension - marttiphpbb calendarinlineview
* @copyright (c) 2019 marttiphpbb <info@martti.be>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace marttiphpbb\calendarinlineview\value;

use marttiphpbb\calendarinlineview\value\topic;
use marttiphpbb\calendarinlineview\value\dayspan;

class calendar_event extends dayspan
{
	protected $topic;

	public function __construct(
		int $start_jd,
		int $end_jd,
		topic $topic
	)
	{
		parent::__construct($start_jd, $end_jd);
		$this->topic = $topic;
	}

	public function get_topic():topic
	{
		return $this->topic;
	}
}
