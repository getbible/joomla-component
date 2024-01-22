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

// set the modal target
$target = $displayData['active']->target ?? '';

// build the verse number
$number = '<span id="getbible-verse-number-' . $displayData['verse']->verse . '" class="getbible-verse-number">' . $displayData['verse']->verse . JLayoutHelper::render('getbibleversetag', $displayData) . '<span>';

// build the actual verse text
$text = '<span id="getbible-verse-text-' . $displayData['verse']->verse . '" class="getbible-verse-text">' . $displayData['verse']->text . '<span>';

// check if this is a selected verse
if ($displayData['selected'])
{
	$text = '<span class="getbible-verse-selected">' . $text . '</span>';
}

?>
<span id="getbible-verse-<?php echo $displayData['verse']->verse; ?>" class="getbible-verse">
<?php if ($displayData['active']->verse): ?>
	<a id="getbible-verse-link-<?php echo $displayData['verse']->verse; ?>" class="getbible-verse-link uk-link-muted"  href="#" uk-toggle="target: #getbible-app-<?php echo $target; ?>" onclick="setActiveVerse(<?php echo $displayData['verse']->verse; ?>);" uk-tooltip="<?php echo $displayData['active']->tooltip; ?>"><?php echo $number; ?></a>&nbsp;<?php echo $text; ?>
<?php else: ?>
	<?php echo $number; ?>&nbsp;<?php echo $text; ?>
<?php endif; ?>
</span>
