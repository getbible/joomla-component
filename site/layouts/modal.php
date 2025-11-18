<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.life>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/



use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianBible\Component\GetBible\Site\Helper\GetbibleHelper;
use TrueChristianBible\Joomla\Utilities\StringHelper;

// No direct access to this file
defined('JPATH_BASE') or die;

// Extract all keys from $displayData as individual variables.
extract($displayData);

// Assign default values for variables that might not be present in $displayData.

// The 'id' parameter, defaulting to a random string if not set.
$id ??= StringHelper::random(7);

// The 'full' parameter, defaulting to false if not set or is null.
$full = (isset($full) && $full) ? true : false;

// The 'header' parameter, defaulting to false if not set or is null.
$header ??= false;

// The 'header_class' parameter, defaulting to 'uk-modal-title' if not set or is null.
$header_class ??= 'uk-modal-title';

// The 'header_class_other' parameter, if set, appends additional class to 'header_class', otherwise retains original 'header_class'.
$header_class = isset($header_class_other) ? $header_class . ' ' . $header_class_other : $header_class;

// The 'body_class' parameter, added if set, otherwise defaults to 'uk-modal-body'.
$body_class = isset($body_class) ? ' class="' . $body_class . '"' : ' class="uk-modal-body"';

// The 'content' parameter, defaulting to an empty string if not set.
$content ??= '';

// The 'buttons' parameter, defaulting to null if not set.
$buttons ??= null;

// The 'buttons_class' parameter, defaulting to an empty string if not set.
$buttons_class ??= '';

// The 'buttons_id' parameter, defaulting to an empty string if not set.
$buttons_id ??= '';

// The 'close' parameter, set to false if explicitly set to false, otherwise defaults to true.
$close = isset($close) && !$close ? false : true;

// The 'overflow' parameter, defaulting to 'uk-overflow-auto' unless set to false.
$overflow = isset($overflow) && !$overflow ? '' : ' uk-overflow-auto';

// The 'dialog_class' parameter, defaulting to 'uk-modal-dialog' if not set or empty.
$dialog_class ??= 'uk-modal-dialog';

// Set the full modal behavior when 'full' is true.
if ($full)
{
	// The 'modal_class' parameter, defaulting to an empty string if not set.
	$modal_class = $modal_class ?? '';

	// The 'modal_class' is wrapped with full modal classes if 'full' is true.
	$modal_class = ' class="uk-modal-full ' . $modal_class . '"';

	// Change 'class_close' to the full modal close button style when 'full' is true.
	$class_close = ' class="uk-modal-close-full uk-close-large"';
}
else
{
	// The 'modal_class' parameter, defaulting to an empty string unless provided.
	$modal_class = isset($modal_class) ? ' class="' . $modal_class . '"' : '';

	// The default close button class.
	$class_close = ' class="uk-modal-close-default"';
}

?>
<div id="<?php echo $id; ?>"<?php echo $modal_class; ?> uk-modal>
	<div class="<?php echo $dialog_class; ?>">

		<?php if ($close): ?><button<?php echo $class_close; ?> type="button" uk-close></button><?php endif; ?>

		<?php if ($header): ?>
			<?php if (strpos($header, 'uk-navbar') !== false || strpos($header, 'uk-modal-header') !== false): ?>
				<?php echo $header; ?>
			<?php else: ?>
				<div class="uk-modal-header">
					<h2 class="<?php echo $header_class; ?>"><?php echo $header; ?></h2>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<div<?php echo $body_class; ?><?php echo $overflow; ?>>
			<?php echo $content; ?>
		</div>

		<?php if ($buttons !== null): ?>
		<div class="uk-modal-footer uk-text-right">
			<?php if (!empty($buttons_class) || !empty($buttons_id)): ?>
				<div class="<?php echo $buttons_class; ?>" id="<?php echo $buttons_id; ?>">
			<?php endif; ?>
			<?php foreach ($buttons as $button): ?>
				<?php
					$id_ = $button['id'] ?? StringHelper::random(7);
					$class =  $button['class'] ?? 'uk-button uk-button-default';
					$class .= (isset($button['close']) && $button['close']) ? ' uk-modal-close' : '';
					$name = (isset($button['name'])) ? $button['name'] : ((isset($button['close']) && $button['close']) ? Text::_('COM_GETBIBLE_CANCEL') : Text::_('COM_GETBIBLE_SAVE'));
					$onclick = (isset($button['onclick'])) ? ' onclick="' . $button['onclick'] . '"' : '';
					$disabled = !empty($button['readonly']) || !empty($button['disabled']) ? ' disabled="disabled"' : '';
				?>
				<button id="<?php echo $id_; ?>" class="<?php echo $class; ?>" type="button"<?php echo $onclick . $disabled; ?>><?php echo $name; ?></button>
			<?php endforeach; ?>
			<?php if (!empty($buttons_class) || !empty($buttons_id)): ?>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>

