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
use VDM\Joomla\Utilities\ObjectHelper;

$modules = $this->params->get('custom_tag_tabs');
$tabs = [];
if (ObjectHelper::check($modules))
{
	foreach ($modules as $module)
	{
		if (isset($module->position) && strlen($module->position) > 0)
		{
			if (($content = $this->getModules($module->position, $module->separator ?? '', $module->class ?? '')) !== false)
			{
				$tabs[$module->position] = $content;
			}
			else
			{
				$tabs[$module->position] = JLayoutHelper::render('modulepositionerror', ['position' => $module->position, 'page' => 'GetBible Tag']);
			}
		}
	}
}

$custom_tabs_card = ($this->params->get('show_custom_tabs_card') == 1) ? 'uk-card uk-card-' . $this->params->get('custom_tabs_card_style', 'default') . ' uk-card-body' : '';

?>
<?php if ($tabs !== []): ?>
	<?php foreach ($tabs as $tab): ?>
		<li class="el-item uk-margin-remove-first-child">
			<div class="<?php echo $custom_tabs_card; ?>">
				<?php echo $tab; ?>
			</div>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
