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
<div id="getbible-app-notes" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-margin uk-margin-remove-top">
			<h3 class="uk-modal-title uk-margin-remove"><?php echo $this->chapter->book_name; ?> <?php echo $this->chapter->chapter; ?>:<span class="active-getbible-verse">1</span> <span uk-icon="icon: comment; ratio: 1.5"></span></h3>
			<span class="uk-text-small uk-text-muted uk-margin-remove"><?php echo Text::_('COM_GETBIBLE_NOTES_ON_THIS_VERSE'); ?></span>
		</div>
		<div class="uk-padding uk-padding-remove-bottom">
			<div id="verse-note-selection-slider"></div>
		</div>
		<div class="uk-margin">
			<p class="uk-text-muted uk-text-small uk-margin-remove getbible-verse-pre-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"></p>
			<p class="uk-text-emphasis uk-margin-remove getbible-verse-selected-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"><?php echo Text::_('COM_GETBIBLE_THE_ACTIVE_VERSE_SELECTED_TEXT_SHOULD_LOAD_HERE'); ?></p>
			<p class="uk-text-muted uk-text-small uk-margin-remove getbible-verse-post-text direction-<?php echo strtolower($this->translation->direction); ?>"
				dir="<?php echo $this->translation->direction; ?>"></p>
			<?php echo LayoutHelper::render('textarea', [
				'id' => 'verse-note-textarea',
				'class_other' => 'uk-margin',
				'placeholder' => Text::_('COM_GETBIBLE_ADD_YOUR_NOTES_HERE'),
				'direction' => $this->translation->direction,
			]); ?>
			<button id="save-verse-note" class="uk-button  uk-width-1-1 uk-button-default" onclick="saveGetBibleNote();"><?php echo Text::_('COM_GETBIBLE_SAVE'); ?></button>
		</div>
		<?php $this->modalState->main = 'notes'; ?>
		<?php $this->modalState->one = 'tags'; ?>
		<?php $this->modalState->oneText = Text::_('COM_GETBIBLE_TAGS'); ?>
		<?php $this->modalState->two = 'sharing'; ?>
		<?php $this->modalState->twoText = Text::_('COM_GETBIBLE_SHARE'); ?>
		<?php echo $this->loadTemplate('getbibleappmodalbottom'); ?>
	</div>
</div>
<script type="text/javascript">
// load the note verse slider
var getbibleNoteVerseSlider = document.getElementById('verse-note-selection-slider');
const getbibleNoteVerseText = document.getElementById('verse-note-textarea');
noUiSlider.create(getbibleNoteVerseSlider, {
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
getbibleNoteVerseSlider.noUiSlider.on('update', function(values, handle) {
	let value = Math.round(values[0]);
	// update active verse
	setActiveVerse(value, false);
});
const saveGetBibleNote = () => {
	setNote(getbible_book_nr, getbible_chapter_nr, getbibleActiveVerse.value, getbibleNoteVerseText.value);
}
</script>
