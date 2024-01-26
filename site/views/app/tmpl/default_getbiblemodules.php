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
use VDM\Joomla\Utilities\ObjectHelper;

$modules = $this->params->get($this->getBibleModules['position']);
$position_card = '';
$positions = [];
if (ObjectHelper::check($modules))
{
	foreach ($modules as $module)
	{
		if (isset($module->position) && strlen($module->position) > 0)
		{
			if (($content = $this->getModules($module->position, $module->separator ?? '', $module->class ?? '')) !== false)
			{
				$positions[$module->position] = $content;
			}
			else
			{
				$positions[$module->position] = LayoutHelper::render('modulepositionerror', ['position' => $module->position, 'page' => $this->getBibleModules['page']]);
			}
		}
	}
	$position_card = ($this->params->get('show_' . $this->getBibleModules['position'] . '_card') == 1) ? 'uk-card uk-card-' . $this->params->get($this->getBibleModules['position'] . '_card_style', 'default') . ' uk-card-body uk-margin' : 'uk-margin';
}

?>
<?php if ($positions !== []): ?>
<div class="<?php echo $this->getBibleModules['class'] ?? 'uk-margin'; ?>">
	<?php foreach ($positions as $mod): ?>
		<div class="<?php echo $position_card; ?>">
			<?php if (is_array($mod)): ?>
				<?php foreach ($mod as $pos): ?>
					<?php echo $pos; ?>
				<?php endforeach; ?>
			<?php elseif (is_string($mod)): ?>
				<div uk-grid>
					<?php echo $mod; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
<?php endif; ?>
