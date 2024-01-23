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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
Html::_('formbehavior.chosen', '.multipleAccessLevels', null, ['placeholder_text_multiple' => '- ' . Text::_('COM_GETBIBLE_FILTER_SELECT_ACCESS') . ' -']);
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;

if ($this->saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_getbible&task=passwords.saveOrderAjax&tmpl=component';
	Html::_('sortablelist.sortable', 'passwordList', 'adminForm', strtolower($this->listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo Route::_('index.php?option=com_getbible&view=passwords'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
<?php
	// Add the trash helper layout
	echo LayoutHelper::render('trashhelper', $this);
	// Add the searchtools
	echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
?>
<?php if (empty($this->items)): ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<table class="table table-striped" id="passwordList">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<input type="hidden" name="boxchecked" value="0" />
	</div>
<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<?php echo Html::_('form.token'); ?>
</form>
