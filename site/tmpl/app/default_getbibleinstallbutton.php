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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die;

?>
<?php if ($this->params->get('show_install_button') == 1): ?>
<?php $status = '<span id="getbible-install-progress-verse-total">' . $this->totalVerse . '<span>'; ?>
<p><?php echo Text::sprintf('COM_GETBIBLE_WE_CURRENTLY_HAVE_BSB_VERSES_STORED_IN_THE_DATABASE_FOR_THIS_PARTICULAR_TRANSLATION', $status); ?></p>
<progress id="getbible-install-progress" class="uk-progress uk-width-1-1" value="0" max="100"  style="display:none;"></progress>
<div id="getbible-install-success" class="uk-alert-success" style="display:none;" uk-alert>
	<a class="uk-alert-close" uk-close></a>
	<h2><?php echo Text::_('COM_GETBIBLE_INSTALLATION_OF_TRANSLATION_COMPLETE'); ?></h2>
	<p><?php echo Text::_("COM_GETBIBLE_THIS_INSTALLATION_PROCESS_IS_A_ONETIME_OPERATION_FOR_EACH_TRANSLATION_HOWEVER_YOU_ARE_WELCOME_TO_RUN_THE_PROCESS_A_SECOND_TIME_OR_AS_OFTEN_AS_YOUD_LIKE_TO_VALIDATE_AND_ENSURE_THAT_ALL_CONTENT_WAS_PROPERLY_LOADED_AND_IS_UPTODATE"); ?></p>
	<p><?php echo Text::_("COM_GETBIBLE_PERFORMING_MULTIPLE_INSTALLATIONS_WILL_NOT_CAUSE_ANY_DUPLICATES_OR_DATA_CLUTTER_THE_SYSTEM_INTELLIGENTLY_RECOGNIZES_ALREADY_INSTALLED_PARTS_AND_MERELY_REFRESHES_THEM_TO_VERIFY_THAT_THEY_ARE_IN_SYNC_WITH_THE_ORIGINAL_SOURCE"); ?></p>
	<p><?php echo Text::_("COM_GETBIBLE_ONCE_INSTALLED_YOUR_BIBLE_WILL_BE_EQUIPPED_WITH_AN_AUTOUPDATE_FEATURE_THIS_FEATURE_ACTIVELY_MONITORS_FOR_ANY_CHANGES_OR_UPDATES_IN_THE_ORIGINAL_SOURCE_AND_APPLIES_THEM_AUTOMATICALLY_THIS_ENSURES_THAT_YOUR_BIBLE_TRANSLATION_IS_ALWAYS_ACCURATE_AND_UPDATED_WITHOUT_REQUIRING_ANY_ADDITIONAL_MANUAL_INTERVENTION_FROM_YOU"); ?></p>
	<h4><?php echo Text::_("COM_GETBIBLE_DEACTIVATE_THIS_INSTALLATION_OPTION"); ?></h4>
	<p><?php echo Text::_("COM_GETBIBLE_ONCE_YOU_HAVE_COMPLETED_THE_INSTALLATION_OF_ALL_DESIRED_BIBLE_TRANSLATIONS_YOU_ARE_ADVISED_TO_DEACTIVATE_THE_INSTALLATION_OPTION_IN_THE_GETBIBLE_BACKEND_OF_YOUR_SYSTEM_THIS_DEACTIVATION_IS_A_CRITICAL_AND_HIGHLY_RECOMMENDED_STEP_THAT_HELPS_TO_MAINTAIN_SYSTEM_EFFICIENCY"); ?></p>
	<p><?php echo Text::_("COM_GETBIBLE_TO_PERFORM_THIS_OPEN_THE_GLOBAL_OPTIONS_SECTION_OF_THE_GETBIBLE_BACKEND_LOCATE_THE_GLOBAL_TAB_AND_SWITCH_SHOW_INSTALL_BUTTON_TO_NO_THIS_STEP_ENSURES_THAT_YOUR_SYSTEM_RESOURCES_ARE_NOT_UTILIZED_UNNECESSARILY_ONCE_YOU_HAVE_SUCCESSFULLY_INSTALLED_ALL_YOUR_DESIRED_BIBLE_TRANSLATIONS"); ?></p>
</div>
<button id="getbible-install-translation-now" class="uk-button uk-button-secondary uk-button-large uk-width-1-1"><?php echo Text::_('COM_GETBIBLE_INSTALL'); ?> <?php echo $this->translation->translation; ?></button>
<div id="getbible-install-progress-viewing-box" style="height: 300px; overflow: auto; display:none;"><!-- the install messages will be given here --></div>
<script type="text/javascript">

