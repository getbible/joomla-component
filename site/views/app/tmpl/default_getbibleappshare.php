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
<div id="getbible-app-sharing" class="uk-modal-container" uk-modal>
	<div class="uk-modal-dialog uk-modal-body">
		<button class="uk-modal-close-default" type="button" uk-close></button>
		<div class="uk-margin uk-margin-remove-top">
			<h3 class="uk-modal-title uk-margin-remove"><?php echo $this->chapter->book_name; ?> <?php echo $this->chapter->chapter; ?>:<span id="share-getbible-verse"></span> <span uk-icon="icon: forward; ratio: 1.5"></span></h3>
			<span class="uk-text-small uk-text-muted uk-margin-remove"><?php echo Text::_('COM_GETBIBLE_SHARING_THE_WORD_OF_GOD_WITH_THE_WORLD'); ?></span>
		</div>
		<div class="uk-padding uk-padding-remove-bottom">
			<div id="verse-share-selection-slider"></div>
		</div>
		<div class="uk-margin">
			<ul uk-accordion>
				<li class="uk-open">
					<a class="uk-accordion-title" href="#"><span uk-icon="icon: social"></span> <?php echo Text::_('COM_GETBIBLE_SHARE_TEXT'); ?></a>
					<div class="uk-accordion-content uk-padding-remove-horizontal">
						<div class="uk-child-width-1-1" uk-grid>
						<form id="getbible-share">
							<button class="uk-button  uk-width-1-1 uk-button-small" uk-toggle="target: #advance-options" type="button"><?php echo Text::_('COM_GETBIBLE_ADVANCE_OPTIONS'); ?></button>
							<div id="advance-options" class="uk-grid-small uk-margin-small uk-margin-remove-bottom uk-child-width-1-1@s uk-child-width-1-2@m uk-child-width-1-3@l uk-text-center"  hidden uk-grid>
								<div>
									<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
										<label class="uk-form-label" for="getbible-layout-share">
											<?php echo Text::_('COM_GETBIBLE_LAYOUT'); ?>
										</label>
										<div class="uk-form-controls">
											<select class="uk-select" id="getbible-layout-share">
												<option value="1" selected>
													<?php echo Text::_('COM_GETBIBLE_PARAGRAPH'); ?>
												</option>
												<option value="2">
													<?php echo Text::_('COM_GETBIBLE_PER_LINE'); ?>
												</option>
											</select>
										</div>
									</div>
								</div>
								<div>
									<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
										<label class="uk-form-label" for="getbible-number-share">
											<?php echo Text::_('COM_GETBIBLE_VERSE_NUMBER'); ?>
										</label>
										<div class="uk-form-controls">
											<select class="uk-select" id="getbible-number-share">
												<option value="1" selected>
													<?php echo Text::_('COM_GETBIBLE_SHOW'); ?>
												</option>
												<option value="2">
													<?php echo Text::_('COM_GETBIBLE_HIDE'); ?>
												</option>
											</select>
										</div>
									</div>
								</div>
								<div>
									<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
										<label class="uk-form-label" for="getbible-link-share">
											<?php echo Text::_('COM_GETBIBLE_LINK'); ?>
										</label>
										<div class="uk-form-controls">
											<select class="uk-select" id="getbible-link-share">
												<option value="1">
													<?php echo Text::_('COM_GETBIBLE_ADD'); ?>
												</option>
												<option value="2" selected>
													<?php echo Text::_('COM_GETBIBLE_REMOVE'); ?>
												</option>
											</select>
										</div>
									</div>
								</div>
								<div>
									<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
										<label class="uk-form-label" for="getbible-reference-share">
												<?php echo Text::_('COM_GETBIBLE_REFERENCE'); ?>
										</label>
										<div class="uk-form-controls">
											<select class="uk-select" id="getbible-reference-share">
												<option value="1">
													<?php echo Text::_('COM_GETBIBLE_NONE'); ?>
												</option>
												<option value="2">
													<?php echo Text::_('COM_GETBIBLE_TOP'); ?>
												</option>
												<option value="3" selected>
													<?php echo Text::_('COM_GETBIBLE_BOTTOM'); ?>
												</option>
											</select>
										</div>
									</div>
								</div>
								<div>
									<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
										<label class="uk-form-label" for="getbible-translation-share">
											<?php echo Text::_('COM_GETBIBLE_TRANSLATION'); ?>
										</label>
										<div class="uk-form-controls">
											<select class="uk-select" id="getbible-translation-share">
												<option value="1">
													<?php echo Text::_('COM_GETBIBLE_NONE'); ?>
												</option>
												<option value="2" selected>
													<?php echo Text::_('COM_GETBIBLE_ABBREVIATION'); ?>
												</option>
												<option value="3">
													<?php echo Text::_('COM_GETBIBLE_NAME'); ?>
												</option>
											</select>
										</div>
									</div>
								</div>
								<div>
									<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
										<label class="uk-form-label" for="getbible-format-share">
											<?php echo Text::_('COM_GETBIBLE_FORMAT'); ?>
										</label>
										<div class="uk-form-controls">
											<select class="uk-select" id="getbible-format-share">
												<option value="1" selected>
													<?php echo Text::_('COM_GETBIBLE_PLAIN_TEXT'); ?>
												</option>
												<option value="2">
													<?php echo Text::_('COM_GETBIBLE_MARKDOWN'); ?>
												</option>
												<option value="3">
													<?php echo Text::_('HTML'); ?>
												</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							</form>
						</div>
						<div class="uk-child-width-1-1 uk-text-center uk-margin-small-top" uk-grid>
							<div>
								<div id="getbible-text-share" class="uk-box-shadow-small uk-padding-small uk-margin uk-panel uk-panel-scrollable uk-height-medium direction-<?php echo strtolower($this->translation->direction); ?>">...</div>
								<button id="copy-share-getbible-text" class="uk-button  uk-width-1-1 uk-button-default"><?php echo Text::_('COM_GETBIBLE_COPY'); ?></button>
							</div>
						</div>
					</div>
				</li>
				<li>
					<a class="uk-accordion-title" href="#"><span uk-icon="icon: link"></span> <?php echo Text::_('COM_GETBIBLE_SHARE_LINK'); ?></a>
					<div class="uk-accordion-content uk-padding-remove-horizontal">
						<div class="uk-child-width-1-1 uk-text-center" uk-grid>
							<div>
								<div class="uk-box-shadow-small uk-padding uk-margin uk-text-small uk-text-nowrap"><span id="getbible-link-share-url"></span></div>
								<button id="copy-share-getbible-link" class="uk-button  uk-width-1-1 uk-button-default"><?php echo Text::_('COM_GETBIBLE_COPY'); ?></button>
							</div>
						</div>
					</div>
				</li>
			</ul>
		</div>
		<?php $this->modalState->main = 'sharing'; ?>
		<?php $this->modalState->one = 'notes'; ?>
		<?php $this->modalState->oneText = Text::_('COM_GETBIBLE_NOTES'); ?>
		<?php $this->modalState->two = 'tags'; ?>
		<?php $this->modalState->twoText = Text::_('COM_GETBIBLE_TAGS'); ?>
		<?php echo $this->loadTemplate('getbibleappmodalbottom'); ?>
	</div>
