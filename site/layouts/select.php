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

$id = (isset($displayData['id'])) ? $displayData['id'] : '';
$name = (isset($displayData['name'])) ? $displayData['name'] : $id;
$class = (isset($displayData['class'])) ? $displayData['class'] : 'uk-select';
$class_other = (isset($displayData['class_other'])) ? ' ' . $displayData['class_other'] : '';
$name = str_replace('-', '_', $name);
$options = (isset($displayData['options']) && is_array($displayData['options'])) ? $displayData['options'] : false;
$default = $displayData['default'] ?? '';
$onchange = (isset($displayData['onchange'])) ? ' onchange="' . $displayData['onchange'] . '"' : '';
$onkeydown = (isset($displayData['onkeydown'])) ? ' onkeydown="' . $displayData['onkeydown'] . '"' : '';

?>
<select class="<?php echo $class . $class_other; ?>" id="<?php echo $id; ?>" name="<?php echo $name; ?>"<?php echo $onkeydown; echo $onchange; ?>>
<?php if ($options): ?>
	<?php foreach ($options as $key => $value): ?>
		<?php if (is_object($value) && isset($value->key) && isset($value->value)): ?>
			<?php if ($default === $value->key): ?>
				<option value="<?php echo  $value->key; ?>" selected><?php echo $value->value; ?></option>
			<?php else: ?>
				<option value="<?php echo  $value->key; ?>"><?php echo $value->value; ?></option>
			<?php endif; ?>
		<?php else: ?>
			<?php if ($default === $key): ?>
				<option value="<?php echo $key; ?>" selected><?php echo $value; ?></option>
			<?php else: ?>
				<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
			<?php endif; ?>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<option><?php echo Text::_('COM_GETBIBLE_EMPTY'); ?></option>
<?php endif; ?>
</select>
