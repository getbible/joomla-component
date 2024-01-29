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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper;

// No direct access to this file
defined('_JEXEC') or die;

// set the defaults
$items = $displayData->vvymessage;
$user = Factory::getApplication()->getIdentity();
$id = $displayData->item->id;
// set the edit URL
$edit = "index.php?option=com_getbible&view=open_ai_messages&task=open_ai_message.edit";
// set a return value
$return = ($id) ? "index.php?option=com_getbible&view=open_ai_response&layout=edit&id=" . $id : "";
// check for a return value
$jinput = Factory::getApplication()->input;
if ($_return = $jinput->get('return', null, 'base64'))
{
	$return .= "&return=" . $_return;
}
// check if return value was set
if (StringHelper::check($return))
{
	// set the referral values
	$ref = ($id) ? "&ref=open_ai_response&refid=" . $id . "&return=" . urlencode(base64_encode($return)) : "&return=" . urlencode(base64_encode($return));
}
else
{
	$ref = ($id) ? "&ref=open_ai_response&refid=" . $id : "";
}

?>
<div class="form-vertical">
<?php if (ArrayHelper::check($items)): ?>
<table class="footable table data open_ai_messages" data-show-toggle="true" data-toggle-column="first" data-sorting="true" data-paging="true" data-paging-size="20" data-filtering="true">
<thead>
	<tr>
		<th data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_ROLE_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_OPEN_AI_RESPONSE_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_PROMPT_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm md" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_SOURCE_LABEL'); ?>
		</th>
		<th width="10" data-breakpoints="xs sm md">
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_STATUS'); ?>
		</th>
		<th width="5" data-type="number" data-breakpoints="xs sm md">
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_ID'); ?>
		</th>
	</tr>
</thead>
<tbody>
<?php foreach ($items as $i => $item): ?>
	<?php
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || $item->checked_out == 0;
		$userChkOut = Factory::getContainer()->
			get(\Joomla\CMS\User\UserFactoryInterface::class)->
				loadUserById($item->checked_out);
		$canDo = GetbibleHelper::getActions('open_ai_message',$item,'open_ai_messages');
	?>
	<tr>
		<td>
			<?php if ($canDo->get('open_ai_message.edit')): ?>
				<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?><?php echo $ref; ?>"><?php echo Text::_($item->role); ?></a>
				<?php if ($item->checked_out): ?>
					<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'open_ai_messages.', $canCheckin); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo Text::_($item->role); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->open_ai_response_response_id); ?>
		</td>
		<td>
			<?php if ($user->authorise('prompt.edit', 'com_getbible.prompt.' . (int) $item->prompt_id)): ?>
				<a href="index.php?option=com_getbible&view=prompts&task=prompt.edit&id=<?php echo $item->prompt_id; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->prompt_name); ?></a>
			<?php else: ?>
				<?php echo $displayData->escape($item->prompt_name); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo Text::_($item->source); ?>
		</td>
		<?php if ($item->published == 1): ?>
			<td class="center"  data-sort-value="1">
				<span class="status-metro status-published" title="<?php echo Text::_('COM_GETBIBLE_PUBLISHED');  ?>">
					<?php echo Text::_('COM_GETBIBLE_PUBLISHED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 0): ?>
			<td class="center"  data-sort-value="2">
				<span class="status-metro status-inactive" title="<?php echo Text::_('COM_GETBIBLE_INACTIVE');  ?>">
					<?php echo Text::_('COM_GETBIBLE_INACTIVE'); ?>
				</span>
			</td>
		<?php elseif ($item->published == 2): ?>
			<td class="center"  data-sort-value="3">
				<span class="status-metro status-archived" title="<?php echo Text::_('COM_GETBIBLE_ARCHIVED');  ?>">
					<?php echo Text::_('COM_GETBIBLE_ARCHIVED'); ?>
				</span>
			</td>
		<?php elseif ($item->published == -2): ?>
			<td class="center"  data-sort-value="4">
				<span class="status-metro status-trashed" title="<?php echo Text::_('COM_GETBIBLE_TRASHED');  ?>">
					<?php echo Text::_('COM_GETBIBLE_TRASHED'); ?>
				</span>
			</td>
		<?php endif; ?>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php endif; ?>
</div>
