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

JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', '.multipleAccessLevels', null, array('placeholder_text_multiple' => '- ' . JText::_('COM_GETBIBLE_FILTER_SELECT_ACCESS') . ' -'));
JHtml::_('formbehavior.chosen', 'select');
if ($this->saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_getbible&task=translations.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'translationList', 'adminForm', strtolower($this->listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_getbible&view=translations'); ?>" method="post" name="adminForm" id="adminForm">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
<?php
	// Add the trash helper layout
	echo JLayoutHelper::render('trashhelper', $this);
	// Add the searchtools
	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
?>
<?php if (empty($this->items)): ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<table class="table table-striped" id="translationList">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<?php // Load the batch processing form. ?>
	<?php if ($this->canCreate && $this->canEdit) : ?>
		<?php echo JHtml::_(
			'bootstrap.renderModal',
			'collapseModal',
			array(
				'title' => JText::_('COM_GETBIBLE_TRANSLATIONS_BATCH_OPTIONS'),
				'footer' => $this->loadTemplate('batch_footer')
			),
			$this->loadTemplate('batch_body')
		); ?>
	<?php endif; ?>
	<input type="hidden" name="boxchecked" value="0" />
	</div>
<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
