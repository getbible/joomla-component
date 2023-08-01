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

// set content
$options = array_map( function ($item) {
	return (object) ['key' => $item->nr, 'value' => $item->name];
}, $this->books);
$content = '<div class="uk-alert-success" id="getbible_favourite_error" uk-alert style="display:none"><p id="getbible_favourite_error_message"></p></div>';
$content .= '<p class="uk-text-emphasis uk-text-center">' . JText::_('COM_GETBIBLE_YOU_SHOULD_SELECT_ONE_OF_BYOUR_FAVOURITEB_VERSES') . '</p>';
$content .= '<p class="uk-text-muted uk-text-center">' . JText::_('COM_GETBIBLE_THIS_VERSE_IN_COMBINATION_WITH_YOUR_ISESSION_KEYI_WILL_BE_USED_TO_AUTHENTICATE_YOU_IN_THE_FUTURE') . '</p>';
$content .= '<div class="uk-child-width-expand uk-text-center" uk-grid>';
$content .= '<div><div class="uk-card">';
$content .= JLayoutHelper::render('selectbox', ['id' => 'getbible_favourite_book', 'label' => JText::_('COM_GETBIBLE_BOOKS'), 'options' => $options, 'default' => $this->chapter->book_nr]);
$content .= '</div></div>';
$options = array_map( function ($item) {
	return (object) ['key' => $item, 'value' => $item];
}, range(1, 150));
$content .= '<div><div class="uk-card">';
$content .= JLayoutHelper::render('selectbox', ['id' => 'getbible_favourite_chapter', 'label' => JText::_('COM_GETBIBLE_CHAPTERS'), 'options' => $options, 'default' => $this->chapter->chapter]);
$content .= '</div></div>';
$options = array_map( function ($item) {
	return (object) ['key' => $item, 'value' => $item];
}, range(1, 176));
$content .= '<div><div class="uk-card">';
$content .= JLayoutHelper::render('selectbox', ['id' => 'getbible_favourite_verse', 'label' => JText::_('COM_GETBIBLE_VERSES'), 'options' => $options, 'default' => (string) $this->verses->first]);
$content .= '</div></div>';
$content .= '</div>';
$content .= '<p class="uk-text-emphasis uk-text-center">' . JText::_('COM_GETBIBLE_THIS_IS_CURRENTLY_THE_ACTIVE_SESSION_KEY') . '</p>';
$content .= '<div class="uk-child-width-expand uk-text-center" uk-grid>';
$content .= JLayoutHelper::render('inputbox', ['id' => 'getbible_favourite_linker', 'class_other' => 'getbible-linker-guid-input uk-text-center', 'label' => JText::_('COM_GETBIBLE_SESSION_KEY'), 'placeholder' => JText::_('COM_GETBIBLE_AUTO_GENERATED')]);
$content .= '</div>';
$content .= '<p class="uk-text-muted">' . JText::_('COM_GETBIBLE_SHOULD_YOU_HAVE_BANOTHER_SESSION_KEYB_FROM_A_PREVIOUS_SESSION') . '<br />' . JText::_('COM_GETBIBLE_YOU_CAN_ADD_IT_HERE_TO_LOAD_YOUR_PREVIOUS_SESSION') . '</p>';

// set buttons
$buttons = [
	['id' => 'getbible-select-favourite-verse', 'name' => JText::_('COM_GETBIBLE_SELECT'), 'class' => 'uk-button uk-button-default uk-width-2-3'],
	['id' => 'getbible-cancel-favourite-verse', 'close' => true, 'name' => JText::_('COM_GETBIBLE_CANCEL'), 'class' => 'uk-button uk-button-danger uk-width-1-3']
];


?>
<?php echo JLayoutHelper::render('modal', [
	'id' => 'getbible_favourite_verse_selector',
	'header' => JText::_('COM_GETBIBLE_FAVOURITE_VERSE'),
	'header_class_other' => 'uk-text-center',
	'close' => true,
	'content' => $content,
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
favouriteSelection.addEventListener('click', async () => {
	try {
		favouriteError.style.display = 'none';
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
		let pass = getLocalMemory(linker + '-validated');
		if (pass) {
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
