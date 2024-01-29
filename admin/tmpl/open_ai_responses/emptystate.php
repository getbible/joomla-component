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

use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die;

$displayData = [
	'textPrefix' => 'COM_GETBIBLE_OPEN_AI_RESPONSES',
	'formURL'    => 'index.php?option=com_getbible&view=open_ai_responses',
	'icon'       => 'icon-reply',
];

if ($this->user->authorise('open_ai_response.create', 'com_getbible'))
{
	$displayData['createURL'] = 'index.php?option=com_getbible&task=open_ai_response.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
