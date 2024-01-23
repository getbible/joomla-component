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
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;

// chapters
$chapters = array_map( function ($item) {
	return (object) ['key' => $item, 'value' => $item];
}, range(1, 150));
// verses
$verses = array_map( function ($item) {
	return (object) ['key' => $item, 'value' => $item];
}, range(1, 180));

?>
<div class="uk-alert-warning" id="getbible_favourite_error" uk-alert style="display:none">
	<p id="getbible_favourite_error_message"></p>
</div>
<p class="uk-text-emphasis uk-text-center">
	<?php echo Text::_('COM_GETBIBLE_YOU_SHOULD_SELECT_ONE_OF_BYOUR_FAVOURITEB_VERSES'); ?>
</p>
<p class="uk-text-muted uk-text-center">
	<?php echo Text::_('COM_GETBIBLE_THIS_VERSE_IN_COMBINATION_WITH_YOUR_ISESSION_KEYI_WILL_BE_USED_TO_AUTHENTICATE_YOU_IN_THE_FUTURE'); ?>
</p>
<div class="uk-child-width-expand uk-text-center" uk-grid>
	<div>
		<div class="uk-card">
			<?php echo JLayoutHelper::render('selectbox', [
				'id' => 'getbible_favourite_book',
				'label' => Text::_('COM_GETBIBLE_BOOKS'),
				'options' =>  $displayData['book_options'],
				'default' =>  $displayData['book_default']
			]); ?>
		</div>
	</div>
	<div>
		<div class="uk-card">
			<?php echo JLayoutHelper::render('selectbox', [
				'id' => 'getbible_favourite_chapter',
				'label' => Text::_('COM_GETBIBLE_CHAPTERS'),
				'options' => $chapters,
				'default' =>  $displayData['chapter_default']
			]); ?>
		</div>
	</div>
	<div>
		<div class="uk-card">
			<?php echo JLayoutHelper::render('selectbox', [
				'id' => 'getbible_favourite_verse',
				'label' => Text::_('COM_GETBIBLE_VERSES'),
				'options' => $verses,
				'default' => $displayData['verse_default']
			]); ?>
		</div>
	</div>
</div>
<p class="uk-text-emphasis uk-text-center">
	<?php echo Text::_('COM_GETBIBLE_THIS_IS_CURRENTLY_THE_ACTIVE_SESSION_KEY'); ?>
</p>
<div class="uk-child-width-expand uk-text-center" uk-grid>
	<?php echo JLayoutHelper::render('inputbox', [
		'id' => 'getbible_favourite_linker',
		'class_other' => 'getbible-linker-guid-input uk-text-center',
		'label' => Text::_('COM_GETBIBLE_SESSION_KEY'),
		'class_other_label' => 'getbible-linker-name-value',
		'placeholder' => Text::_('COM_GETBIBLE_AUTO_GENERATED')
	]); ?>
</div>
<p class="uk-text-muted">
	<?php echo Text::_('COM_GETBIBLE_SHOULD_YOU_HAVE_BANOTHER_SESSION_KEYB_FROM_A_PREVIOUS_SESSION'); ?><br />
	<?php echo Text::_('COM_GETBIBLE_YOU_CAN_ADD_IT_HERE_TO_LOAD_YOUR_PREVIOUS_SESSION'); ?>
</p>
