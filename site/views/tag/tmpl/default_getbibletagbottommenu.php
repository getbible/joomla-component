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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

$type = $this->params->get('bottom_menu_type');

if ($type == 2)
{
	$menu_type = 'class="uk-margin-small uk-flex-center" uk-tab ';
}
else
{
	$menu_type = 'class="el-nav uk-margin-small uk-subnav uk-subnav-pill uk-flex-center" ';
}

?>
<ul <?php echo $menu_type; ?>uk-switcher="connect: #get-bible-tag-body; animation: uk-animation-scale-up;">
	<li class="uk-active">
		<a href="#"><?php echo Text::_('COM_GETBIBLE_TAGGED_VERSES'); ?></a>
	</li>
	<li>
		<a href="#"><?php echo Text::_('COM_GETBIBLE_TAGS'); ?></a>
	</li>
	<?php if ($this->params->get('set_custom_tag_tabs') == 1): ?>
		<?php echo $this->loadTemplate('getbibletagcustomtabsmenu'); ?>
	<?php endif; ?>
</ul>
