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



use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianBible\Component\GetBible\Site\Helper\GetbibleHelper;

// No direct access to this file
defined('JPATH_BASE') or die;

// Extract all keys from $displayData as individual variables.
extract($displayData);

// Assign default values for variables that might not be present in $displayData.

// The 'id' parameter, defaulting to an empty string if not set or is null.
$id ??= '';

// The 'name' parameter, defaulting to 'id' if not set. Additionally, replace hyphens with underscores.
$name ??= $id;
$name = str_replace('-', '_', $name);

// The 'class' parameter, defaulting to 'uk-select' if not set or is null.
$class ??= 'uk-select';

// The 'class_other' parameter, prepended with a space if set, otherwise defaulting to an empty string.
$class_other = isset($class_other) ? ' ' . $class_other : '';

// The 'options' parameter, set only if it exists and is an array, otherwise defaults to `false`.
$options = (isset($options) && is_array($options)) ? $options : false;

// The 'default' parameter, defaulting to an empty string if not set or is null.
$default ??= '';

// The 'disabled' parameter, defaulting to an empty string if not set or is null.
$disabled = !empty($readonly) || !empty($disabled) ? ' disabled="disabled"' : '';

// The 'onchange' attribute, added only if set, otherwise left as an empty string.
$onchange = isset($onchange) ? ' onchange="' . $onchange . '"' : '';

// The 'onkeydown' attribute, added only if set, otherwise left as an empty string.
$onkeydown = isset($onkeydown) ? ' onkeydown="' . $onkeydown . '"' : '';

?>
<select
	class="<?php echo htmlspecialchars($class . $class_other) ?>" 
	id="<?php echo htmlspecialchars($id) ?>" 
	name="<?php echo htmlspecialchars($name) ?>" 
	<?php echo $onkeydown ? htmlspecialchars($onkeydown) : '' ?>
	<?php echo $onchange ? htmlspecialchars($onchange) : '' ?>
	<?php echo $disabled ? htmlspecialchars($disabled) : '' ?>
>
	<?php if (!empty($options)): ?>
		<?php foreach ($options as $key => $value): ?>
			<?php
				// Determine the option key and value
				$option_key = $key;
				$option_value = $value;

				if (is_object($value) && isset($value->key, $value->value)) {
					$option_key = $value->key;
					$option_value = $value->value;
				} elseif (is_array($value) && isset($value['key'], $value['value'])) {
					$option_key = $value['key'];
					$option_value = $value['value'];
				}

				// Check if this option should be selected
				$isSelected = ($default === $option_key) ? ' selected' : '';
			?>
			<option value="<?php echo htmlspecialchars($option_key) ?>"<?php echo $isSelected ?>>
				<?php echo htmlspecialchars($option_value) ?>
			</option>
		<?php endforeach; ?>
	<?php else: ?>
		<option><?php echo htmlspecialchars(Text::_('COM_GETBIBLE_EMPTY')) ?></option>
	<?php endif; ?>
</select>
