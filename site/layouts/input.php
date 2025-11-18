<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    2015-12-03 01:42:15
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

// The 'value' parameter, defaulting to an empty string if not set or is null.
$value ??= '';

// The 'class' parameter, defaulting to 'uk-input' if not set or is null.
$class ??= 'uk-input';

// The 'class_other' parameter, prepended with a space if set, otherwise defaulting to an empty string.
$class_other = isset($class_other) ? ' ' . $class_other : '';

// The 'placeholder' parameter, defaulting to an empty string if not set or is null.
$placeholder ??= '';

// The 'type' parameter, defaulting to 'text' if not set or is null.
$type ??= 'text';

// The 'readonly' attribute, set to 'readonly' if true, otherwise left as an empty string.
$readonly = !empty($readonly) ? ' readonly' : '';

// The 'format' attribute, added only if set, otherwise left as an empty string.
$format = !empty($format) ? ' format="' . $format . '"' : '';

// The 'onchange' attribute, added only if set, otherwise left as an empty string.
$onchange = isset($onchange) ? ' onchange="' . $onchange . '"' : '';

// The 'onkeydown' attribute, added only if set, otherwise left as an empty string.
$onkeydown = isset($onkeydown) ? ' onkeydown="' . $onkeydown . '"' : '';

// The 'required' attribute, set to 'required' if true, otherwise left as an empty string.
$required = !empty($required) ? ' required' : '';

?>
<input
	class="<?php echo $class . $class_other; ?>"
	name="<?php echo $name; ?>"
	id="<?php echo $id; ?>"
	type="<?php echo $type; ?>"
	placeholder="<?php echo $placeholder; ?>"
	value="<?php echo $value; ?>"
	<?php echo $readonly; echo $onchange; echo $onkeydown; echo $format; echo $required; ?>
>
