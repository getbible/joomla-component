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

/* JS Document */

const memoryAppMemory = [];
const setLocalMemory = (key, values, merge = false) =>  {
	if (merge) {
		values = mergeLocalMemory(key, values);
	} else {
		values = JSON.stringify(values);
	}

	if (typeof Storage !== "undefined") {
		localStorage.setItem(key, values);
	} else {
		memoryAppMemory[key] = values;
	}
}
const mergeLocalMemory = (key, values) =>  {
	const oldValues = getLocalMemory(key);

	if (oldValues) {
		values = { ...oldValues, ...values };
	}

	return JSON.stringify(values);
}
const getLocalMemory = (key, defaultValue = null, setDefault = false) =>  {

	let returnValue = null;

	if (typeof Storage !== "undefined") {
		const localValue = localStorage.getItem(key);

		if (isJsonString(localValue)) {
			returnValue = JSON.parse(localValue);
		}
	} else if (typeof memoryAppMemory[key] !== "undefined") {
		const localValue = memoryAppMemory[key];

		if (isJsonString(localValue)) {
			returnValue = JSON.parse(localValue);
		}
	}

	if (returnValue) {
		return returnValue;
	} else if (setDefault) {
		setLocalMemory(key, defaultValue, false);
	}

	return defaultValue;
}
const clearLocalMemory = (key) =>  {
	if (typeof Storage !== "undefined") {
		localStorage.removeItem(key);
	} else if (typeof memoryAppMemory[key] !== "undefined") {
		delete memoryAppMemory[key];
	}
}
const isJsonString = (str) =>  {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

class ScrollMemory {
	constructor(divId) {
		this.div = document.getElementById(divId);
		this.localStorageKey = `${divId}ScrollPosition`;

		this.init();
	}

	init() {
		this.restoreScrollPosition();
		this.observeDivChanges();
		this.addScrollEventListener();
	}

	restoreScrollPosition() {
		const scrollPosition = localStorage.getItem(this.localStorageKey);
		if (scrollPosition) {
			this.div.scrollTop = scrollPosition;
		}
	}

	saveScrollPosition() {
		localStorage.setItem(this.localStorageKey, this.div.scrollTop);
	}

	observeDivChanges() {
		const observer = new MutationObserver(() => this.restoreScrollPosition());
		const config = { childList: true };
		observer.observe(this.div, config);
	}

	addScrollEventListener() {
		this.div.addEventListener('scroll', () => this.saveScrollPosition());
	}
}
/**
 * JS Function to set Share His Word url
 */
const setShareHisWordUrl = async (linker, translation, book, chapter) => {
	// Make a request to your endpoint
	const response = await fetch(getShareHisWordUrl(linker, translation, book, chapter));

	// Wait for the server to return the response, then parse it as JSON.
	const data = await response.json();

	if (data.url || data.error) {
		return data; // return the data object on success
	} else {
		throw new Error(data); // throw an error if the request was not successful
	}
};
/**
 * JS Function to check if we have a valid linker key
 */
const checkValidLinker = async (linker, oldLinker) => {
	// Make a request to your endpoint
	const response = await fetch(getCheckValidLinkerUrl(linker, oldLinker));

	// Wait for the server to return the response, then parse it as JSON.
	const data = await response.json();

	if (data.success || data.error) {
		return data; // return the data object on success
	} else {
		throw new Error(data); // throw an error if the request was not successful
	}
};
/**
 * JS Function to set the search url
 */
const setSearchUrl = async (search, translation) => {
	// always reset the url value
	document.getElementById('getbible-search-word').href = '#';
	try {
		// Make a request to your endpoint
		const response = await fetch(getSearchURL(search, translation));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.url) {
			document.getElementById('getbible-search-word').href = data.url;
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};
/**
 * JS Function to set open AI url
 */
const setOpenaiUrl = async (ids, guid, words, verse, chapter, book, translation) => {
	// always reset the url value
	ids.forEach(id => updateUrl(id, '#'));
	try {
		// Make a request to your endpoint
		const response = await fetch(getOpenaiURL(guid, words, verse, chapter, book, translation));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.url) {
			ids.forEach(id => updateUrl(id, data.url));
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to update the url
 */
const updateUrl = (id, url) => {
	let button = document.getElementById(id);
	if (button) {
		button.href = url;
	}
}

/**
 * JS Function to set the linker session value
 */
const setLinker = async (linker) => {
	try {
		// Make a request to your endpoint
		const response = await fetch(getSetLinkerURL(linker));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.success) {
			console.log(linker, data.success);
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to set the linker pass value
 */
const setLinkerAccess = async (linker, pass, oldPass = '') => {
	// build form
	const formData = new FormData();

	// add the form data
	formData.set('linker', linker);
	formData.set('pass', pass);
	formData.set('old', oldPass);

	let options = {
		method: 'POST',
		body: formData
	}

	const response = await fetch(getSetLinkerAccessURL(), options);
	const data = await response.json();

	if (data.success || data.error) {
		return data; // return the data object on success
	} else {
		throw new Error(data); // throw an error if the request was not successful
	}
};

/**
 * JS Function to set the active linker on the page
 */
const setActiveLinkerOnPage = async (guid) => {
	// Get all elements with the class name 'getbible-linker-guid-value'
	let values = document.getElementsByClassName('getbible-linker-guid-value');
	let inputs = document.getElementsByClassName('getbible-linker-guid-input');
	// Update the 'textContent' of each value display
	for (let i = 0; i < values.length; i++) {
		values[i].textContent = guid;
	}
	// Update the 'value' of each input area
	for (let i = 0; i < inputs.length; i++) {
		inputs[i].value = guid;
	}
}

/**
 * JS Function to set a note
 */
const setNote = async (book, chapter, verse, note) => {
	try {
		// build form
		const formData = new FormData();

		// add the form data
		formData.set('book', book);
		formData.set('chapter', chapter);
		formData.set('verse', verse);
		formData.set('note', note);

		let options = {
			method: 'POST',
			body: formData
		}

		// Make a request to your endpoint
		const response = await fetch(getSetNoteURL(), options);

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.success) {
			// Show success message
			UIkit.notification({
				message: data.success,
				status: 'success',
				timeout: 5000
			});
			// update the local and the html in the verses
			setActiveNoteVerse(verse, data.note);
			setActiveNoteTextarea(verse);
			setTimeout(function() {
				UIkit.modal('#getbible-app-notes').hide();
			}, 2000);
		} else if (data.access_required && data.error) {
			setupGetBibleAccess(
				'getbible-app-notes',
				data.error,
				setNote,
				[book, chapter, verse, note]
			);
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to set a tag
 */
const setTag = async (name) => {
	try {
		// Make a request to your endpoint
		const response = await fetch(getSetTagURL(name));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.success) {
			// Show success message
			UIkit.notification({
				message: data.success,
				status: 'success',
				timeout: 5000
			});
		}  else if (data.access_required && data.error) {
			setupGetBibleAccess(
				'getbible-app-tags',
				data.error,
				setTag,
				[name]
			);
		} else if (data.error) {
			// Show danger message
			UIkit.notification({
				message: data.error,
				status: 'danger',
				timeout: 3000
			});
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to set a tag to a verse
 */
const tagVerse = async (translation, book, chapter, verse, tag) => {
	try {
		// Make a request to your endpoint
		const response = await fetch(getTagVerseURL(translation, book, chapter, verse, tag));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.success) {
			// So success message
			UIkit.notification({
				message: data.success,
				status: 'success',
				timeout: 3000
			});
			// update the local and the html in the verses
			setActiveTaggedVerse(data);
		} else if (data.access_required && data.error) {
			setupGetBibleAccess(
				'getbible-app-tags',
				data.error,
				tagVerse,
				[translation, book, chapter, verse, tag]
			);
		} else if (data.error) {
			// Show danger message
			UIkit.notification({
				message: data.error,
				status: 'danger',
				timeout: 3000
			});
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to remove a tag from a verse
 */
const removeTagFromVerse = async (tag, verse) => {
	try {
		// Make a request to your endpoint
		const response = await fetch(getRemoveTagFromVerseURL(tag));

		// Wait for the server to return the response, then parse it as JSON.
		const data = await response.json();

		// Call another function after the response has been received
		if (data.success) {
			// Show success message
			UIkit.notification({
				message: data.success,
				status: 'success',
				timeout: 3000
			});
			// update the local and the html in the verses
			setInactiveTaggedVerse(tag, verse);
		} else if (data.access_required && data.error) {
			setupGetBibleAccess(
				'getbible-app-tags',
				data.error,
				removeTagFromVerse,
				[tag, verse]
			);
		} else if (data.error || data.notice) {
			if (data.notice) {
				// Show primary message as notice
				UIkit.notification({
					message: data.notice,
					status: 'primary',
					timeout: 8000
				});
			} else {
				// Show danger message
				UIkit.notification({
					message: data.error,
					status: 'danger',
					timeout: 3000
				});
			}
			updateActiveGetBibleTaggedItems(verse);
			updateAllGetBibleTaggedItems(verse);
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to set get Bible access
 */
const setupGetBibleAccess = async (active_modal, error_message, callback, args) => {
	// close the active modal
	UIkit.modal('#' + active_modal).hide();
	try {
		let modal = UIkit.modal('#getbible_favourite_verse_selector');
		modal.show();
		// get linker
		let linker_old = getLocalMemory('getbible_active_linker_guid');
		// Wait for the modal to be closed
		await new Promise(resolve => {
			// The 'hidden' event is triggered when the modal is closed
			UIkit.util.on(modal.$el, 'hidden', function() {
				resolve();
			});
		});
		// get linker
		let linker = getLocalMemory('getbible_active_linker_guid');
		let pass = getLocalMemory(linker);
		// check if access was set
		if (pass) {
			UIkit.modal('#' + active_modal).show();
			// we should reload the page if a new linker was set
			if (linker_old !== linker) {
				triggerGetBibleReload = true;
			}
			callback(...args);
		} else {
			// Show message
			UIkit.notification({
				message: error_message,
				status: 'warning',
				timeout: 5000
			});
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
};

/**
 * JS Function to create an tag div item
 */
const createGetbileTagDivItem = (id, verse, name, url, tagged = null, ) => {
	let itemElement = document.createElement('div');
	itemElement.id = 'getbible-tag-' + id;
	itemElement.dataset.tag = id;
	itemElement.dataset.verse = verse;
	if (tagged !== null) {
		itemElement.dataset.tagged = tagged;
	}

	let marginDiv = document.createElement('div');
	marginDiv.className = 'uk-margin';

	let cardDiv = document.createElement('div');
	cardDiv.className = 'uk-card uk-card-default uk-card-body uk-card-small';

	// Create handle span
	let handleSpan = document.createElement('span');
	handleSpan.className = 'uk-sortable-handle uk-margin-small-right uk-text-center';
	handleSpan.setAttribute('uk-icon', 'move');
	handleSpan.insertAdjacentText('beforeend', name + ' ');

	// Create view icon
	let viewIcon = document.createElement('a');
	viewIcon.href = url;
	viewIcon.className = 'uk-icon-button';
	viewIcon.setAttribute('uk-icon', 'tag');
	viewIcon.setAttribute('uk-tooltip', 'title: ' + Joomla.JText._('COM_GETBIBLE_VIEW_ALL_VERSES_TAGGED'));
	viewIcon.onclick = (event) => {
		event.stopPropagation();
	};

	// Append view icon and name to cardDiv
	cardDiv.appendChild(handleSpan);
	cardDiv.appendChild(viewIcon);

	marginDiv.appendChild(cardDiv);
	itemElement.appendChild(marginDiv);

	return itemElement;
};

/**
 * JS Function to clear content from its parent div
 */
const removeChildrenElements = (parentId) => {
	let list = document.querySelector('#' + parentId);
	list.innerHTML = '';
};