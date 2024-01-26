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

$edit_content = '<div class="uk-alert-success" id="getbible-edit-tag-error" uk-alert style="display:none"><p id="getbible-edit-tag-error-message"></p></div>';
$edit_content .= LayoutHelper::render('inputbox', ['id' => 'getbible-edit-tag-name', 'label' => Text::_('COM_GETBIBLE_NAME')]);
$edit_content .= LayoutHelper::render('textareabox', ['id' => 'getbible-edit-tag-description', 'label' => Text::_('COM_GETBIBLE_DESCRIPTION')]);
$edit_content .= '<input id="getbible-edit-tag-guid" type="hidden">';
$edit_content .= '<input id="getbible-edit-tag-verse" type="hidden">';
// set buttons
$edit_buttons = [
	['id' => 'getbible-save-edit-tag', 'name' => Text::_('COM_GETBIBLE_SAVE'), 'class' => 'uk-button uk-button-primary uk-width-2-4'],
	['id' => 'getbible-delete-edit-tag', 'name' => Text::_('COM_GETBIBLE_DELETE'), 'class' => 'uk-button uk-button-danger uk-width-1-4'],
	['id' => 'getbible-cancel-edit-tag', 'name' => Text::_('COM_GETBIBLE_CANCEL'), 'class' => 'uk-button uk-button-default uk-width-1-4']
];

$create_content = '<div class="uk-alert-success" id="getbible-create-tag-error" uk-alert style="display:none"><p id="getbible-create-tag-error-message"></p></div>';
$create_content .= LayoutHelper::render('inputbox', ['id' => 'getbible-create-tag-name', 'label' => Text::_('COM_GETBIBLE_NAME'), 'placeholder' => Text::_('COM_GETBIBLE_TAG_NAME')]);
$create_content .= LayoutHelper::render('textareabox', ['id' => 'getbible-create-tag-description', 'label' => Text::_('COM_GETBIBLE_DESCRIPTION'), 'placeholder' => Text::_('COM_GETBIBLE_TAG_DESCRIPTION')]);
// set buttons
$create_buttons = [
	['id' => 'getbible-create-tag', 'name' => Text::_('COM_GETBIBLE_CREATE'), 'class' => 'uk-button uk-button-primary uk-width-2-3'],
	['id' => 'getbible-cancel-create-tag', 'name' => Text::_('COM_GETBIBLE_CANCEL'), 'class' => 'uk-button uk-button-default uk-width-1-3']
];

?>
<div id="getbible-app-tags" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-margin uk-margin-remove-top">
			<h3 class="uk-modal-title uk-margin-remove"><?php echo $this->chapter->book_name; ?> <?php echo $this->chapter->chapter; ?>:<span class="active-getbible-verse">1</span> <span uk-icon="icon: tag; ratio: 1.5"></span></h3>
			<span class="uk-text-small uk-text-muted uk-margin-remove"><?php echo Text::_('COM_GETBIBLE_TAGGING_THIS_VERSE'); ?></span>
		</div>
		<div class="uk-padding uk-padding-remove-bottom">
			<div id="verse-tag-selection-slider"></div>
		</div>
		<div class="uk-margin">
			<p class="uk-text-muted uk-text-small uk-margin-remove getbible-verse-pre-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"></p>
			<p class="uk-text-emphasis uk-margin-remove getbible-verse-selected-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"><?php echo Text::_('COM_GETBIBLE_THE_ACTIVE_VERSE_SELECTED_TEXT_SHOULD_LOAD_HERE'); ?></p>
			<p class="uk-text-muted uk-text-small uk-margin-remove getbible-verse-post-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"></p>
			<div class="uk-child-width-1-2@s uk-grid-small" uk-grid>
				<div>
					<h4><?php echo Text::_('COM_GETBIBLE_ACTIVE'); ?></h4>
					<div id="getbible-active-tags" uk-sortable="group: getbible-tag-selection, handle: .uk-sortable-handle" style="max-height: 400px; overflow-y: auto;">
						<!-- Active items will be dynamically added here -->
					</div>
				</div>
				<div>
					<h4><?php echo Text::_('COM_GETBIBLE_AVAILABLE_TAGS'); ?> <a  id="getbible-create-new-tag" href="#" uk-icon="plus" uk-tooltip="title: <?php echo Text::_('COM_GETBIBLE_CREATE_TAG'); ?>"></a></h4>
					<div id="getbible-tags" class="uk-width-auto uk-grid-small"  uk-sortable="group: getbible-tag-selection, handle: .uk-sortable-handle" style="max-height: 400px; overflow-y: auto;" uk-grid>
						<!-- All items will be dynamically added here -->
					</div>
				</div>
			</div>
			<div class="uk-child-width-1-2@s uk-grid-small" uk-grid>
				<div>
					<p class="uk-text-small uk-text-muted uk-margin-remove"><?php echo Text::_('COM_GETBIBLE_DRAG_AND_DROP_THE_DESIRED_TAG_FROM_THE_AVAILABLE_ONES_TO_THE_ACTIVE_AREA'); ?></p>
				</div>
				<div>
					<p class="uk-text-small uk-text-muted uk-margin-remove"><?php echo Text::_('COM_GETBIBLE_TO_UNTAG_A_VERSE_DRAG_AND_DROP_THE_DESIRED_TAG_FROM_ACTIVE_TO_THE_AVAILABLE_TAGS_AREA'); ?></p>
				</div>
			</div>
		</div>
		<?php $this->modalState->main = 'tags'; ?>
		<?php $this->modalState->one = 'notes'; ?>
		<?php $this->modalState->oneText = Text::_('COM_GETBIBLE_NOTES'); ?>
		<?php $this->modalState->two = 'sharing'; ?>
		<?php $this->modalState->twoText = Text::_('COM_GETBIBLE_SHARE'); ?>
		<?php echo $this->loadTemplate('getbibleappmodalbottom'); ?>
	</div>
