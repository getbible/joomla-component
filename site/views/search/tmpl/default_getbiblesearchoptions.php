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
<button class="uk-button  uk-width-1-1 uk-button-small" uk-toggle="target: #search-options" type="button"><?php echo Text::_('COM_GETBIBLE_SEARCH_OPTIONS'); ?> (<?php echo implode(', ', $this->getOptionsText()); ?>)</button>
<div id="search-options" class="uk-grid-small uk-width-1-1 uk-margin-small uk-margin-remove-bottom uk-child-width-1-1@s uk-child-width-1-3@m uk-child-width-1-5@l uk-text-center"  hidden uk-grid>
	<div>
		<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
			<label class="uk-form-label" for="getbible-search-translation">
				<?php echo Text::_('COM_GETBIBLE_TRANSLATION'); ?>
			</label>
			<div class="uk-form-controls">
				<select class="uk-select getbible-search-option" id="getbible-search-translation">
					<?php foreach($this->translations as $translation): ?>
						<option value="<?php echo $translation->abbreviation; ?>"<?php if ($translation->abbreviation == $this->translation->abbreviation) { echo ' selected'; } ?>>
							<?php echo $translation->translation; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<div>
		<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
			<label class="uk-form-label" for="getbible-search-words">
				<?php echo Text::_('COM_GETBIBLE_WORDS'); ?>
			</label>
			<div class="uk-form-controls">
				<select class="uk-select getbible-search-option" id="getbible-search-words">
					<option value="1"<?php if ($this->words == 1) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_ALL_WORDS'); ?>
					</option>
					<option value="2"<?php if ($this->words == 2) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_ANY_WORDS'); ?>
					</option>
					<option value="3"<?php if ($this->words == 3) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_EXACT_PHRASE'); ?>
					</option>
				</select>
			</div>
		</div>
	</div>
	<div>
		<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
			<label class="uk-form-label" for="getbible-search-match">
				<?php echo Text::_('COM_GETBIBLE_MATCH'); ?>
			</label>
			<div class="uk-form-controls">
				<select class="uk-select getbible-search-option" id="getbible-search-match">
					<option value="1"<?php if ($this->match == 1) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_EXACT'); ?>
					</option>
					<option value="2"<?php if ($this->match == 2) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_PARTIAL'); ?>
					</option>
				</select>
			</div>
		</div>
	</div>
	<div>
		<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
			<label class="uk-form-label" for="getbible-search-case">
				<?php echo Text::_('COM_GETBIBLE_CASE'); ?>
			</label>
			<div class="uk-form-controls">
				<select class="uk-select getbible-search-option" id="getbible-search-case">
					<option value="1"<?php if ($this->case == 1) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_INSENSITIVE'); ?>
					</option>
					<option value="2"<?php if ($this->case == 2) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_SENSITIVE'); ?>
					</option>
				</select>
			</div>
		</div>
	</div>
	<div>
		<div class="uk-card uk-card-default uk-card-body uk-padding-remove">
			<label class="uk-form-label" for="getbible-search-target">
					<?php echo Text::_('COM_GETBIBLE_TARGETED_BOOKS'); ?>
			</label>
			<div class="uk-form-controls">
				<select class="uk-select getbible-search-option" id="getbible-search-target">
					<option value="1000"<?php if ($this->target == 1000) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_ALL_BOOKS'); ?>
					</option>
					<option value="2000"<?php if ($this->target == 2000) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_OLD_TESTAMENT'); ?>
					</option>
					<option value="3000"<?php if ($this->target == 3000) { echo ' selected'; } ?>>
						<?php echo Text::_('COM_GETBIBLE_NEW_TESTAMENT'); ?>
					</option>
				</select>
			</div>
		</div>
	</div>
</div>
