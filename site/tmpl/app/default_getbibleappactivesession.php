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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

?>
<div class="uk-child-width-1-2@s uk-text-center uk-grid-match" uk-grid>
	<div>
		<div class="uk-card uk-card-default uk-card-body">
			<?php if (!empty($this->linker['name'])): ?>
				<h4><?php echo Text::_('COM_GETBIBLE_YOUR_ACTIVE_PERSISTENT_SESSION'); ?></h4>
			<?php else: ?>
				<h4><?php echo Text::_('COM_GETBIBLE_ACTIVE_PERSISTENT_SESSION'); ?></h4>
			<?php endif; ?>
			<?php echo JLayoutHelper::render('inputbox', ['id' => 'getbible-settings-session-name', 'label' => Text::_('COM_GETBIBLE_NAME'), 'class_other' => 'uk-text-center', 'value' => $this->linker['name'] ?? 'Default Name']); ?>
			<?php echo JLayoutHelper::render('inputbox', ['id' => 'getbible-settings-session-linker', 'label' => Text::_('COM_GETBIBLE_PERSISTENT_SESSION_KEY'), 'class_other' => 'getbible-linker-guid-input uk-text-center', 'value' => $this->linker['guid']]); ?>
			<button id="getbible-settings-session-copy"  class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_YOU_CAN_SHARE_YOUR_SESSION_WITH_LOVED_ONES_SO_THEY_CAN_SEE_YOUR_NOTES_AND_TAGS'); ?>"><?php echo Text::_('COM_GETBIBLE_SHARE_YOUR_SESSION'); ?></button>
			<button id="getbible-settings-session-name-update"  class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_YOU_CAN_CHANGE_YOUR_SESSION_NAME_TO_SOMETHING_MORE_RECOGNIZABLE'); ?>"><?php echo Text::_('COM_GETBIBLE_UPDATE_PERSISTENT_SESSION_NAME'); ?></button>
			<p class="uk-text-muted"><?php echo Text::_('COM_GETBIBLE_TO_USE_A_DIFFERENT_PERSISTENT_SESSION_KEY_SIMPLY_ADD_IT_ABOVE_AND_CLICK_THE_BUTTON_BELOW'); ?></p>
			<button id="getbible-settings-session-load" class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom"><?php echo Text::_('COM_GETBIBLE_LOAD_PREVIOUS_PERSISTENT_SESSION'); ?></button>
		</div>
	</div>
	<div>
		<div class="uk-child-width-1-2@s uk-text-center uk-grid-match" uk-grid>
			<div>
				<div class="uk-card uk-card-default uk-card-body">
					<h4><?php echo Text::_('COM_GETBIBLE_HOW_THIS_ALL_WORKS'); ?></h4>
					<p><?php echo Text::_('COM_GETBIBLE_YOUR_PERSISTENT_SESSION_KEY_TOGETHER_WITH_YOUR_FAVOURITE_VERSE_AUTHENTICATES_YOU_IT_LINKS_TO_ALL_YOUR_SPAN_CLASSGETBIBLEACTIVITYNOTESANDTAGSNOTES_AND_TAGSSPAN_IN_THE_BIBLE_YOU_CAN_SHARE_IT_WITH_LOVED_ONES_SO_THEY_CAN_SEE_YOUR_SPAN_CLASSGETBIBLEACTIVITYNOTESANDTAGSNOTES_AND_TAGSSPAN'); ?></p>
					<p><?php echo Text::_('COM_GETBIBLE_HOWEVER_TO_MODIFY_YOUR_SPAN_CLASSGETBIBLEACTIVITYNOTESANDTAGSNOTES_AND_TAGSSPAN_YOU_NEED_BOTH_THE_PERSISTENT_SESSION_KEY_AND_YOUR_FAVOURITE_VERSE'); ?></p>
					<a href="#" id="getbible-session-status-switch" uk-icon="icon: lock; ratio: 5" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_ENABLE_EXCLUSIVE_ACCESS_TO_EDIT_YOUR_NOTES_AND_TAGS'); ?>"></a>
				</div>
			</div>
			<div>
				<div class="uk-card uk-card-default uk-card-body">
					<h4><?php echo Text::_('COM_GETBIBLE_PLEASE_KEEP_YOUR_FAVOURITE_VERSE_PRIVATE'); ?></h4>
					<p><?php echo Text::_('COM_GETBIBLE_YOUR_PERSISTENT_SESSION_KEY_AND_FAVOURITE_VERSE_PROVIDE_YOU_EXCLUSIVE_ACCESS_TO_EDIT_YOUR_SPAN_CLASSGETBIBLEACTIVITYNOTESANDTAGSNOTES_AND_TAGSSPAN_THINK_OF_YOUR_PERSISTENT_SESSION_KEY_AS_A_USERNAME_AND_YOUR_FAVOURITE_VERSE_AS_A_PASSWORD_THEREFORE_ENSURE_YOUR_FAVOURITE_VERSE_IS_KEPT_PRIVATE'); ?></p>
					<p><?php echo Text::_('COM_GETBIBLE_THE_PERSISTENT_SESSION_KEY_ALLOWS_VIEWING_WHILE_EDITING_IS_ONLY_POSSIBLE_WHEN_THE_CORRECT_FAVOURITE_VERSE_IS_PROVIDED'); ?></p>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