class InstallGetBibleChapters {
	constructor(translation, progressBarId = 'getbible-install-progress', successMessageId = 'getbible-install-success') {

		this.translation = translation;
		this.getBible = 'https://api.getbible.net/v2';
		this.progressBar = document.getElementById(progressBarId);
		this.progressMessage = document.getElementById(progressBarId + '-viewing-box');
		this.progressVerseTotal = document.getElementById(progressBarId + '-verse-total');
		this.successMessage = document.getElementById(successMessageId);

		// hide the stuff not to be seen right now
		this.progressBar.style.display = 'none';
		this.successMessage.style.display = 'none';

		// reset the progress bar just incase
		this.progressBar.value = 0;
	}

	async fetchJson(url) {
		const response = await fetch(url);
		if (!response.ok) {
			throw new Error('HTTP error! status: ' + response.status);
		}

		const data = await response.json();

		if (data.success || data.error){
			this.updateProgressMessagePerChapter(data);
		}

		return data;
	}

	async updateProgressMessagePerChapter(message){
		// create new message element
		let newMessage = document.createElement('div');
		let text = '';

		if (message.success){
			newMessage.classList.add('uk-text-success');
			text = message.success;
			if (message.total) {
				this.progressVerseTotal.innerText = message.total;
			}
		} else if (message.error) {
			newMessage.classList.add('uk-text-danger');
			text = message.error;
		}
		newMessage.classList.add('uk-text-nowrap');
		newMessage.innerText = text;

		// if too many messages, remove the last one
		if (this.progressMessage.childElementCount >= 100){
			this.progressMessage.removeChild(this.progressMessage.lastChild);
		}

		// add new message at the top
		this.progressMessage.prepend(newMessage);
	}

	async getBooks() {
		const url = this.getBible + '/' + this.translation + '/books.json';
		return await this.fetchJson(url);
	}

	async getChapters(bookNr) {
		const url = this.getBible + '/' + this.translation + '/' + bookNr + '/chapters.json';
		return await this.fetchJson(url, bookNr);
	}

	getLoadUrl(translation, book, chapter) {
		return installBibleChapterURL(translation, book, chapter);
	}

	async getLoadUrls() {
		try {
			const books = await this.getBooks();
			this.progressBar.max = Object.keys(books).length;

			this.progressBar.style.display = '';
			this.progressMessage.style.display = '';

			// Define delay function
			function delay(t, v) {
				return new Promise(function(resolve) { 
					setTimeout(resolve.bind(null, v), t)
				});
			}

			// Rate limit parameter
			let delayBetweenRequests = 66; // delay in ms

			for (const book of Object.values(books)) {
				const chapters = await this.getChapters(book.nr);
				const loadUrls = Object.values(chapters).map((chapter) => this.getLoadUrl(this.translation, book.nr, chapter.chapter));

				for(let i = 0; i < loadUrls.length; i++) {
					await this.fetchJson(loadUrls[i]);
					await delay(delayBetweenRequests);  // Here is the delay
				}

				this.progressBar.value++;

				if(this.progressBar.value === this.progressBar.max){
					this.successMessage.style.display = "";
					setTimeout(() => {
						this.progressBar.remove();
						this.progressMessage.remove();
					}, 10000);
				}
			}
		} catch (error) {
			console.error(error);
		}
	}
}
// Trigger install
UIkit.util.ready(function () {
	const bibleApi = new InstallGetBibleChapters('<?php echo $this->translation->abbreviation; ?>');
	const installButton = document.getElementById('getbible-install-translation-now');
	installButton.addEventListener('click', function() {
		UIkit.modal.confirm('<?php echo Text::_('COM_GETBIBLE_YOU_ARE_ABOUT_TO_INITIATE_THE_INSTALLATION_PROCESS_FOR_THIS_TRANSLATION_PLEASE_NOTE_THIS_PROCEDURE_IS_QUITE_EXTENSIVE_AND_MAY_TAKE_A_SIGNIFICANT_AMOUNT_OF_TIME_TO_COMPLETE_DEPENDING_ON_YOUR_NETWORK_SPEED_IT_COULD_RANGE_FROM_SEVERAL_MINUTES_TO_A_FEW_HOURS_DURING_THIS_PERIOD_IT_IS_ESSENTIAL_THAT_YOU_DO_NOT_NAVIGATE_AWAY_FROM_THIS_PAGE_CLOSE_THE_BROWSER_OR_SHUT_DOWN_YOUR_COMPUTER_AS_THIS_COULD_INTERRUPT_THE_INSTALLATION_PROCESS_PLEASE_ENSURE_YOU_HAVE_A_STABLE_INTERNET_CONNECTION_AND_SUFFICIENT_TIME_BEFORE_PROCEEDING_WE_RECOMMEND_INITIATING_THIS_WHEN_YOU_DO_NOT_REQUIRE_IMMEDIATE_USE_OF_YOUR_DEVICE_ARE_YOU_SURE_YOU_WANT_TO_START_THE_INSTALLATION_NOW'); ?>').then(function() {
			installButton.style.display = 'none';
			bibleApi.getLoadUrls().catch((error) => console.error(error));
		}, function () {
			console.log('Installation cancelled.')
		});
	});
});
</script>
<?php endif; ?>
