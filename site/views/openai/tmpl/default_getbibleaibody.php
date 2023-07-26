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

$type = $this->params->get('top_menu_type');
if ($type == 2)
{
	$menu_type = ' uk-card-body';
}
else
{
	$menu_type = ' uk-card-small';
}

$ai_card = ($this->params->get('show_ai_card') == 1) ? 'uk-card uk-card-' . $this->params->get('ai_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
$prompt_card = ($this->params->get('show_prompt_card') == 1) ? 'uk-card uk-card-' . $this->params->get('prompt_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;

if ($this->params->get('show_prompt_settings', 1) == 1)
{
	$settings_card = ($this->params->get('show_prompt_settings_card') == 1) ? 'uk-card uk-card-' . $this->params->get('prompt_settings_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
}

if ($this->params->get('show_openai_details', 1) == 1)
{
	$details_card = ($this->params->get('show_openai_details_card') == 1) ? 'uk-card uk-card-' . $this->params->get('openai_details_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
}

?>
<ul id="get-bible-ai-body" class="uk-switcher" style="touch-action: pan-y pinch-zoom;" data-uk-observe>
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $ai_card; ?>">
			<?php echo $this->loadTemplate('getbibleaimessages'); ?>
		</div>
	</li>
	<li class="el-item uk-margin-remove-first-child">
		<div class="uk-card<?php echo $prompt_card; ?>">
			<?php echo $this->loadTemplate('getbibleaipromptmessages'); ?>
		</div>
	</li>
	<?php if ($this->params->get('show_prompt_settings', 1) == 1): ?>
		<li class="el-item uk-margin-remove-first-child">
			<div class="<?php echo $settings_card; ?>">
				<?php echo $this->loadTemplate('getbiblepromptsettings'); ?>
			</div>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('show_openai_details', 1) == 1): ?>
		<li class="el-item uk-margin-remove-first-child">
			<div class="<?php echo $details_card; ?>">
				<?php echo $this->loadTemplate('getbibleaidetails'); ?>
			</div>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('set_custom_ai_tabs') == 1): ?>
		<?php echo $this->loadTemplate('getbibleaicustomtabs'); ?>
	<?php endif; ?>
</ul>
