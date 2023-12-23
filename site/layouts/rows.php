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

$headers = $displayData['headers'];
$items = $displayData['items'];

?>
<?php if (is_array($items)): ?>
	<?php foreach ($items as $row => $values): ?>
		<tr>
		<?php foreach($values as $value): ?>
			<td class=""><?php echo $value; ?></td>
		<?php endforeach; ?>
		</tr>
	<?php endforeach; ?>
<?php elseif (is_numeric($items) && is_array($headers)): ?>
	<?php for( $row = 0; $row < $items; $row++): ?>
		<tr class="">
		<?php foreach($headers as $header): ?>
			<td class="">&nbsp;&nbsp;</td>
		<?php endforeach; ?>
		</tr>
	<?php endfor; ?>
<?php elseif (is_numeric($items) && is_numeric($headers)): ?>
	<?php for( $row = 0; $row < $items; $row++): ?>
		<tr class="">
		<?php for( $column = 0; $column < $headers; $column++): ?>
			<td class="">&nbsp;&nbsp;</td>
		<?php endfor; ?>
		</tr>
	<?php endfor; ?>
<?php endif; ?>
