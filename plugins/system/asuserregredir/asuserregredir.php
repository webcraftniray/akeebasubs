<?php
/**
 * @package		akeebasubs
 * @copyright	Copyright (c)2010-2015 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die();

JLoader::import('joomla.plugin.plugin');

// PHP version check
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}
// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.3.0', '>=')) return;

// Make sure F0F is loaded, otherwise do not run
if(!defined('F0F_INCLUDED')) {
	include_once JPATH_LIBRARIES.'/f0f/include.php';
}
if(!defined('F0F_INCLUDED') || !class_exists('F0FLess', true))
{
	return;
}

// Do not run if Akeeba Subscriptions is not enabled
JLoader::import('joomla.application.component.helper');
if(!JComponentHelper::isEnabled('com_akeebasubs', true)) return;

class plgSystemAsuserregredir extends JPlugin
{
	/**
	 * Public constructor. Overridden to load the language strings.
	 */
	public function __construct(& $subject, $config = array())
	{
		if(!is_object($config['params'])) {
			JLoader::import('joomla.registry.registry');
			$config['params'] = new JRegistry($config['params']);
		}

		parent::__construct($subject, $config);

		// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
		if(function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set')) {
			if(function_exists('error_reporting')) {
				$oldLevel = error_reporting(0);
			}
			$serverTimezone = @date_default_timezone_get();
			if(empty($serverTimezone) || !is_string($serverTimezone)) $serverTimezone = 'UTC';
			if(function_exists('error_reporting')) {
				error_reporting($oldLevel);
			}
			@date_default_timezone_set( $serverTimezone);
		}
	}

	public function onAfterRoute()
	{
		// Only run in the front-end
		if (!JFactory::getApplication()->isSite())
		{
			return;
		}

		$input	= JFactory::getApplication()->input;
		$option	= $input->getCmd('option');
		$view	= $input->getCmd('view');

		// Only run on user registration task
		if (($option != 'com_users') || ($view != 'registration'))
		{
			return;
		}

		$default_url = JRoute::_('index.php?option=com_akeebasubs');

		$url		= $this->params->get('url', $default_url);
		$message	= $this->params->get('message', '');

		$url = trim($url);
		$message = trim($message);

		if (empty($url))
		{
			$url = $default_url;
		}

		if (empty($message))
		{
			JFactory::getApplication()->redirect($url);
		}
		else
		{
			JFactory::getApplication()->redirect($url, $message);
		}
	}
}