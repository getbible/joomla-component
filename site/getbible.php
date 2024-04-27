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

// The power autoloader for this project (JPATH_SITE) area.
$power_autoloader = JPATH_SITE . '/components/com_getbible/helpers/powerloader.php';
if (file_exists($power_autoloader))
{
	require_once $power_autoloader;
}

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\MVC\Controller\BaseController;

// Set the component css/js
Html::_('stylesheet', 'components/com_getbible/assets/css/site.css', ['version' => 'auto']);
Html::_('script', 'components/com_getbible/assets/js/site.js', ['version' => 'auto']);

// Require helper files
JLoader::register('GetbibleHelper', __DIR__ . '/helpers/getbible.php');
JLoader::register('GetbibleHelperRoute', __DIR__ . '/helpers/route.php');

// Get an instance of the controller prefixed by Getbible
$controller = BaseController::getInstance('Getbible');

// Perform the request task
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
