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
<script type="text/javascript">
// set global values
const getbible_active_translation = '<?php echo $this->chapter->abbreviation; ?>';
const getbible_verses = <?php echo json_encode($this->chapter->verses); ?>;
const getbible_book_nr = <?php echo $this->chapter->book_nr; ?>;
const getbible_chapter_nr = <?php echo $this->chapter->chapter; ?>;
const getbible_page_url = '<?php echo $this->getBaseUrl(); ?>/';
var triggerGetBibleReload = false;
</script>
<div class="uk-section-default uk-section">
	<div class="uk-container">
		<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
			<div class="uk-first-column">
				<div class="uk-margin">
					<?php if ($this->params->get('show_top_menu') == 1): ?>
						<?php echo $this->loadTemplate('getbibleapptopmenu'); ?>
					<?php endif; ?>
					<?php echo $this->loadTemplate('getbibleappbody'); ?>
					<?php if ($this->params->get('show_bottom_menu') == 1): ?>
						<?php echo $this->loadTemplate('getbibleappbottommenu'); ?>
					<?php endif; ?>
				</div>
				<?php if ($this->params->get('show_hash_validation') == 1 || $this->params->get('show_getbible_link') == 1 || $this->params->get('show_api_link') == 1): ?>
					<div class="uk-margin">
						<?php echo $this->loadTemplate('getbibleappfooter'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->loadTemplate('getbiblefavouriteverseselector'); ?>
<script type="text/javascript">
<?php echo $this->loadTemplate('getbiblelinkermanager'); ?>
// function to access verses by number
const getActiveVerseText = (verseNumber) => {
	const verseObj = getbible_verses.find(verse => verse.verse === verseNumber.toString());
	return verseObj ? verseObj.text : '';
}
<?php if ($this->params->get('activate_sharing') == 1 || $this->params->get('activate_tags') == 1 || $this->params->get('activate_notes') == 1): ?>
<?php if ($this->notes): ?>
var getbible_notes = <?php echo json_encode($this->notes); ?>;
<?php else: ?>
var getbible_notes = [];
<?php endif;?>
<?php if ($this->tags): ?>
var getbible_tags = <?php echo json_encode($this->tags); ?>;
<?php else: ?>
var getbible_tags = [];
<?php endif;?>
<?php if ($this->taggedverses): ?>
var getbible_tagged = <?php echo json_encode($this->taggedverses); ?>;
<?php else: ?>
var getbible_tagged = [];
<?php endif;?>
const getbibleFormatVerseSlider = {
	from: function (formattedValue) {
		return Number(formattedValue);
	},
	to: function(numericValue) {
		return Math.round(numericValue);
	}
};
<?php if ($this->params->get('activate_notes') == 1): ?>
const setActiveNoteTextarea = async (verse) => {
	// Find the textarea by id
	const textArea = document.getElementById('verse-note-textarea');
	// If the textarea exists
	if(textArea) {
		textArea.value = '';
		// Loop over the data array
		for(let i = 0; i < getbible_notes.length; i++) {
			// If the verse of the current object matches the passed verse
			if(getbible_notes[i].verse == verse) {
				// Update the value of the textarea with the note of the matching object
				textArea.value = getbible_notes[i].note;
				// Stop looping as we found the matching verse
				break;
			}
		}
	}
}
const setActiveNoteVerse = async (verse, note = null) => {
	// Find the note verse by id
	const noteVerse = document.getElementById('getbible-verse-note-' + verse);
	// If the textarea exists
	if(noteVerse) {
		noteVerse.value = '';
		// Loop over the data array
		for(let i = 0; i < getbible_notes.length; i++) {
			// If the verse of the current object matches the passed verse
			if(getbible_notes[i].verse == verse) {
				// if we have a new note we also update the object
				if (note !== null) {
					getbible_notes[i].note = note;
				}
				// Update the value of the verse (note) with the note of the matching object
				noteVerse.textContent = getbible_notes[i].note;
				// Stop looping as we found the matching verse
				break;
			}
		}
	} else {
		setTimeout(function() {
			location.reload();
		}, 2000);
	}
	// if a note is empty we remove it completely
	if (note !== null && note.trim() === '' || triggerGetBibleReload) {
		setTimeout(function() {
			location.reload();
		}, 2000);
	}
}
<?php endif;?>
<?php if ($this->params->get('activate_tags') == 1): ?>
const setActiveTags = async (verse) => {
	// update the active tags
	updateActiveGetBibleTaggedItems(verse);
	updateAllGetBibleTaggedItems(verse);
}
const canEditTag = (item) => {
	return item.linker && getbible_linker_guid === item.linker;
}
const updateActiveGetBibleTaggedItems = async (verse) => {
	removeChildrenElements('getbible-active-tags');
	getbible_tags.forEach((itemData) => {
		// if item is not in getbible_tagged array, move it back to all items list
		let foundItem = getbible_tagged.find(item => item.tag == itemData.guid && item.verse == verse);
		if (foundItem) {
			let itemElement = createGetbileTagDivItem(itemData.guid, verse, itemData.name, itemData.url, canEditTag(itemData), foundItem.guid);
			let allList = document.querySelector('#getbible-active-tags');
			allList.appendChild(itemElement);
		}
	});
};
const updateAllGetBibleTaggedItems = async (verse) => {
	removeChildrenElements('getbible-tags');
	getbible_tags.forEach((itemData) => {
		// if item is not in getbible_tagged array, move it back to all items list
		if (!getbible_tagged.find(item => item.tag == itemData.guid && item.verse == verse)) {
			let itemElement = createGetbileTagDivItem(itemData.guid, verse, itemData.name, itemData.url, canEditTag(itemData));
			let allList = document.querySelector('#getbible-tags');
			allList.appendChild(itemElement);
		}
	});
};
const getBibleTagItem = (guid) => {
	let item = getbible_tags.find(item => item.guid == guid);
	if (item) {
		return item;
	} else {
		return null; // or whatever default value you want
	}
};
const setBibleTagItem = (guid, tagItem) => {
	let index = getbible_tags.findIndex(item => item.guid == guid);
	if (index !== -1) {
		// If the item exists, replace it
		getbible_tags[index] = tagItem;
	} else {
		// If the item doesn't exist, add it
		getbible_tags.push(tagItem);
		// sort getbible_tagged by 'name' key in ascending order
		getbible_tags.sort((a, b) => {
			if (a.name < b.name) {
				return -1;
			}
			if (a.name > b.name) {
				return 1;
			}
			return 0;
		});
	}
};
const deleteBibleTagItem = (guid) => {
	let index = getbible_tags.findIndex(item => item.guid == guid);
	if (index !== -1) {
		// If the item exists, remove it
		getbible_tags.splice(index, 1);
	} else {
		// If the item doesn't exist, do nothing
		console.log('Item does not exist');
	}
};
const setActiveTaggedVerse = async (data) => {
	let found = false;
	getbible_tagged.forEach((itemData) => {
		if(itemData.verse == data.verse && itemData.tag == data.tag) {
			found = true;
		}
	});
	if (!found) {
		// add code to add this data from the getbible_tagged array
		getbible_tagged.push(data);
	}
	// add tag if not found
	let verse_tag = document.getElementById('getbible-verse-tag-' + data.verse);
	if (verse_tag) {
		verse_tag.style.display = '';
	}
	// check if we should reload
	if (triggerGetBibleReload) {
		setTimeout(function() {
			location.reload();
		}, 2000);
	}
}
const setInactiveTaggedVerse = async (tag, verse) => {
	getbible_tagged = getbible_tagged.filter((itemData) => {
		return !(itemData.guid == tag);
	});
	let found = false;
	getbible_tagged.forEach((itemData) => {
		if(itemData.verse == verse) {
			found = true;
		}
	});
	if (!found) {
		// remove tag if not found
		let verse_tag = document.getElementById('getbible-verse-tag-' + verse);
		if (verse_tag) {
			verse_tag.style.display = 'none';
		}
	}
	// check if we should reload
	if (triggerGetBibleReload) {
		setTimeout(function() {
			location.reload();
		}, 2000);
	}
}
<?php endif;?>
// Define an object with getter and setter properties
const getbibleActiveVerse = {
	_value: 1, // the actual variable
	get value() {
		return this._value;
	},
	set value(val) {
		this._value = val;
		// Update all elements with the class name `active-getbible-verse`
		let activeGetbibleVerse = document.getElementsByClassName('active-getbible-verse');
		for (let i = 0; i < activeGetbibleVerse.length; i++) {
			activeGetbibleVerse[i].textContent = this._value;
		}
		// Update all elements with the class name `getbible-verse-selected-text`
		let getbibleVerseSelectedText = document.getElementsByClassName('getbible-verse-selected-text');
		let verseText = getActiveVerseText(this._value);
		for (let i = 0; i < getbibleVerseSelectedText.length; i++) {
			getbibleVerseSelectedText[i].textContent = verseText;
		}
		// Update all elements with the class name `getbible-verse-pre-text`
		let getbibleVersePreText = document.getElementsByClassName('getbible-verse-pre-text');
		let pre_value = val - 1;
		let versePre = getActiveVerseText(pre_value);
		for (let i = 0; i < getbibleVersePreText.length; i++) {
			getbibleVersePreText[i].textContent = versePre;
		}
		// Update all elements with the class name `getbible-verse-post-text`
		let getbibleVersePostText = document.getElementsByClassName('getbible-verse-post-text');
		let post_value = val + 1;
		let versePost = getActiveVerseText(post_value);
		for (let i = 0; i < getbibleVersePostText.length; i++) {
			getbibleVersePostText[i].textContent = versePost;
		}
<?php if ($this->params->get('activate_notes') == 1): ?>		// update the note
		setActiveNoteTextarea(this._value);<?php endif; ?>
<?php if ($this->params->get('activate_tags') == 1): ?>		// update the tags
		setActiveTags(this._value);<?php endif; ?>
		setSharedValues(this._value, this._value, true);
	}
};
const setActiveVerse = async (number, update = true) => {
	getbibleActiveVerse.value = number;
	<?php if ($this->params->get('activate_tags') == 1 || $this->params->get('activate_notes') == 1): ?>
	// update all the active sliders
	if (update) {
		<?php if ($this->params->get('activate_tags') == 1): ?>
		// update the tag sliders
		getbibleTagVerseSlider.noUiSlider.set(number);
		<?php endif; ?>
		<?php if ($this->params->get('activate_notes') == 1): ?>
		// update the note sliders
		getbibleNoteVerseSlider.noUiSlider.set(number);
		<?php endif; ?>
	}
	<?php endif; ?>
	<?php if ($this->params->get('enable_open_ai') == 1 && ($buttons = $this->promptIntegration($this->prompts, [2])) !== null): ?>
		<?php foreach($buttons as $button): ?>
			let ids = [];<?php if ($this->params->get('activate_sharing') == 1): ?>
			ids.push('getbible-openai-sharing-<?php echo $button->guid; ?>');<?php endif; ?><?php if ( $this->params->get('activate_tags') == 1): ?>
			ids.push('getbible-openai-tags-<?php echo $button->guid; ?>');<?php endif; ?><?php if ($this->params->get('activate_notes') == 1): ?>
			ids.push('getbible-openai-notes-<?php echo $button->guid; ?>');<?php endif; ?>
			setOpenaiUrl(ids, '<?php echo $button->guid; ?>', 0, number, getbible_chapter_nr, getbible_book_nr, getbible_active_translation);
		<?php endforeach; ?>
	<?php endif; ?>
}
const setActiveOpenModel = async (model, update = true) => {
	// Your new value
	let newValue = 'target: #getbible-app-' + model;
	// Get all elements with the class name 'getbible-verse-link'
	let elements = document.getElementsByClassName('getbible-verse-link');
	// Update the 'uk-toggle' attribute of each element
	for (let i = 0; i < elements.length; i++) {
		elements[i].setAttribute('uk-toggle', newValue);
	}
	// add this to memory
	if (update) {
		setLocalMemory('getbible_active_open_model', {target: model});
		// update the tag sliders
		setActiveVerse(getbibleActiveVerse.value);
	}
}
</script>
<?php if ($this->params->get('activate_sharing') == 1): ?>
	<?php echo $this->loadTemplate('getbibleappshare'); ?>
<?php endif; ?>
<?php if ($this->params->get('activate_tags') == 1): ?>
	<?php echo $this->loadTemplate('getbibleapptags'); ?>
<?php endif; ?>
<?php if ($this->params->get('activate_notes') == 1): ?>
	<?php echo $this->loadTemplate('getbibleappnotes'); ?>
<?php endif; ?>
<script type="text/javascript">
// check if we have values in memory
var tmp = getLocalMemory('getbible_active_open_model');
if (tmp !== null && typeof tmp.target !== 'undefined') {
	let available_model = document.getElementById('getbible-app-' + tmp.target);
	if (available_model) {
		setActiveOpenModel(tmp.target, false);
	}
}
<?php if ($this->verses->first > 3): ?>
document.addEventListener('DOMContentLoaded', function() {
	var element = document.getElementById('getbible-verse-<?php echo $this->verses->first; ?>');
	if(element) {
		smoothScrollTo(element, 40);
	}
});
<?php endif; ?>
<?php endif; ?>
// always make sure that we have the valid linker set
if (getbible_linker_guid !== null) {
	setActiveLinkerOnPage(getbible_linker_guid);
}
</script>
