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

$type = $this->params->get('bottom_menu_type');

if ($type == 2)
{
	$menu_type = 'class="uk-margin-small uk-flex-center" uk-tab ';
}
else
{
	$menu_type = 'class="el-nav uk-margin-small uk-subnav uk-subnav-pill uk-flex-center" ';
}

?>
<ul <?php echo $menu_type; ?>uk-switcher="connect: #get-bible-ai-body; animation: uk-animation-scale-up;">
	<li class="uk-active">
		<a href="#"><?php echo JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE'); ?></a>
	</li>
	<li>
		<a href="#"><?php echo JText::_('COM_GETBIBLE_PROMPT_USED'); ?></a>
	</li>
	<?php if ($this->params->get('show_prompt_settings', 1) == 1): ?>
		<li>
			<a href="#"><?php echo JText::_('COM_GETBIBLE_PROMPT_SETTINGS'); ?></a>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('show_openai_details', 1) == 1): ?>
		<li>
			<a href="#"><?php echo JText::_('COM_GETBIBLE_RESPONSE_DETAILS'); ?></a>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('set_custom_ai_tabs') == 1): ?>
		<?php echo $this->loadTemplate('getbibleaicustomtabsmenu'); ?>
	<?php endif; ?>
</ul>
