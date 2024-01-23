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
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper;

// set the defaults
$items = $displayData->vvvtags;
$user = Factory::getApplication()->getIdentity();
$id = $displayData->item->id;
// set the edit URL
$edit = "index.php?option=com_getbible&view=tagged_verses&task=tagged_verse.edit";
// set a return value
$return = ($id) ? "index.php?option=com_getbible&view=linker&layout=edit&id=" . $id : "";
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
	$ref = ($id) ? "&ref=linker&refid=" . $id . "&return=" . urlencode(base64_encode($return)) : "&return=" . urlencode(base64_encode($return));
}
else
{
	$ref = ($id) ? "&ref=linker&refid=" . $id : "";
}
// set the create new URL
$new = "index.php?option=com_getbible&view=tagged_verses&task=tagged_verse.edit" . $ref;
// set the create new and close URL
$close_new = "index.php?option=com_getbible&view=tagged_verses&task=tagged_verse.edit";
// load the action object
$can = GetbibleHelper::getActions('tagged_verse');

?>
<div class="form-vertical">
<?php if ($can->get('tagged_verse.create')): ?>
	<div class="btn-group">
		<a class="btn btn-small btn-success" href="<?php echo $new; ?>"><span class="icon-new icon-white"></span> <?php echo Text::_('COM_GETBIBLE_NEW'); ?></a>
		<a class="btn btn-small" onclick="Joomla.submitbutton('linker.cancel');" href="<?php echo $close_new; ?>"><span class="icon-new"></span> <?php echo Text::_('COM_GETBIBLE_CLOSE_NEW'); ?></a>
	</div><br /><br />
<?php endif; ?>
<?php if (ArrayHelper::check($items)): ?>
<table class="footable table data tagged_verses" data-show-toggle="true" data-toggle-column="first" data-sorting="true" data-paging="true" data-paging-size="20" data-filtering="true">
<thead>
	<tr>
		<th data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSES_REFERENCE'); ?>
		</th>
		<th data-breakpoints="xs sm" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSE_ABBREVIATION_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSE_ACCESS_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm md" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSE_LINKER_LABEL'); ?>
		</th>
		<th data-breakpoints="xs sm md" data-type="html" data-sort-use="text">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSE_TAG_LABEL'); ?>
		</th>
		<th width="10" data-breakpoints="xs sm md">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSE_STATUS'); ?>
		</th>
		<th width="5" data-type="number" data-breakpoints="xs sm md">
			<?php echo Text::_('COM_GETBIBLE_TAGGED_VERSE_ID'); ?>
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
		$canDo = GetbibleHelper::getActions('tagged_verse',$item,'tagged_verses');
	?>
	<tr>
		<td>
			<?php if ($canDo->get('tagged_verse.edit')): ?>
				<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->book_nr); ?></a>
				<?php if ($item->checked_out): ?>
					<?php echo Html::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'tagged_verses.', $canCheckin); ?>
				<?php endif; ?>
			<?php else: ?>
				<?php echo $displayData->escape($item->book_nr); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php if ($user->authorise('translation.edit', 'com_getbible.translation.' . (int) $item->abbreviation_id)): ?>
				<a href="index.php?option=com_getbible&view=translations&task=translation.edit&id=<?php echo $item->abbreviation_id; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->abbreviation_translation); ?></a>
			<?php else: ?>
				<?php echo $displayData->escape($item->abbreviation_translation); ?>
			<?php endif; ?>
		</td>
		<td>
			<?php echo Text::_($item->access); ?>
		</td>
		<td>
			<?php echo $displayData->escape($item->linker_name); ?>
		</td>
		<td>
			<?php if ($user->authorise('tag.edit', 'com_getbible.tag.' . (int) $item->tag_id)): ?>
				<a href="index.php?option=com_getbible&view=tags&task=tag.edit&id=<?php echo $item->tag_id; ?><?php echo $ref; ?>"><?php echo $displayData->escape($item->tag_name); ?></a>
			<?php else: ?>
				<?php echo $displayData->escape($item->tag_name); ?>
			<?php endif; ?>
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
