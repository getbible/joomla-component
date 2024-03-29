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
defined('_JEXEC') or die;

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

// get the fields
$fields = $displayData->get($fields_tab_layout) ?: [
	'response_id',
	'prompt'
];

// Ensure $fields is treated as an array and count its size.
$size = count((array) $fields);

// Use a ternary operator to determine the class.
// If there are 1 to 4 fields, set the class to divide the 12-grid column equally among the fields.
// For more than 4 fields, default to four columns (3-grid each) for medium and larger screens.
$css_class = ($size > 0 && $size <= 4) ? 'col-12 col-md-' . round(12 / $size) : 'col-12 col-md-3';

$hiddenFields = $displayData->get('hidden_fields') ?: [];

?>
<?php if ($fields && count((array) $fields)) :?>
<div class="row title-alias form-vertical mb-3">
	<?php foreach($fields as $field): ?>
		<?php if (in_array($field, $hiddenFields)) : ?>
			<?php $form->setFieldAttribute($field, 'type', 'hidden'); ?>
		<?php endif; ?>
		<?php echo $form->renderField($field, null, null, ['class' => $css_class . ' control-wrapper-' . $field]); ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
