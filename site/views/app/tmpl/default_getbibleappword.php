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
<div id="getbible-app-word" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-margin uk-margin-remove-top">
			<span class="uk-text-large uk-text-bolder" id="getbible-active-word">WORD</span> <span uk-icon="icon: search; ratio: 2"></span>
			<span class="uk-text-small uk-text-muted uk-margin-remove"><?php echo JText::_('COM_GETBIBLE_RESEARCH_THIS'); ?>...</span>
		</div>
		<div class="uk-margin">
			<?php if ($this->params->get('activate_search') == 1): ?>
				<a href="#" id="getbible-search-word" class="uk-button uk-button-default uk-button-large uk-width-1-1 uk-margin-small-bottom"><?php echo JText::_('COM_GETBIBLE_SEARCH'); ?></a>
			<?php endif; ?>
			<?php if ($this->params->get('enable_open_ai') == 1 && ($buttons = $this->promptIntegration($this->prompts, [1,3])) !== null): ?>
				<?php foreach ($buttons as $button): ?>
					<a href="#" id="getbible-openai-<?php echo $button->guid; ?>" class="uk-button uk-button-default uk-button-large uk-width-1-1 uk-margin-small-bottom"><?php echo $this->escape($button->name); ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
	var mainArea = document.getElementById('getbible-holy-scripture');
	if (mainArea) {
		let startedInWord = false;
		mainArea.addEventListener('mousedown', function(event) {
			let target = event.target;
			if (target.nodeType === Node.TEXT_NODE) {
				target = target.parentNode;
			}
			startedInWord = target.classList.contains('getbible-word');
		});

		mainArea.addEventListener('mouseup', function (event) {
			var selection = window.getSelection();
			if (!selection.rangeCount || !startedInWord) return;

			var range = selection.getRangeAt(0);
			var startContainer = range.startContainer;
			var endContainer = range.endContainer;

			// If the startContainer or endContainer is a Text node, use its parent
			if (startContainer.nodeType === Node.TEXT_NODE) {
				startContainer = startContainer.parentNode;
			}
			if (endContainer.nodeType === Node.TEXT_NODE) {
				endContainer = endContainer.parentNode;
			}

			var wordElements = Array.from(document.querySelectorAll('.getbible-word'));
			var startIdx = wordElements.indexOf(startContainer);
			var endIdx = wordElements.indexOf(endContainer);
			var selectedWordElements = wordElements.slice(startIdx, endIdx + 1);

			var selectedWords = selectedWordElements.reduce(function(acc, wordElement) {
				var verse = wordElement.getAttribute('data-verse');
				var nr = wordElement.getAttribute('data-word-nr');

				if (!acc[verse]) {
					acc[verse] = {};
				}

				acc[verse][nr] = {
					urlWord: wordElement.getAttribute('data-url-word'),
					word: wordElement.getAttribute('data-word'),
					wordNr: nr,
					verseNr: verse
				};

				return acc;
			}, {});

			if (Object.keys(selectedWords).length == 1) {
				// get the words
				let verseWord = getBibleWordsFromSingleVerse(selectedWords, 'word');
				let verseUrlWord = getBibleWordsFromSingleVerse(selectedWords, 'urlWord');
				let wordNumber = getBibleWordsFromSingleVerse(selectedWords, 'wordNr', '-');
				let verseNumber = getBibleWordsFromSingleVerse(selectedWords, 'verseNr', '-');
				// open the model
				getBibleWord(verseWord, verseUrlWord, wordNumber, verseNumber);
				// Clear the selection
				selection.removeAllRanges();
			}
			<?php if ($this->params->get('activate_sharing') == 1): ?>else if (Object.keys(selectedWords).length > 1)
			{
				// get the range
				let numbers = getBibleFirstAndLastVerseNumbers(selectedWords);
				// set the active verse
				setActiveVerse(numbers.start);
				// update the share verse slider
				getbibleShareVerseSlider.noUiSlider.set([numbers.start, numbers.end]);
				// Clear the selection
				selection.removeAllRanges();
				// open the module
				UIkit.modal('#getbible-app-sharing').show();
			}<?php endif; ?>
		});
	}
});
var getbible_active_word = '';
const getBibleWord = async (word, url_word, word_number, verse_number) => {
	getbible_active_word = url_word;
	document.getElementById('getbible-active-word').textContent  = word;
	<?php if ($this->params->get('activate_search') == 1): ?>
		setSearchUrl(getbible_active_word, getbible_active_translation);
	<?php endif; ?>
	<?php if ($this->params->get('enable_open_ai') == 1 && ($buttons = $this->promptIntegration($this->prompts, [1])) !== null): ?>
		<?php foreach($buttons as $button): ?>
			<?php $id = 'getbible-openai-' . $button->guid; ?>
			if (word_number.includes('-')) {
				document.getElementById('<?php echo $id; ?>').style.display = 'none';
			} else {
				document.getElementById('<?php echo $id; ?>').style.display = '';
				let ids = [];
				ids.push('<?php echo $id; ?>');
				setOpenaiUrl(ids, '<?php echo $button->guid; ?>', word_number, verse_number, getbible_chapter_nr, getbible_book_nr, getbible_active_translation);
			}
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if ($this->params->get('enable_open_ai') == 1 && ($buttons = $this->promptIntegration($this->prompts, [3])) !== null): ?>
		<?php foreach($buttons as $button): ?>
			<?php $id = 'getbible-openai-' . $button->guid; ?>
			if (word_number.includes('-')) {
				document.getElementById('<?php echo $id; ?>').style.display = '';
				let ids = [];
				ids.push('<?php echo $id; ?>');
				setOpenaiUrl(ids, '<?php echo $button->guid; ?>', word_number, verse_number, getbible_chapter_nr, getbible_book_nr, getbible_active_translation);
			} else {
				document.getElementById('<?php echo $id; ?>').style.display = 'none';
			}
		<?php endforeach; ?>
	<?php endif; ?>
	setTimeout(function() { UIkit.modal('#getbible-app-word').show(); }, 500);
}
const getBibleWordsFromSingleVerse = (verseData, valueKey, separator = ' ') => {
	// Get the first verse key in the verseData object
	var firstVerseKey = Object.keys(verseData)[0];
	// Get the words for the first verse
	var firstVerseWords = verseData[firstVerseKey];
	// Join the specified values into a single string
	return Object.values(firstVerseWords).map(word => word[valueKey]).join(separator);
}
<?php if ($this->params->get('activate_sharing') == 1): ?>
const getBibleFirstAndLastVerseNumbers = (verseData) => {
	var verseNumbers = Object.keys(verseData).map(Number).sort((a, b) => a - b);
	return {
		start: verseNumbers[0],
		end: verseNumbers[verseNumbers.length - 1]
	};
}
<?php endif; ?>
</script>
