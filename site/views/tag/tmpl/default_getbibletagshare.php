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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

?>
<div id="getbible-tag-sharing" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-margin uk-margin-remove-top">
			<h3 class="uk-modal-title uk-margin-remove"><?php echo $this->tag->name; ?> <span uk-icon="icon: forward; ratio: 1.5"></span></h3>
			<span class="uk-text-small uk-text-muted uk-margin-remove"><?php echo Text::_('COM_GETBIBLE_SHARING_THE_WORD_OF_GOD_WITH_THE_WORLD'); ?></span>
		</div>
		<div>
			<div id="getbible-link-share-url" class="uk-box-shadow-small uk-padding uk-margin uk-text-small uk-text-nowrap"><?php echo $this->getCanonicalUrl(); ?></div>
				<button id="copy-share-getbible-link" class="uk-button  uk-width-1-1 uk-button-default"><?php echo Text::_('COM_GETBIBLE_COPY'); ?></button>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
document.getElementById('copy-share-getbible-link').onclick = function() {
	var textToCopy = document.getElementById('getbible-link-share-url').textContent;
	try {
		navigator.clipboard.writeText(textToCopy).then(function() {
			// close the modal
			UIkit.modal('#getbible-tag-sharing').hide();
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THE_LINK_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
				status: 'success',
				timeout: 5000
			});
		}, function(err) {
			console.error('Could not copy text: ', err);
		});
	} catch (err) {
		// Fallback for browsers that do not support clipboard API
		const textarea = document.createElement("textarea");
		textarea.textContent = textToCopy;
		document.body.appendChild(textarea);
		textarea.select();
		try {
			document.execCommand("copy");
			// close the modal
			UIkit.modal('#getbible-tag-sharing').hide();
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THE_LINK_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
				status: 'success',
				timeout: 5000
			});
		} catch (err) {
			console.error('Failed to copy: ', err);
		} finally {
			document.body.removeChild(textarea);
		}
	}
}
</script>
