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
use VDM\Joomla\Utilities\ObjectHelper;

// No direct access to this file
defined('_JEXEC') or die;

$modules = $this->params->get('custom_tabs');
$menus = [];
if (ObjectHelper::check($modules))
{
	foreach ($modules as $module)
	{
		if (isset($module->position) && strlen($module->position) > 0)
		{
			$menus[$module->position] = $this->escape($module->name);
		}
	}
}

?>
<?php if ($menus !== []): ?>
	<?php foreach ($menus as $tab => $menu): ?>
		<li<?php if ($this->tab_menu['active_tab'] === $tab) { echo ' class="uk-active"'; } ?>>
			<a href="#"><?php echo $menu; ?></a>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
