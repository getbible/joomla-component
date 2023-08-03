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
defined('JPATH_BASE') or die('Restricted access');



?>
<a class="getbible-verse-note-link uk-link-muted"  href="#" uk-toggle="target: #getbible-app-notes" onclick="setActiveVerse(<?php echo $displayData['number']; ?>);" uk-tooltip="<?php echo JText::_('COM_GETBIBLE_EDIT_NOTE'); ?>"><span uk-icon="file-edit"></span></a>