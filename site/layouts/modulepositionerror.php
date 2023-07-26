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
<div class="uk-alert uk-alert-large">
	<h2><?php echo JText::_('COM_GETBIBLE_MODULE_POSITION'); ?></h2>
	<p><?php echo JText::sprintf('COM_GETBIBLE_PLEASE_PUBLISH_A_MODULE_TO_THIS_CODESCODE_POSITION_AND_INSURE_THAT_YOU_TARGET_THIS_PAGE_S_USING_A_BUNIQUE_MODULE_POSITION_NAMEB_IT_SHOULD_BE_POSSIBLE_TO_TARGET_ALL_PAGES', $displayData['position'],  $displayData['page'] ?? 'target'); ?></p>
</div>
