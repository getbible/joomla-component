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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die;

$type = $this->params->get('top_menu_type');

if ($type == 2)
{
	$menu_type = 'class="uk-margin-small uk-flex-center" uk-tab';
}
else
{
	$menu_type = 'class="el-nav uk-margin-small uk-subnav uk-subnav-pill uk-flex-center"';
}

?>
<ul <?php echo $menu_type; ?> uk-switcher="connect: #get-bible-app-body; animation: uk-animation-scale-up;">
	<li<?php if ($this->tab_menu['active_tab'] === 'scripture') { echo ' class="uk-active"'; } ?>>
		<a href="#"><?php echo $this->tab_menu['scripture_icon']; ?><?php echo $this->tab_menu['scripture']; ?></a>
	</li>
	<li<?php if ($this->tab_menu['active_tab'] === 'books') { echo ' class="uk-active"'; } ?>>
		<a href="#"><?php echo $this->tab_menu['books_icon']; ?><?php echo $this->tab_menu['books']; ?></a>
	</li>
	<li<?php if ($this->tab_menu['active_tab'] === 'chapters') { echo ' class="uk-active"'; } ?>>
		<a href="#"><?php echo $this->tab_menu['chapters_icon']; ?><?php echo $this->tab_menu['chapters']; ?></a>
	</li>
	<li<?php if ($this->tab_menu['active_tab'] === 'translations') { echo ' class="uk-active"'; } ?>>
		<a href="#"><?php echo $this->tab_menu['translations_icon']; ?><?php echo $this->tab_menu['translations']; ?></a>
	</li>
	<?php if ($this->params->get('show_settings') == 1): ?>
		<li<?php if ($this->tab_menu['active_tab'] === 'settings') { echo ' class="uk-active"'; } ?>>
			<a href="#"><?php echo $this->tab_menu['settings_icon']; ?><?php echo $this->tab_menu['settings']; ?></a>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('show_details') == 1): ?>
		<li<?php if ($this->tab_menu['active_tab'] === 'details') { echo ' class="uk-active"'; } ?>>
			<a href="#"><?php echo $this->tab_menu['details_icon']; ?><?php echo $this->tab_menu['details']; ?></a>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('set_custom_tabs') == 1): ?>
		<?php echo $this->loadTemplate('getbibleappcustomtabsmenu'); ?>
	<?php endif; ?>
</ul>
