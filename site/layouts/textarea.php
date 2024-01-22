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
$class = (isset($displayData['class'])) ? $displayData['class'] : 'uk-textarea';
$class_other = (isset($displayData['class_other'])) ? ' ' . $displayData['class_other'] : '';
$rows = (isset($displayData['rows'])) ? $displayData['rows'] : 5;
$columns = (isset($displayData['columns'])) ? $displayData['columns'] : '';
$placeholder = (isset($displayData['placeholder'])) ? $displayData['placeholder'] : '';
$readonly = (isset($displayData['readonly']) && $displayData['readonly']) ? ' readonly' : '';
$direction = (isset($displayData['direction'])) ? ' dir="' . $displayData['direction'] . '"' : '';
$onchange = (isset($displayData['onchange'])) ? ' onchange="' . $displayData['onchange'] . '"' : '';
$onkeydown = (isset($displayData['onkeydown'])) ? ' onkeydown="' . $displayData['onkeydown'] . '"' : '';

?>
<textarea
	class="<?php echo $class . $class_other; ?>"
	name="<?php echo $name; ?>"
	aria-label="Textarea"
	id="<?php echo $id; ?>"
	rows="<?php echo $rows; ?>"
	columns="<?php echo $columns; ?>"
	placeholder="<?php echo $placeholder; ?>"
	<?php echo $direction; echo $readonly; echo $onchange; echo $onkeydown; ?>
></textarea>
