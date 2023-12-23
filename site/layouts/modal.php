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
defined('JPATH_BASE') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use VDM\Joomla\Utilities\StringHelper;

$id = (isset($displayData['id'])) ? $displayData['id'] : StringHelper::random(7);
$full = (isset($displayData['full']) && $displayData['full']) ? true : false;
$header = (isset($displayData['header'])) ? $displayData['header'] : false;
$header_class = $displayData['header_class'] ?? 'uk-modal-title';
$header_class = (isset($displayData['header_class_other'])) ? $header_class . ' ' . $displayData['header_class_other'] : $header_class;
$body_class = (isset($displayData['body_class'])) ? ' class="' . $displayData['body_class'] . '"' : ' class="uk-modal-body"';
$content = (isset($displayData['content'])) ? $displayData['content'] : '';
$buttons = $displayData['buttons'] ?? null;
$buttons_class = $displayData['buttons_class'] ?? '';
$buttons_id = $displayData['buttons_id'] ?? '';
$close = (isset($displayData['close']) && !$displayData['close']) ? false : true;
$overflow = (isset($displayData['overflow']) && !$displayData['overflow']) ? '' : ' uk-overflow-auto';
$dialog_class = (isset($displayData['dialog_class']) && $displayData['dialog_class']) ? $displayData['dialog_class'] : 'uk-modal-dialog';
// set the full modal behavior
$modal_class = (isset($displayData['modal_class'])) ? ' class="' . $displayData['modal_class'] . '"' : '';
$class_close = ' class="uk-modal-close-default"';
if ($full)
{
	$modal_class = $displayData['modal_class'] ?? '';
	$modal_class = ' class="uk-modal-full ' . $modal_class . '"';
	$class_close = ' class="uk-modal-close-full uk-close-large"';
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
				?>
				<button id="<?php echo $id_; ?>" class="<?php echo $class; ?>" type="button"<?php echo $onclick; ?>><?php echo $name; ?></button>
			<?php endforeach; ?>
			<?php if (!empty($buttons_class) || !empty($buttons_id)): ?>
				</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>

