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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$componentParams = $this->params; // will be removed just use $this->params instead
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_getbible/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#getbible_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
<div id="getbible_loader" style="display: none;">
<form action="<?php echo JRoute::_('index.php?option=com_getbible&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('open_ai_response.details_above', $this); ?>
<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'open_ai_responseTab', array('active' => 'details')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'open_ai_responseTab', 'details', JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_DETAILS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.details_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.details_right', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'open_ai_responseTab', 'prompt', JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_PROMPT', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.prompt_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.prompt_right', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'open_ai_responseTab', 'bible', JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_BIBLE', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.bible_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.bible_right', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php if ($this->canDo->get('open_ai_message.access')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'open_ai_responseTab', 'message', JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_MESSAGE', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
		</div>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('open_ai_response.message_fullwidth', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'open_ai_responseTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('open_ai_response.edit.created_by') || $this->canDo->get('open_ai_response.edit.created') || $this->canDo->get('open_ai_response.edit.state') || ($this->canDo->get('open_ai_response.delete') && $this->canDo->get('open_ai_response.edit.state'))) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'open_ai_responseTab', 'publishing', JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('open_ai_response.publlshing', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'open_ai_responseTab', 'permissions', JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_PERMISSION', true)); ?>
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
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="open_ai_response.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
</form>
</div>