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
	'textPrefix' => 'COM_GETBIBLE_CHAPTERS',
	'formURL'    => 'index.php?option=com_getbible&view=chapters',
	'icon'       => 'icon-generic',
];

if ($this->user->authorise('chapter.create', 'com_getbible'))
{
	$displayData['createURL'] = 'index.php?option=com_getbible&task=chapter.add';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
