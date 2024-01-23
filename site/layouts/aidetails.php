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
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;



?>
<div class="uk-card">
	<div class="uk-card-badge uk-label"><?php echo $displayData->response_model; ?></div>
	<h3 class="uk-card-title"><?php echo $displayData->response_object; ?></h3>
	<ul class="uk-list uk-list-collapse uk-list-striped">
		<li><?php echo Text::_('COM_GETBIBLE_CREATED'); ?>: <?php echo $displayData->response_created; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_PROMPT_TOKENS'); ?>: <?php echo $displayData->prompt_tokens; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_COMPLETION_TOKENS'); ?>: <?php echo $displayData->completion_tokens; ?></li>
		<li><?php echo Text::_('COM_GETBIBLE_TOTALTOKENS'); ?>: <?php echo $displayData->total_tokens; ?></li>
	</ul>
</div>