</div>
<?php echo LayoutHelper::render('modal', [
	'id' => 'getbible-tag-editor',
	'header' => Text::_('COM_GETBIBLE_EDIT_TAG'),
	'header_class_other' => 'uk-text-center',
	'close' => true,
	'content' => $edit_content,
	'buttons_class' => 'uk-button-group uk-width-1-1',
	'buttons_id' => 'getbible-tag-edit-buttons',
	'buttons' => $edit_buttons
]); ?>
<?php echo LayoutHelper::render('modal', [
	'id' => 'getbible-tag-creator',
	'header' => Text::_('COM_GETBIBLE_CREATE_TAG'),
	'header_class_other' => 'uk-text-center',
	'close' => true,
	'content' => $create_content,
	'buttons_class' => 'uk-button-group uk-width-1-1',
	'buttons_id' => 'getbible-tag-create-buttons',
	'buttons' => $create_buttons
]); ?>
<script type="text/javascript">
// track the scroller of the tags
new ScrollMemory('getbible-tags');
// set the edit module handles
const getbibleCreateNewTag = document.getElementById('getbible-create-new-tag');
const getbibleCreateTagError = document.getElementById('getbible-create-tag-error');
const getbibleCreateTagErrorMessage = document.getElementById('getbible-create-tag-error-message');
const getbibleCreateTagName= document.getElementById('getbible-create-tag-name');
const getbibleCreateTagDescription = document.getElementById('getbible-create-tag-description');
const getbibleCreateTag = document.getElementById('getbible-create-tag');
const getbibleCanselCreateTag = document.getElementById('getbible-cancel-create-tag');
const getbibleEditTagError = document.getElementById('getbible-edit-tag-error');
const getbibleEditTagErrorMessage = document.getElementById('getbible-edit-tag-error-message');
const getbibleEditTagName= document.getElementById('getbible-edit-tag-name');
const getbibleEditTagDescription = document.getElementById('getbible-edit-tag-description');
const getbibleEditTagGuid = document.getElementById('getbible-edit-tag-guid');
const getbibleEditTagRefeshVerse = document.getElementById('getbible-edit-tag-verse');
const getbibleSaveEditTag = document.getElementById('getbible-save-edit-tag');
const getbibleDeleteEditTag = document.getElementById('getbible-delete-edit-tag');
const getbibleCanselEditTag = document.getElementById('getbible-cancel-edit-tag');
// update tag button click events
getbibleCreateNewTag.onclick = async function () {
	try {
		getbibleCreateTagError.style.display = 'none';
		// hide the tags modal
		UIkit.modal('#getbible-app-tags').hide();
		// add the values to the fields
		getbibleCreateTagName.value = '';
		getbibleCreateTagDescription.value = '';
		// show the edit tag modal
		UIkit.modal('#getbible-tag-creator').show();
	} catch (error) {
		console.error("Error occurred: ", error);
	}
}
getbibleCreateTag.onclick = async function () {
	try {
		if (getbibleCreateTagName.value.length == 0) {
			getbibleCreateTagError.style.display = '';
			getbibleCreateTagErrorMessage.textContent = '<?php echo Text::_('COM_GETBIBLE_YOU_MUST_ADD_A_TAG_NAME'); ?>';
		} else {
			getbibleCreateTagError.style.display = 'none';
			// trigger create of tag
			createTag(getbibleCreateTagName.value, getbibleCreateTagDescription.value);
		}
	} catch (error) {
		console.error("Error occurred: ", error);
	}
}
getbibleCanselCreateTag.onclick = async function () {
	try {
		// close edit view open tag view
		UIkit.modal('#getbible-tag-creator').hide();
		UIkit.modal('#getbible-app-tags').show();
	} catch (error) {
		console.error("Error occurred: ", error);
	}
}
getbibleSaveEditTag.onclick = async function () {
	try {
		getbibleEditTagError.style.display = 'none';
		// trigger update of tag
		updateTag(getbibleEditTagGuid.value, getbibleEditTagName.value, getbibleEditTagDescription.value);
	} catch (error) {
		console.error("Error occurred: ", error);
	}
}
getbibleDeleteEditTag.onclick = async function () {
	getbibleEditTagError.style.display = 'none';
	// trigger update of tag
	UIkit.modal.confirm('<?php echo Text::_('COM_GETBIBLE_YOU_ARE_ABOUT_TO_REMOVE_THIS_TAG_ENTIRELY_THIS_PROCESS_WILL_ALSO_DISCONNECT_THIS_TAG_FROM_ALL_VERSES_MEANING_THAT_IT_WILL_NO_LONGER_EXIST_IN_ANY_CONTEXT'); ?>').then( async function() {
		deleteTag(getbibleEditTagGuid.value);
	}, function () {
		console.log('Deleting of the tag cancelled.')
	});
}
getbibleCanselEditTag.onclick = async function () {
	try {
		// close edit view open tag view
		UIkit.modal('#getbible-tag-editor').hide();
		UIkit.modal('#getbible-app-tags').show();
	} catch (error) {
		console.error("Error occurred: ", error);
	}
}
// function to edit a tag
const editGetBibleTag = (guid, verse) => {
	let tag = getBibleTagItem(guid);
	if (tag) {
		// hide the tags modal
		UIkit.modal('#getbible-app-tags').hide();
		// add the values to the fields
		getbibleEditTagGuid.value = tag.guid;
		getbibleEditTagName.value = tag.name;
		getbibleEditTagDescription.value = tag.description;
		// this is for easy name update of tags
		getbibleEditTagRefeshVerse.value = verse;
		// show the edit tag modal
		UIkit.modal('#getbible-tag-editor').show();
	} else {
		// Show success message
		UIkit.notification({
			message: '<?php echo Text::_('COM_GETBIBLE_THERE_WAS_AN_ERROR_TAG_NOT_FOUND_ON_PAGE'); ?>',
			status: 'danger',
			timeout: 3000
		});
	}
};
// load the tag verse slider
var getbibleTagVerseSlider = document.getElementById('verse-tag-selection-slider');
noUiSlider.create(getbibleTagVerseSlider, {
	start: 1,
	connect: true,
	step: 1,
	format: getbibleFormatVerseSlider,
	tooltips: {
		to: function(numericValue) {
			return numericValue.toFixed(0);
		}
	},
	range: {
		'min': 1,
		'max': <?php echo $this->last_verse; ?>
	}
});
getbibleTagVerseSlider.noUiSlider.on('update', function(values, handle) {
	let value = Math.round(values[0]);
	// update active verse
	setActiveVerse(value, false);
});
// to watch the active list
UIkit.util.on('#getbible-active-tags', 'added', async function (event) {
	// Fires after an element has been added
	let addedElement = event.detail[1];
	// now add this tag to this verse
	await tagVerse(getbible_active_translation, getbible_book_nr, getbible_chapter_nr, addedElement.dataset.verse, addedElement.dataset.tag);
	setActiveVerse(addedElement.dataset.verse, false);
});
UIkit.util.on('#getbible-active-tags', 'removed', async function (event) {
	// Fires after an element has been removed
	let removedElement = event.detail[1];
	// now remove the tag from this verse
	if (removedElement.dataset.tagged) {
		await removeTagFromVerse(removedElement.dataset.tagged, removedElement.dataset.verse);
	}
});
</script>
