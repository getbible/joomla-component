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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::_('behavior.formvalidator');
Html::_('formbehavior.chosen', 'select');
Html::_('behavior.keepalive');

$componentParams = $this->params; // will be removed just use $this->params instead
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = document.querySelector('body');
	var loadingDiv = document.createElement('div');
	loadingDiv.id = 'loading';
	loadingDiv.style.cssText = "background: rgba(255, 255, 255, .8) url('components/com_getbible/assets/images/import.gif') 50% 15% no-repeat; top: " + (outerDiv.getBoundingClientRect().top + window.pageYOffset) + "px; left: " + (outerDiv.getBoundingClientRect().left + window.pageXOffset) + "px; width: " + outerDiv.offsetWidth + "px; height: " + outerDiv.offsetHeight + "px; position: fixed; opacity: 0.80; -ms-filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=80); filter: alpha(opacity=80); display: none;";
	outerDiv.appendChild(loadingDiv);
	loadingDiv.style.display = 'block';
	// when page is ready remove and show
	window.addEventListener('load', function() {
		var componentLoader = document.getElementById('getbible_loader');
		if (componentLoader) componentLoader.style.display = 'block';
		loadingDiv.style.display = 'none';
	});
</script>
<div id="getbible_loader" style="display: none;">
<form action="<?php echo Route::_('index.php?option=com_getbible&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

<?php echo LayoutHelper::render('prompt.prompt_above', $this); ?>
<div class="form-horizontal">

	<?php echo Html::_('bootstrap.startTabSet', 'promptTab', ['active' => 'prompt', 'recall' => true]); ?>

	<?php echo Html::_('bootstrap.addTab', 'promptTab', 'prompt', Text::_('COM_GETBIBLE_PROMPT_PROMPT', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo LayoutHelper::render('prompt.prompt_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo LayoutHelper::render('prompt.prompt_right', $this); ?>
			</div>
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo LayoutHelper::render('prompt.prompt_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo Html::_('bootstrap.endTab'); ?>

	<?php echo Html::_('bootstrap.addTab', 'promptTab', 'open_ai', Text::_('COM_GETBIBLE_PROMPT_OPEN_AI', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo LayoutHelper::render('prompt.open_ai_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo LayoutHelper::render('prompt.open_ai_right', $this); ?>
			</div>
		</div>
	<?php echo Html::_('bootstrap.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'promptTab'; ?>
	<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('prompt.edit.created_by') || $this->canDo->get('prompt.edit.created') || $this->canDo->get('prompt.edit.state') || ($this->canDo->get('prompt.delete') && $this->canDo->get('prompt.edit.state'))) : ?>
	<?php echo Html::_('bootstrap.addTab', 'promptTab', 'publishing', Text::_('COM_GETBIBLE_PROMPT_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo LayoutHelper::render('prompt.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo LayoutHelper::render('prompt.publlshing', $this); ?>
			</div>
		</div>
	<?php echo Html::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo Html::_('bootstrap.addTab', 'promptTab', 'permissions', Text::_('COM_GETBIBLE_PROMPT_PERMISSION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo Html::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo Html::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="prompt.edit" />
		<?php echo Html::_('form.token'); ?>
	</div>
</div>
</form>
</div>

<script type="text/javascript">



window.onload = function() {
  // Dynamic Field Manager
  let manager = new DynamicFieldManager('jform_integration', 'jform_cache_behaviour', '2', '2', '1');
}
</script>
