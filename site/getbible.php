<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// register this component namespace
spl_autoload_register(function ($class) {
	// project-specific base directories and namespace prefix
	$search = [
		'libraries/jcb_powers/VDM.Joomla.GetBible' => 'VDM\\Joomla\\GetBible',
		'libraries/jcb_powers/VDM.Joomla.Openai' => 'VDM\\Joomla\\Openai',
		'libraries/jcb_powers/VDM.Joomla.Gitea' => 'VDM\\Joomla\\Gitea',
		'libraries/jcb_powers/VDM.Joomla' => 'VDM\\Joomla'
	];
	// Start the search and load if found
	$found = false;
	$found_base_dir = "";
	$found_len = 0;
	foreach ($search as $base_dir => $prefix)
	{
		// does the class use the namespace prefix?
		$len = strlen($prefix);
		if (strncmp($prefix, $class, $len) === 0)
		{
			// we have a match so load the values
			$found = true;
			$found_base_dir = $base_dir;
			$found_len = $len;
			// done here
			break;
		}
	}
	// check if we found a match
	if (!$found)
	{
		// not found so move to the next registered autoloader
		return;
	}
	// get the relative class name
	$relative_class = substr($class, $found_len);
	// replace the namespace prefix with the base directory, replace namespace
	// separators with directory separators in the relative class name, append
	// with .php
	$file = JPATH_ROOT . '/' . $found_base_dir . '/src' . str_replace('\\', '/', $relative_class) . '.php';
	// if the file exists, require it
	if (file_exists($file))
	{
		require $file;
	}
});



// Set the component css/js
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_getbible/assets/css/site.css');
$document->addScript('components/com_getbible/assets/js/site.js');

// Require helper files
JLoader::register('GetbibleHelper', __DIR__ . '/helpers/getbible.php');
JLoader::register('GetbibleHelperRoute', __DIR__ . '/helpers/route.php');

// Get an instance of the controller prefixed by Getbible
$controller = JControllerLegacy::getInstance('Getbible');

// Perform the request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();