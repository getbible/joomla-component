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
	['name' => JText::_('COM_GETBIBLE_SELECT'), 'onclick' => "activeLinkerAccess();", 'class' => 'uk-button uk-button-default uk-width-2-3'],
	['close' => true, 'name' => JText::_('COM_GETBIBLE_CANCEL'), 'class' => 'uk-button uk-button-danger uk-width-1-3']
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

// function to try and set the linker access
const activeLinkerAccess = async () => {
	try {
		// create the pass key
		favouriteError.style.display = 'none';
		let pass = favouriteBook.value + '_' + favouriteChapter.value + '_' + favouriteVerse.value;
		let linker = favouriteLinker.value;

		// Wait for the server to return the response, then parse it as JSON.
		const data = await setLinkerAccess(linker, pass);

		// Call another function after the response has been received
		if (data.success) {
			// set the linker global
			favouriteError.style.display = 'none';
			setActiveLinkerOnPage(linker);
			setLocalMemory('getbible_active_linker_guid', linker);
			setLocalMemory(linker, pass);
			UIkit.modal('#getbible_favourite_verse_selector').hide();
		} else if (data.error) {
			// show the error
			favouriteError.style.display = '';
			favouriteErrorMessage.textContent = data.error;
		} else {
			// Handle any errors
			console.error("Error occurred: ", data);
		}
	} catch (error) {
		// Handle any errors
		console.error("Error occurred: ", error);
	}
}
</script>
