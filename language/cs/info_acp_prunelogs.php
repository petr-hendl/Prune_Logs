<?php
/**
*
* @package Prune Log's
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'PRUNE_LOGS_DAYS' 			=> 'Smazat staré logy',
	'PRUNE_LOGS_DAYS_EXPLAIN'	=> 'Kolik dní mají být logy zachovány, než budou smazány. Pokud nastavíte 0, žádné logy se mazat nebudou.',

	'LOG_PRUNE_LOGS'			=> '<strong>Mazání starých logů</strong><br />» %s',
	'NO_PRUNE_LOGS'				=> '<strong>Mazání starých logů</strong><br />» Žádné logy ke smazání',
	'PRUNE_LOGS_NOTICE'			=> '<div class="phpinfo"><p class="entry">Nastavení promazávání je v %1$s » %2$s » %3$s.</p></div>',
));
