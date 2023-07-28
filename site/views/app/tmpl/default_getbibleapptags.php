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
<div id="getbible-app-tags" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-margin uk-margin-remove-top">
			<h3 class="uk-modal-title uk-margin-remove"><?php echo $this->chapter->book_name; ?> <?php echo $this->chapter->chapter; ?>:<span class="active-getbible-verse">1</span> <span uk-icon="icon: tag; ratio: 1.5"></span></h3>
			<span class="uk-text-small uk-text-muted uk-margin-remove"><?php echo JText::_('COM_GETBIBLE_TAGGING_THIS_VERSE'); ?></span>
		</div>
		<div class="uk-padding uk-padding-remove-bottom">
			<div id="verse-tag-selection-slider"></div>
		</div>
		<div class="uk-margin">
			<p class="uk-text-emphasis uk-margin-remove-top getbible-verse-selected-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"><?php echo JText::_('COM_GETBIBLE_THE_ACTIVE_VERSE_SELECTED_TEXT_SHOULD_LOAD_HERE'); ?></p>
			<div class="uk-child-width-1-2@s uk-grid-small" uk-grid>
				<div>
					<div style="position: sticky; top: 0;">
						<h4><?php echo JText::_('COM_GETBIBLE_ACTIVE'); ?></h4>
						<div id="getbible-active-tags" uk-sortable="group: getbible-tag-selection, handle: .uk-sortable-handle">
							<!-- Active items will be dynamically added here -->
						</div>
					</div>
				</div>
				<div>
					<h4><?php echo JText::_('COM_GETBIBLE_AVAILABLE_TAGS'); ?></h4>
					<div id="getbible-tags" class="uk-width-auto uk-grid-small"  uk-sortable="group: getbible-tag-selection, handle: .uk-sortable-handle" style="max-height: 400px; overflow-y: auto;" uk-grid>
						<!-- All items will be dynamically added here -->
					</div>
				</div>
			</div>
			<div class="uk-child-width-1-2@s uk-grid-small" uk-grid>
				<div>
					<p class="uk-text-small uk-text-muted uk-margin-remove"><?php echo JText::_('COM_GETBIBLE_DRAG_AND_DROP_THE_DESIRED_TAG_FROM_THE_AVAILABLE_ONES_TO_THE_ACTIVE_AREA'); ?></p>
				</div>
				<div>
					<p class="uk-text-small uk-text-muted uk-margin-remove"><?php echo JText::_('COM_GETBIBLE_TO_UNTAG_A_VERSE_DRAG_AND_DROP_THE_DESIRED_TAG_FROM_ACTIVE_TO_THE_AVAILABLE_TAGS_AREA'); ?></p>
				</div>
			</div>
		</div>
		<?php $this->modalState->main = 'tags'; ?>
		<?php $this->modalState->one = 'notes'; ?>
		<?php $this->modalState->oneText = JText::_('COM_GETBIBLE_NOTES'); ?>
		<?php $this->modalState->two = 'sharing'; ?>
		<?php $this->modalState->twoText = JText::_('COM_GETBIBLE_SHARE'); ?>
		<?php echo $this->loadTemplate('getbibleappmodalbottom'); ?>
	</div>
</div>
<script type="text/javascript">
// track the scroller of the tags
new ScrollMemory('getbible-tags');
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
