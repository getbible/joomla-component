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
<?php if ($displayData->state->get('filter.published') == -2 && ($displayData->canState && $displayData->canDelete)) : ?>
	<script>
		// change the class of the delete button
		jQuery("#toolbar-delete button").toggleClass("btn-danger");
		// function to empty the trash
		function emptyTrash() {
			if (document.adminForm.boxchecked.value == 0) {
				// select all the items visable
				document.adminForm.elements['checkall-toggle'].checked=1;
				Joomla.checkAll(document.adminForm.elements['checkall-toggle']);
				// check to confirm the deletion
				if(confirm('<?= JText::_("COM_GETBIBLE_ARE_YOU_SURE_YOU_WANT_TO_DELETE_CONFIRMING_WILL_PERMANENTLY_DELETE_THE_SELECTED_ITEMS") ?>')) {
					Joomla.submitbutton('<?= $displayData->get("name") ?>.delete');
				} else {
					document.adminForm.elements['checkall-toggle'].checked=0;
					Joomla.checkAll(document.adminForm.elements['checkall-toggle']);
				}
			} else {
				// confirm deletion of those selected
				if (confirm('<?= JText::_("COM_GETBIBLE_ARE_YOU_SURE_YOU_WANT_TO_DELETE_CONFIRMING_WILL_PERMANENTLY_DELETE_THE_SELECTED_ITEMS") ?>')) {
					Joomla.submitbutton('<?= $displayData->get("name") ?>.delete');
				};
			}
			return false;
		}
		// function to exit the tash state
		function exitTrash() {
			document.adminForm.filter_published.selectedIndex = 0;
			document.adminForm.submit();
			return false;
		}
	</script>
	<div class="alert alert-error">
		<?php if (empty($displayData->items)): ?>
			<h4 class="alert-heading">
				<span class="icon-trash"></span>
				<?= JText::_("COM_GETBIBLE_TRASH_AREA") ?>
			</h4>
			<p><?= JText::_("COM_GETBIBLE_YOU_ARE_CURRENTLY_VIEWING_THE_TRASH_AREA_AND_YOU_DONT_HAVE_ANY_ITEMS_IN_TRASH_AT_THE_MOMENT") ?></p>
		<?php else: ?>
			<h4 class="alert-heading">
				<span class="icon-trash"></span>
				<?= JText::_("COM_GETBIBLE_TRASHED_ITEMS") ?>
			</h4>
			<p><?= JText::_("COM_GETBIBLE_YOU_ARE_CURRENTLY_VIEWING_THE_TRASHED_ITEMS") ?></p>
			<button onclick="emptyTrash();" class="btn btn-small btn-danger">
				<span class="icon-delete" aria-hidden="true"></span>
				<?= JText::_("COM_GETBIBLE_EMPTY_TRASH") ?>
			</button>
		<?php endif; ?>
		<button onclick="exitTrash();" class="btn btn-small">
			<span class="icon-back" aria-hidden="true"></span>
			<?= JText::_("COM_GETBIBLE_EXIT_TRASH") ?>
		</button>
	</div>
<?php endif; ?>
