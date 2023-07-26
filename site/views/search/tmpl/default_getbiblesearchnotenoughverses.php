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
<?php if (!$this->enoughVerses): ?>
	<div class="uk-alert-warning" uk-alert>
		<p><?php echo JText::sprintf("COM_GETBIBLE_THERE_IS_NOT_ENOUGH_VERSES_BEEN_INDEXED_FOR_BSB_TRANSLATION_OF_THE_BIBLE_THIS_MEANS_YOUR_SEARCH_RESULTS_WILL_NOT_BE_ACCURATE_PLEASE_CONTACT_THE_SYSTEM_ADMINISTRATOR_OF_THIS_WEBSITE_TO_RESOLVE_THIS", $this->translation->translation); ?></p>
	</div>
<?php endif; ?>
