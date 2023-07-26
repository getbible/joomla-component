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


?>

<?php if ($this->params->get('activate_search') == 1): ?>
	<?php echo $this->loadTemplate('getbiblesearch'); ?>
<?php else: ?>
	<div class="uk-alert-danger" uk-alert>
		<p><?php echo JText::_("COM_GETBIBLE_THE_SEARCH_FEATURE_HAS_NOT_BEEN_ACTIVATED_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR_OF_THIS_WEBSITE_TO_RESOLVE_THIS"); ?></p>
	</div>
<?php endif; ?>
