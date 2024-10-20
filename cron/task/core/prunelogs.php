<?php
/**
*
* @package Prune Log's
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\prunelogs\cron\task\core;

use phpbb\config\config;
use phpbb\user;
use phpbb\db\driver\driver_interface;
use phpbb\log\log;

class prunelogs extends \phpbb\cron\task\base
{
	protected $config;
	protected $user;
	protected $db;
	protected $log;

	/**
	* Constructor.
	*
	* @param phpbb_config $config The config
	* @param phpbb_db_driver $db The db connection
	*/
	public function __construct(config $config, user $user, driver_interface $db, log $log)
	{
		$this->config = $config;
		$this->user = $user;
		$this->db = $db;
		$this->log = $log;
	}

	/**
	* Runs this cron task.
	*
	* @return null
	*/
	public function run()
	{
		$log_types = array('LOG_ADMIN', 'LOG_MOD', 'LOG_CRITICAL', 'LOG_USERS');

		$expire_date = time() - ($this->config['prune_logs_days'] * 86400);
		$log_aray = array();
		$sql = 'SELECT COUNT(log_id) AS total, log_type FROM ' . LOG_TABLE . ' WHERE log_time < ' . $expire_date . ' GROUP BY log_type ORDER BY log_type';
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$log_aray[$log_types[$row['log_type']]] = $row['total'];
		}

		if (sizeof($log_aray))
		{
			$this->user->add_lang('acp/common');
			$sql = 'DELETE FROM ' . LOG_TABLE . ' WHERE  log_time < ' . $expire_date;
			$this->db->sql_query($sql);

			$loglist = array_map(function ($v, $k)
			{
				global $user;
				return $user->lang['ACP_' . str_replace('LOG_', '', $k) . '_LOGS'] . ': ' . $v;
			},
			$log_aray, array_keys($log_aray));
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip ?? null, 'LOG_PRUNE_LOGS', false, array(implode(',<br />', $loglist)));
		} else
		{
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip ?? null, 'NO_PRUNE_LOGS', false, array());
		}
		$this->config->set('prune_logs_last_gc', time());
	}

	/**
	* Returns whether this cron task can run, given current board configuration.
	*
	* @return bool
	*/
	public function is_runnable()
	{
		return (bool) $this->config['prune_logs_days'];
	}

	/**
	* Returns whether this cron task should run now, because enough time
	* has passed since it was last run.
	*
	* @return bool
	*/
	public function should_run()
	{
		return $this->config['prune_logs_last_gc'] < time() - $this->config['prune_logs_gc'];
	}
}
