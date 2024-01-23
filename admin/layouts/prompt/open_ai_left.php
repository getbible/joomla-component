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
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\Component\Helper;

// get the form
$form = $displayData->getForm();

// get the layout fields override method name (from layout path/ID)
$layout_path_array = explode('.', $this->getLayoutId());
// Since we cannot pass the layout and tab names as parameters to the model method
// this name combination of tab and layout in the method name is the only work around
// seeing that JCB uses those two values (tab_name & layout_name) as the layout file name.
// example of layout name: details_left.php
// example of method name: getFields_details_left()
$fields_tab_layout = 'fields_' . $layout_path_array[1];

$params = Helper::getParams('com_getbible');
$active = $params->get('enable_open_ai', 0);

// only load the fields if the OPEN AI is activated
$fields = [];
if ($active == 1)
{
	// get the fields
	$fields = $displayData->get($fields_tab_layout) ?: array(
	'max_tokens_override',
	'max_tokens',
	'max_tokens_note',
	'temperature_override',
	'temperature',
	'temperature_note',
	'top_p_override',
	'top_p',
	'top_p_note',
	'n_override',
	'n',
	'n_note'
	);
}

$hiddenFields = $displayData->get('hidden_fields') ?: array();

?>
<?php if ($fields && count((array) $fields)) :?>
<?php foreach($fields as $field): ?>
	<?php if (in_array($field, $hiddenFields)) : ?>
		<?php $form->setFieldAttribute($field, 'type', 'hidden'); ?>
	<?php endif; ?>
	<?php echo $form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field)); ?>
<?php endforeach; ?>
<?php else: ?>
	<div class="alert alert-info">
		<h4><?php echo Text::_('COM_GETBIBLE_OPEN_AI_DISABLED'); ?></h4>
		<p><?php echo Text::_('COM_GETBIBLE_YOU_WILL_HAVE_TO_ENABLE_OPEN_AI_IN_THE_GLOBAL_OPTIONS_OF_YOUR_COMPONENT_SINCE_IT_IS_CURRENTLY_DISABLED'); ?></p>
	</div>
<?php endif; ?>
