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
defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_GETBIBLE_OPEN_AI_MESSAGES',
	'formURL'    => 'index.php?option=com_getbible&view=open_ai_messages',
	'icon'       => 'icon-comment',
];

if ($this->user->authorise('open_ai_message.create', 'com_getbible'))
{
	$displayData['createURL'] = 'index.php?option=com_getbible&task=open_ai_message.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
