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

$url = $displayData ? JRoute::_('index.php?option=com_getbible&view=app&t=' . $displayData->abbreviation . '&ref=' . $displayData->name . '&c=' . $displayData->chapter) : null;

?>
<?php if ($url): ?>
	<a class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom" href="<?php echo $url; ?>" title="<?php echo JText::_('COM_GETBIBLE_NEXT'); ?>" uk-icon="icon: chevron-down"></a>
<?php endif; ?>
