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

// No direct access to this file
defined('_JEXEC') or die;

?>
<tr>
	<?php if ($this->canEdit&& $this->canState): ?>
		<th width="1%" class="nowrap center hidden-phone">
			<?php echo Html::_('searchtools.sort', '', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
		</th>
		<th width="20" class="nowrap center">
			<?php echo Html::_('grid.checkall'); ?>
		</th>
	<?php else: ?>
		<th width="20" class="nowrap center hidden-phone">
			&#9662;
		</th>
		<th width="20" class="nowrap center">
			&#9632;
		</th>
	<?php endif; ?>
	<th class="nowrap" >
			<?php echo Html::_('searchtools.sort', 'COM_GETBIBLE_OPEN_AI_MESSAGE_ROLE_LABEL', 'a.role', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap" >
			<?php echo Html::_('searchtools.sort', 'COM_GETBIBLE_OPEN_AI_MESSAGE_OPEN_AI_RESPONSE_LABEL', 'g.response_id', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap" >
			<?php echo Html::_('searchtools.sort', 'COM_GETBIBLE_OPEN_AI_MESSAGE_PROMPT_LABEL', 'h.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="nowrap hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_GETBIBLE_OPEN_AI_MESSAGE_SOURCE_LABEL', 'a.source', $this->listDirn, $this->listOrder); ?>
	</th>
	<?php if ($this->canState): ?>
		<th width="10" class="nowrap center" >
			<?php echo Html::_('searchtools.sort', 'COM_GETBIBLE_OPEN_AI_MESSAGE_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
		</th>
	<?php else: ?>
		<th width="10" class="nowrap center" >
			<?php echo Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_STATUS'); ?>
		</th>
	<?php endif; ?>
	<th width="5" class="nowrap center hidden-phone" >
			<?php echo Html::_('searchtools.sort', 'COM_GETBIBLE_OPEN_AI_MESSAGE_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>