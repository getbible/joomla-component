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
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;

$edit = "index.php?option=com_getbible&view=open_ai_messages&task=open_ai_message.edit";

?>
<?php foreach ($this->items as $i => $item): ?>
	<?php
		$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
		$userChkOut = Factory::getContainer()->
			get(\Joomla\CMS\User\UserFactoryInterface::class)->
				loadUserById($item->checked_out);
		$canDo = GetbibleHelper::getActions('open_ai_message',$item,'open_ai_messages');
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="order nowrap center hidden-phone">
		<?php if ($canDo->get('open_ai_message.edit.state')): ?>
			<?php
				$iconClass = '';
				if (!$this->saveOrder)
				{
					$iconClass = ' inactive tip-top" hasTooltip" title="' . Html::tooltipText('JORDERINGDISABLED');
				}
			?>
			<span class="sortable-handler<?php echo $iconClass; ?>">
				<i class="icon-menu"></i>
			</span>
			<?php if ($this->saveOrder) : ?>
				<input type="text" style="display:none" name="order[]" size="5"
				value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
			<?php endif; ?>
		<?php else: ?>
			&#8942;
		<?php endif; ?>
		</td>
		<td class="nowrap center">
		<?php if ($canDo->get('open_ai_message.edit')): ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo Html::_('grid.id', $i, $item->id); ?>
					<?php else: ?>
						&#9633;
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('grid.id', $i, $item->id); ?>
				<?php endif; ?>
		<?php else: ?>
			&#9633;
		<?php endif; ?>
		</td>
		<td class="nowrap">
			<div class="name">
				<?php if ($canDo->get('open_ai_message.edit')): ?>
					<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?>"><?php echo Text::_($item->role); ?></a>
					<?php if ($item->checked_out): ?>
						<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'open_ai_messages.', $canCheckin); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Text::_($item->role); ?>
				<?php endif; ?>
			</div>
		</td>
		<td class="nowrap">
			<div class="name">
				<?php if ($this->user->authorise('open_ai_response.edit', 'com_getbible.open_ai_response.' . (int) $item->open_ai_response_id)): ?>
					<a href="index.php?option=com_getbible&view=open_ai_responses&task=open_ai_response.edit&id=<?php echo $item->open_ai_response_id; ?>&return=<?php echo $this->return_here; ?>"><?php echo $this->escape($item->open_ai_response_response_id); ?></a>
				<?php else: ?>
					<?php echo $this->escape($item->open_ai_response_response_id); ?>
				<?php endif; ?>
			</div>
		</td>
		<td class="nowrap">
			<div class="name">
				<?php if ($this->user->authorise('prompt.edit', 'com_getbible.prompt.' . (int) $item->prompt_id)): ?>
					<a href="index.php?option=com_getbible&view=prompts&task=prompt.edit&id=<?php echo $item->prompt_id; ?>&return=<?php echo $this->return_here; ?>"><?php echo $this->escape($item->prompt_name); ?></a>
				<?php else: ?>
					<?php echo $this->escape($item->prompt_name); ?>
				<?php endif; ?>
			</div>
		</td>
		<td class="hidden-phone">
			<?php echo Text::_($item->source); ?>
		</td>
		<td class="center">
		<?php if ($canDo->get('open_ai_message.edit.state')) : ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'open_ai_messages.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo Html::_('jgrid.published', $item->published, $i, 'open_ai_messages.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo Html::_('jgrid.published', $item->published, $i, 'open_ai_messages.', true, 'cb'); ?>
				<?php endif; ?>
		<?php else: ?>
			<?php echo Html::_('jgrid.published', $item->published, $i, 'open_ai_messages.', false, 'cb'); ?>
		<?php endif; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>