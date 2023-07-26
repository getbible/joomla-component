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
<?php if ($this->params->get('show_hash_validation') == 1): ?>
	<div class="uk-float-left">
		<a class="uk-link-muted uk-margin" href="#getbible-hash-details" uk-toggle title="<?php echo JText::_('COM_GETBIBLE_WHAT_IS_THIS_HASH_ALL_ABOUT_CLICK_HERE_TO_READ_MORE'); ?>"><span uk-icon="icon: question"></span></a>
		<?php if ($this->params->get('show_api_link') == 1): ?>
			<a class="uk-link-muted" href="https://api.getbible.net/v2/<?php echo $this->chapter->abbreviation; ?>/<?php echo $this->chapter->book_nr; ?>/<?php echo $this->chapter->chapter; ?>.json" target="_blank" title="API Source"><?php echo JText::_('API'); ?>: <?php echo $this->chapter->name; ?> <span uk-icon="icon: chevron-double-right"></span></a>
		<?php endif; ?>
		<a class="uk-link-muted" href="https://git.vdm.dev/getBible/v2/src/branch/master/<?php echo $this->chapter->abbreviation; ?>/<?php echo $this->chapter->book_nr; ?>/<?php echo $this->chapter->chapter; ?>.sha" target="_blank" title="<?php echo JText::_('COM_GETBIBLE_VALIDATION_HASH_OF_THIS_CHAPTER'); ?>"><?php echo $this->chapter->sha; ?> <span uk-icon="icon: check"></span></a>
	</div>
	<div id="getbible-hash-details" class="uk-modal-container" uk-modal>
		<div class="uk-modal-dialog uk-modal-body">
			<button class="uk-modal-close-default" type="button" uk-close></button>
			<div class="uk-modal-header">
				<h2 class="uk-modal-title"><?php echo JText::_('COM_GETBIBLE_BASIC_HASH_USAGE_EXPLAINED'); ?></h2>
			</div>
			<div class="uk-modal-body" uk-overflow-auto>
				<?php echo JLayoutHelper::render('getbiblehashdetails', []); ?>
			</div>
		</div>
	</div>
<?php elseif ($this->params->get('show_api_link') == 1): ?>
	<div class="uk-float-left">
		<a class="uk-link-muted" href="https://api.getbible.net/v2/<?php echo $this->chapter->abbreviation; ?>/<?php echo $this->chapter->book_nr; ?>/<?php echo $this->chapter->chapter; ?>.json" target="_blank" title="<?php echo JText::_('COM_GETBIBLE_API_SOURCE'); ?>"><?php echo JText::_('API'); ?>: <?php echo $this->chapter->name; ?></a>
	</div>
<?php endif; ?>
<?php echo JLayoutHelper::render('getbiblefooter', [
	'load' => $this->params->get('show_getbible_link'),
	'path' => $this->chapter->abbreviation . '/' . $this->chapter->book_name . '/' . $this->chapter->chapter,
	'logo' => $this->params->get('show_getbible_logo')
]); ?>