document.getElementById('getbible-settings-session-copy').onclick = async function () {
	let linkerGuid = getLocalMemory('getbible_active_linker_guid', null);
	copyGetBiblePersistentSessionUrl(linkerGuid);
}
document.getElementById('getbible-settings-session-name-update').onclick = async function () {
	let linkerName = document.getElementById('getbible-settings-session-name').value;
	if (linkerName) {
		setLinkerName(linkerName);
	}
}
document.getElementById('getbible-settings-session-load').onclick = function() {
	let linkerGuid = document.getElementById('getbible-settings-session-linker').value;
	loadGetBiblePersistentSessionLinker(linkerGuid);
}
const copyGetBiblePersistentSessionUrl = async (linkerGuid) => {
	if (linkerGuid === null) {
		// Show message
		UIkit.notification({
			message: '<?php echo Text::_('COM_GETBIBLE_THERE_WAS_AN_ERROR_PLEASE_RELOAD_YOUR_PAGE_AND_TRY_AGAIN'); ?>',
			status: 'danger',
			timeout: 4000
		});
	} else {
		const shareHisWord = await setShareHisWordUrl(
			linkerGuid, getbible_active_translation, getbible_book_nr, getbible_chapter_nr);
		if (shareHisWord.url) {
			try {
				await navigator.clipboard.writeText(shareHisWord.url);
				UIkit.notification({
					message: '<?php echo Text::_('COM_GETBIBLE_THE_LINK_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
					status: 'success',
					timeout: 5000
				});
			} catch (err) {
				// Fallback for browsers that do not support clipboard API
				const textarea = document.createElement("textarea");
				textarea.textContent = shareHisWord.url;
				document.body.appendChild(textarea);
				textarea.select();
				try {
					document.execCommand("copy");
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
		} else if (shareHisWord.error) {
			// Show message
			UIkit.notification({
				message: shareHisWord.error,
				status: 'danger',
				timeout: 5000
			});
		} else {
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THERE_WAS_AN_ERROR_PLEASE_RELOAD_YOUR_PAGE_AND_TRY_AGAIN'); ?>',
				status: 'danger',
				timeout: 4000
			});
		}
	}
}
const loadGetBiblePersistentSessionLinker = async (linkerGuid) => {
	let oldLinkerGuid = getLocalMemory('getbible_active_linker_guid', null);
	// make sure there was a change
	if (oldLinkerGuid === linkerGuid) {
		// Show message
		UIkit.notification({
			message: '<?php echo Text::_('COM_GETBIBLE_THIS_PERSISTENT_SESSION_IS_ALREADY_ACTIVE'); ?>',
			status: 'success',
			timeout: 4000
		});
	} else {
		UIkit.modal.confirm('<?php echo Text::_('COM_GETBIBLE_YOU_ARE_ABOUT_TO_LOAD_ANOTHER_PERSISTENT_SESSION_KEY_ARE_YOU_SURE_YOU_WOULD_LIKE_TO_CONTINUE'); ?>').then( async function() {
			const response = await checkValidLinker(linkerGuid, oldLinkerGuid);
			if (response.success) {
				// Show message
				UIkit.notification({
					message: response.success,
					status: 'success',
					timeout: 2000
				});
				setLinker(linkerGuid).then((data) => {
					if (data.success) {
						setActiveLinkerOnPage(linkerGuid);
						setLocalMemory('getbible_active_linker_guid', null);
						setTimeout(function() {
							location.assign(getbible_page_url);
						}, 4000);
					} else if (data.error) {
						// Show message
						UIkit.notification({
							message: data.error,
							status: 'danger',
							timeout: 4000
						});
					} 
				});
			} else if (response.error) {
				// Show message
				UIkit.notification({
					message: response.error,
					status: 'danger',
					timeout: 4000
				});
			} else {
				// Show message
				UIkit.notification({
					message: '<?php echo Text::_('COM_GETBIBLE_THERE_WAS_AN_ERROR_PLEASE_RELOAD_YOUR_PAGE_AND_TRY_AGAIN'); ?>',
					status: 'danger',
					timeout: 4000
				});
			}
		}, function () {
			console.log('Change of persistent session key cancelled.')
		});
	}
}
const removeGetBiblePersistentSession = async (linkerGuid) => {
	let LinkerDisplay = document.getElementById('getbible-local-linker-display-' + linkerGuid);
	if (LinkerDisplay) {
		let activeLinkerGuid = getLocalMemory('getbible_active_linker_guid', null);
		if (activeLinkerGuid && activeLinkerGuid === linkerGuid) {
			// Show message
			UIkit.modal.confirm('<?php echo Text::_('COM_GETBIBLE_YOU_ARE_ABOUT_TO_REMOVE_THE_ACTIVE_PERSISTENT_SESSION'); ?>').then(async function() {
				// remove from local list
				await revokeLinkerAccess(linkerGuid);
				await revokeLinkerSession(linkerGuid);
				await linkerManager.remove(linkerGuid);
				LinkerDisplay.remove();
				setLocalMemory(linkerGuid + '-validated', false);
				setLocalMemory('getbible_active_linker_guid', null);
				// Show message
				UIkit.notification({
					message: '<?php echo Text::_('COM_GETBIBLE_ACTIVE_PERSISTENT_SESSION_WAS_REMOVED'); ?>',
					status: 'success',
					timeout: 4000
				});
				setTimeout(function() {
					location.assign(getbible_page_url);
				}, 4100);
			}, function() {
				// Show message
				UIkit.notification({
					message: '<?php echo Text::_('COM_GETBIBLE_ACTIVE_PERSISTENT_SESSION_WAS_NOT_REMOVED'); ?>',
					status: 'primary',
					timeout: 4000
				});
			});
		} else {
			// remove from local list
			await linkerManager.remove(linkerGuid);
			LinkerDisplay.remove();
		}
	}
}
</script>
