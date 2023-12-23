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
$name = str_replace('-', '_', $name);
$value = (isset($displayData['value'])) ? $displayData['value'] : '';
$class = (isset($displayData['class'])) ? $displayData['class'] : 'uk-input';
$class_other = (isset($displayData['class_other'])) ? ' ' . $displayData['class_other'] : '';
$placeholder = (isset($displayData['placeholder'])) ? $displayData['placeholder'] : '';
$type = (isset($displayData['type'])) ? $displayData['type'] : 'text';
$readonly = (isset($displayData['readonly']) && $displayData['readonly']) ? ' readonly' : '';
$format = (isset($displayData['format']) && $displayData['format']) ? ' format="' . $displayData['format'] . '"' : '';
$onchange = (isset($displayData['onchange'])) ? ' onchange="' . $displayData['onchange'] . '"' : '';
$onkeydown = (isset($displayData['onkeydown'])) ? ' onkeydown="' . $displayData['onkeydown'] . '"' : '';
$required = (isset($displayData['required']) && $displayData['required']) ? ' required' : '';

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
