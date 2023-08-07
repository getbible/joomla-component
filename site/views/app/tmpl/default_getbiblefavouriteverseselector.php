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

// set book
$favourite_verse['book_options'] = array_map( function ($item) {
	return (object) ['key' => (int) $item->nr, 'value' => $item->name];
}, $this->books);
$favourite_verse['book_default'] = (int) $this->chapter->book_nr;
// set chapter
$favourite_verse['chapter_default'] = (int) $this->chapter->chapter;
// set verse
$favourite_verse['verse_default'] = (int) $this->verses->first;
// set buttons
$buttons = [
	['id' => 'getbible-new-favourite-verse-session', 'name' => JText::_('COM_GETBIBLE_NEW'), 'class' => 'uk-button uk-button-default uk-width-1-4'],
	['id' => 'getbible-select-favourite-verse', 'name' => JText::_('COM_GETBIBLE_SELECT'), 'class' => 'uk-button uk-button-default uk-width-2-4'],
	['id' => 'getbible-cancel-favourite-verse', 'close' => true, 'name' => JText::_('COM_GETBIBLE_CANCEL'), 'class' => 'uk-button uk-button-danger uk-width-1-4']
];


?>
<?php echo JLayoutHelper::render('modal', [
	'id' => 'getbible_favourite_verse_selector',
	'header' => JText::_('COM_GETBIBLE_FAVOURITE_VERSE'),
	'header_class_other' => 'uk-text-center',
	'close' => true,
	'content' => JLayoutHelper::render('getbiblefavouriteverse', $favourite_verse),
	'buttons_class' => 'uk-button-group uk-width-1-1',
	'buttons_id' => 'getbible-favourite-buttons',
	'buttons' => $buttons
]); ?>
<script type="text/javascript">
// set the field objects
const favouriteError = document.getElementById('getbible_favourite_error');
const favouriteErrorMessage = document.getElementById('getbible_favourite_error_message');
const favouriteBook = document.getElementById('getbible_favourite_book');
const favouriteChapter = document.getElementById('getbible_favourite_chapter');
const favouriteVerse = document.getElementById('getbible_favourite_verse');
const favouriteLinker = document.getElementById('getbible_favourite_linker');
const favouriteSelection = document.getElementById('getbible-select-favourite-verse');
const cancelFavouriteSelection = document.getElementById('getbible-cancel-favourite-verse');
const newFavouriteSession = document.getElementById('getbible-new-favourite-verse-session');
const favouriteBooks = <?php echo json_encode($favourite_verse['book_options']); ?>;
<?php if ($this->params->get('show_settings') == 1): ?>
const sessionAccessStatusSwitch = document.getElementById('getbible-session-status-switch');
<?php endif; ?>
// Helper function to update the UI
const updateUIAfterSettingFavourite = (linker, valid = false) => {
	favouriteError.style.display = 'none';
	setActiveLinkerOnPage(linker);
	setLocalMemory('getbible_active_linker_guid', linker);
	if (valid) {
		setLocalMemory(linker + '-validated', true);<?php if ($this->params->get('show_settings') == 1): ?>
		setSessionStatusAccess();<?php endif; ?>
	} else {
		setLocalMemory(linker + '-validated', false);
	}
	UIkit.modal('#getbible_favourite_verse_selector').hide();
};
// Helper function to handle errors
const handleFavouriteError = (errorMessage) => {
	favouriteError.style.display = '';
	favouriteErrorMessage.textContent = errorMessage;
};
// two variables to hold the resolve and reject functions of our Promise
let resolveFavouriteVerseSelection, rejectFavouriteVerseSelection;
// function to try and set the linker access
const setGetBibleFavouriteVerse = () => {
	UIkit.modal('#getbible_favourite_verse_selector').show();
	return new Promise((resolve, reject) => {
		resolveFavouriteVerse = resolve;
		rejectFavouriteVerse = reject;
	});
};
newFavouriteSession.addEventListener('click', async () => {
	try {
		favouriteError.style.display = 'none';
		// build selected verse values
		let selection = favouriteBook.value + ' ' + favouriteChapter.value + ':' + favouriteVerse.value;
		let selectedBookName = getFavouriteBookName(favouriteBook.value);
		if (selectedBookName) {
			selection = '<br /><br /><b>' + selectedBookName + ' ' + favouriteChapter.value + ':' + favouriteVerse.value + '</b><br />';
		}
		// trigger load of new authenticated session
		UIkit.modal.confirm('<br /><?php echo JText::_('COM_GETBIBLE_YOU_ARE_ABOUT_TO_LOAD_A_BNEW_PERSISTENT_SESSIONB_LINKED_TO_THIS_FAVOURITE_VERSE'); ?>' + ': ' + selection + '<br /><?php echo JText::_('COM_GETBIBLE_DO_NOT_FORGET_THIS_SELECTED_VERSE_AS_IT_WILL_BE_NEEDED_TO_OPEN_THIS_NEW_SESSION_IN_THE_FUTURE'); ?><br />').then( async function() {
			let pass = favouriteBook.value + '_' + favouriteChapter.value + '_' + favouriteVerse.value;
			let linker = '<?php echo $this->linker_new; ?>';
			const setData = await setLinker(linker);
			if (setData.error) {
				handleFavouriteError(data.error);
				rejectFavouriteVerse(data.error);
			} else {
				const data = await setLinkerAccess(linker, pass);
				if (data.success) {
					updateUIAfterSettingFavourite(linker, true);
					triggerGetBibleReload = true;
					resolveFavouriteVerse();
					UIkit.modal('#getbible_favourite_verse_selector').hide();
				} else if (data.error) {
					handleFavouriteError(data.error);
					rejectFavouriteVerse(data.error);
				} else {
					let error = "Unknown error occurred: " + JSON.stringify(data);
					console.error(error);
					rejectFavouriteVerse(error);
				}
			}
		}, function (error) {
			console.log('Loading new session cancelled.');
		});
	} catch (error) {
		console.error("Error occurred: ", error);
		rejectFavouriteVerse(error);
	}
});
favouriteSelection.addEventListener('click', async () => {
	try {
		favouriteError.style.display = 'none';
		// build selected verse values
		let selection = favouriteBook.value + ' ' + favouriteChapter.value + ':' + favouriteVerse.value;
		let selectedBookName = getFavouriteBookName(favouriteBook.value);
		if (selectedBookName) {
			selection = '<br /><br /><b>' + selectedBookName + ' ' + favouriteChapter.value + ':' + favouriteVerse.value + '</b><br />';
		}
		// trigger load of new authenticated session
		UIkit.modal.confirm('<br /><?php echo JText::_('COM_GETBIBLE_YOUR_FAVOURITE_VERSE_SELECTION'); ?>' + ': ' + selection).then( async function() {
			let pass = favouriteBook.value + '_' + favouriteChapter.value + '_' + favouriteVerse.value;
			let linker = favouriteLinker.value;
			const data = await setLinkerAccess(linker, pass);
			if (data.success) {
				updateUIAfterSettingFavourite(linker, true);
				resolveFavouriteVerse();
				UIkit.modal('#getbible_favourite_verse_selector').hide();
			} else if (data.error) {
				handleFavouriteError(data.error);
				rejectFavouriteVerse(data.error);
			} else {
				let error = "Unknown error occurred: " + JSON.stringify(data);
				console.error(error);
				rejectFavouriteVerse(error);
			}
		}, function (error) {
			console.log('Favourite verse selection cancelled.');
		});
	} catch (error) {
		console.error("Error occurred: ", error);
		rejectFavouriteVerse(error);
	}
});
cancelFavouriteSelection.addEventListener('click', () => {
	rejectFavouriteVerse('User cancelled the operation');
	UIkit.modal('#getbible_favourite_verse_selector').hide();
});
const setFavouriteVerseForBrowser = async () => {
	return new Promise(async (resolve, reject) => {
		try {
			await setGetBibleFavouriteVerse();
			resolve();
		} catch (err) {
			reject(err);
		}
	});
};
const getFavouriteBookName = (number) => {
	// Iterate through each book in the array
	for(let i = 0; i < favouriteBooks.length; i++) {
		// If the key of the current book matches the provided number
		if(favouriteBooks[i].key == number) {
			// Return the value (name) of the book
			return favouriteBooks[i].value;
		}
	}
	// If no book was found with the provided number, return null
	return null;
}
<?php if ($this->params->get('show_settings') == 1): ?>
const removeFavouriteVerseFromBrowser = () => {
	return new Promise(async (resolve, reject) => {
		try {
			let linker = getLocalMemory('getbible_active_linker_guid');
			if (linker) {
				let revoked = await revokeLinkerAccess(linker);
				if (revoked.success) {
					setLocalMemory(linker + '-validated', false);
					resolve();
				} else if (revoked.error) {
					reject(revoked.error);
				} else {
					reject("Unexpected response");
				}
			} else {
				reject("Linker is undefined");
			}
		} catch (err) {
			reject(err);
		}
	});
};
const lockSessionStatusAccess = async () => {
	sessionAccessStatusSwitch.setAttribute('uk-icon', 'icon: lock; ratio: 5');
	sessionAccessStatusSwitch.classList.remove('uk-text-success');
	sessionAccessStatusSwitch.setAttribute('uk-tooltip', '<?php echo JText::_('COM_GETBIBLE_ENABLE_EXCLUSIVE_ACCESS_TO_EDIT_YOUR_NOTES_AND_TAGS'); ?>');
};
const unlockSessionStatusAccess = async () => {
	sessionAccessStatusSwitch.setAttribute('uk-icon', 'icon: unlock; ratio: 6');
	sessionAccessStatusSwitch.classList.add('uk-text-success');
	sessionAccessStatusSwitch.setAttribute('uk-tooltip', '<?php echo JText::_('COM_GETBIBLE_REVOKE_EXCLUSIVE_ACCESS_TO_EDIT_YOUR_NOTES_AND_TAGS'); ?>');
};
const changeSessionStatusSwitch = async () => {
	var isLocked = sessionAccessStatusSwitch.getAttribute('uk-icon') === "icon: lock; ratio: 5";
	if(isLocked) {
		setFavouriteVerseForBrowser().then(() => {
			unlockSessionStatusAccess();
		}).catch((error) => {
			console.error("An error occurred:", error);
		});
	} else {
		removeFavouriteVerseFromBrowser().then(() => {
			lockSessionStatusAccess()
		}).catch((error) => {
			console.error("An error occurred:", error);
		});
	}
};
const setSessionStatusAccess = async () => {
	// check if we have an open or closed session
	let linker = getLocalMemory('getbible_active_linker_guid');
	if (linker) {
		let access = await isLinkerAuthenticated(linker);
		if (access.success && access.status) {
			unlockSessionStatusAccess();
		} else {
			lockSessionStatusAccess();
		}
	} else {
		lockSessionStatusAccess();
	}
};
document.addEventListener('DOMContentLoaded', function() {
	setSessionStatusAccess();
	sessionAccessStatusSwitch.addEventListener('click', function() {
		changeSessionStatusSwitch();
	});
});
<?php endif; ?>
</script>
