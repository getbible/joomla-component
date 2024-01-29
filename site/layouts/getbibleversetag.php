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
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;

// No direct access to this file
defined('JPATH_BASE') or die;

$active = false;
$style = '';

// verse does have active tags
if (!isset($displayData['tag']))
{
	$active = false;
}
elseif ($displayData['tag'] == 1)
{
	$active = true;
}
// no active tags, but tags are active on page
elseif ($displayData['tag'] == -1)
{
	$style = 'display:none;';
	$active = true;
}

?>
<?php if ($active): ?>&nbsp;<a id="getbible-verse-tag-<?php echo $displayData['verse']->verse; ?>"
class="getbible-verse-link-tag uk-link-muted"
href="#" uk-toggle="target: #getbible-app-tags" onclick="setActiveVerse(<?php echo $displayData['verse']->verse; ?>);"
uk-tooltip="<?php echo Text::_('COM_GETBIBLE_OPEN_TAG'); ?>"
style="<?php echo $style; ?>"
uk-icon="tag"></a><?php endif; ?>