</div>
<script type="text/javascript">
// check if we have values in memory
var tmp = getLocalMemory('getbible_format_share');
if (tmp === null) {
	tmp = {
		"getbible-layout-share": "<?php echo $this->params->get('verse_layout_share', 1); ?>",
		"getbible-number-share": "<?php echo $this->params->get('verse_number_share', 1); ?>",
		"getbible-link-share": "<?php echo $this->params->get('local_link_share', 2); ?>",
		"getbible-reference-share": "<?php echo $this->params->get('text_reference_share', 3); ?>",
		"getbible-translation-share": "<?php echo $this->params->get('type_translation_share', 2); ?>",
		"getbible-format-share": "<?php echo $this->params->get('default_format_share', 1); ?>"
	};
	setLocalMemory('getbible_format_share', tmp);
}
// keep fields in sync with selected state of the share format
for (let id in tmp) {
	let selectField = document.querySelector('#getbible-share #' + id);
	if (selectField) {
		selectField.value = tmp[id];
	}
}
// load the share verse slider
var getbibleShareVerseSlider = document.getElementById('verse-share-selection-slider');
noUiSlider.create(getbibleShareVerseSlider, {
	start: [<?php echo $this->verses->first; ?>, <?php echo $this->verses->last; ?>],
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
document.getElementById('copy-share-getbible-link').onclick = function() {
	var textToCopy = document.getElementById('getbible-link-share-url').textContent;

	try {
		navigator.clipboard.writeText(textToCopy).then(function() {
			// close the modal
			UIkit.modal('#getbible-app-sharing').hide();
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THE_LINK_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
				status: 'success',
				timeout: 5000
			});
		}, function(err) {
			console.error('Could not copy text: ', err);
		});
	} catch (err) {
		// Fallback for browsers that do not support clipboard API
		const textarea = document.createElement("textarea");
		textarea.textContent = textToCopy;
		document.body.appendChild(textarea);
		textarea.select();
		try {
			document.execCommand("copy");
			// close the modal
			UIkit.modal('#getbible-app-sharing').hide();
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THE_LINK_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
				status: 'success',
				timeout: 5000
			});
		} catch (err) {
			console.error('Failed to copy: ', err);
		} finally {
			document.body.removeChild(textarea);
		}
	}
}
document.getElementById('copy-share-getbible-text').onclick = function() {
	var textToCopy = document.getElementById('getbible-text-share').innerHTML;

	try {
		navigator.clipboard.writeText(textToCopy).then(function() {
			// close the modal
			UIkit.modal('#getbible-app-sharing').hide();
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THE_SCRIPTURE_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
				status: 'success',
				timeout: 5000
			});
		}, function(err) {
			console.error('Could not copy text: ', err);
		});
	} catch (err) {
		// Fallback for browsers that do not support clipboard API
		const textarea = document.createElement("textarea");
		textarea.textContent = textToCopy;
		document.body.appendChild(textarea);
		textarea.select();
		try {
			document.execCommand("copy");
			// close the modal
			UIkit.modal('#getbible-app-sharing').hide();
			// Show message
			UIkit.notification({
				message: '<?php echo Text::_('COM_GETBIBLE_THE_SCRIPTURE_WAS_COPIED_TO_YOUR_CLIPBOARD'); ?>',
				status: 'success',
				timeout: 5000
			});
		} catch (err) {
			console.error('Failed to copy: ', err);
		} finally {
			document.body.removeChild(textarea);
		}
	}
}
function setSharedValues(start, end, update = true) {
	tmp = getLocalMemory('getbible_format_share');
	let shouldAdd = false;
	let resultString = getbible_verses.reduce((accumulator, obj) => {
		if (obj.verse == start) {
			shouldAdd = true;
		}
		if (shouldAdd) {
			if (tmp["getbible-number-share"] === "1") {
				if (tmp["getbible-layout-share"] === "2") {
					if (tmp["getbible-format-share"] === "3") {
						accumulator += obj.verse + '. ' + obj.text.trim() + " <br />\n";
					} else {
						accumulator += obj.verse + '. ' + obj.text.trim() + " \n";
					}
				} else {
					accumulator += obj.verse + '. ' + obj.text.trim() + ' ';
				}
			} else {
				if (tmp["getbible-layout-share"] === "2") {
					if (tmp["getbible-format-share"] === "3") {
						accumulator += obj.text.trim() + " <br />\n";
					} else {
						accumulator += obj.text.trim() + " \n";
					}
				} else {
					accumulator += obj.text.trim() + ' ';
				}
			}
		}
		if (obj.verse == end) {
			shouldAdd = false;
		}
		return accumulator;
	}, '');

	var verse_ref = start + '-' + end;
	if (start == end) {
		verse_ref = end;
	}
	let share_url = getbible_page_url + verse_ref;

	var trans_ref = '';
	if (tmp["getbible-translation-share"] === "2") {
		trans_ref = ' (' + <?php echo json_encode($this->chapter->abbreviation); ?> + ')';
	} else if (tmp["getbible-translation-share"] === "3") {
		trans_ref = ' ' + <?php echo json_encode($this->chapter->translation); ?>;
	}

	var breaking_val = ' ';
	if (tmp["getbible-layout-share"] === "2") {
		if (tmp["getbible-format-share"] === "3") {
			breaking_val = " <br />\n";
		} else {
			breaking_val = " \n";
		}
	}

	var name_ref = '';
	if (tmp["getbible-reference-share"] === "2" ||
		tmp["getbible-reference-share"] === "3") {
		name_ref = <?php echo json_encode($this->chapter->name); ?> + ':' + verse_ref + trans_ref;
		if (tmp["getbible-link-share"] === "1") {
			if (tmp["getbible-format-share"] === "3") {
				name_ref = '<a href="' + share_url + '">' + name_ref + '</a>';
			} else if (tmp["getbible-format-share"] === "2") {
				name_ref = '[' + name_ref + '](' + share_url + ')';
			} else {
				if (tmp["getbible-reference-share"] === "2") {
					name_ref = share_url + breaking_val + name_ref;
				} else {
					name_ref += breaking_val + share_url;
				}
			}
		}
	}

	// Replace double <br> or <br /> (with optional spaces) at the end of a string with a single one
	resultString = resultString.replace(/(\s*<br\s*\/?>\s*){2,}$/, "").replace(/(\s*<br\s*\/?>\s*)$/, "");
	var share_text = resultString.trim();
	if (tmp["getbible-reference-share"] === "2") {
		share_text = name_ref + breaking_val + share_text;
	} else if (tmp["getbible-reference-share"] === "3") {
		share_text += breaking_val + '~ ' + name_ref;
	}

	if (tmp["getbible-format-share"] === "3") {
		share_text = '<p>' + share_text + '</p>';
	}

	document.getElementById('getbible-text-share').innerHTML = share_text;
	document.getElementById('getbible-link-share-url').textContent = share_url;
	document.getElementById('share-getbible-verse').textContent = verse_ref;
	if (update) {
		getbibleShareVerseSlider.noUiSlider.set([start, end]);
	} else {
		//let function_call = 'setSharedValues(' + start + ',' + end + ')';
		//let functionCall = document.getElementById('getbible-main-sharing-button');
		//functionCall.setAttribute('onclick', function_call);
	}
}
getbibleShareVerseSlider.noUiSlider.on('update', function(values, handle) {
	let min_value = Math.round(values[0]);
	let max_value = Math.round(values[1]);
	var share_verse = min_value + '-' + max_value;
	var share_url = getbible_page_url + min_value + '-' + max_value;
	if (min_value == max_value) {
		share_url = getbible_page_url + min_value;
		share_verse = min_value;
	}
	document.getElementById('getbible-link-share-url').textContent = share_url;
	document.getElementById('share-getbible-verse').textContent = share_verse;
	setSharedValues(min_value, max_value, false);
});
document.querySelectorAll('#getbible-share select').forEach((selectElement) => {
	selectElement.addEventListener('change', (event) => {
		let form_values = Array.from(document.querySelectorAll('#getbible-share select')).reduce((acc, select) => {
			acc[select.id] = select.value;  // use the field ID as the key
			return acc;
		}, {});
		setLocalMemory('getbible_format_share', form_values);
		let values = getbibleShareVerseSlider.noUiSlider.get();
		setSharedValues(values[0], values[1], false);
	});
});
</script>
