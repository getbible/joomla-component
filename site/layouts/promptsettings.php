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
defined('JPATH_BASE') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;



?>
<div class="uk-card">
	<div class="uk-card-badge uk-label"><?php echo $displayData->model; ?></div>
	<h3 class="uk-card-title"><?php echo $displayData->language; ?></h3>
	<ul class="uk-list uk-list-collapse uk-list-striped">
		<li><?php echo Text::_('COM_GETBIBLE_MAX_TOKENS'); ?>: <?php echo $displayData->max_tokens; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_TEMPERATURE'); ?>: <?php echo $displayData->temperature; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_TOP_P'); ?>: <?php echo $displayData->top_p; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_PRESENCE_PENALTY'); ?>: <?php echo $displayData->presence_penalty; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_FREQUENCY_PENALTY'); ?>: <?php echo $displayData->frequency_penalty; ?></li>
	</ul>
</div>
