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
use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die;

?>
<?php if ($this->params->get('activate_' . $this->modalState->one) == 1 || $this->params->get('activate_' . $this->modalState->two) == 1): ?>
	<div class="uk-margin uk-padding-remove-horizontal">
		<?php $width =1; ?>
		<?php if ($this->params->get('activate_' . $this->modalState->one) == 1 && $this->params->get('activate_' . $this->modalState->two) == 1): ?>
			<div class="uk-button-group uk-width-1-1">
			<?php $width = 2; ?>
		<?php endif; ?>
		<?php if ($this->params->get('activate_' . $this->modalState->one) == 1): ?>
			<a href="#getbible-app-<?php echo $this->modalState->one; ?>" class="uk-button uk-button-primary uk-width-1-<?php echo $width; ?>" onclick="setActiveOpenModel('<?php echo $this->modalState->one; ?>');" uk-toggle><?php echo $this->modalState->oneText; ?></a>
		<?php endif; ?>
		<?php if ($this->params->get('activate_' . $this->modalState->two) == 1): ?>
			<a href="#getbible-app-<?php echo $this->modalState->two; ?>" class="uk-button uk-button-primary uk-width-1-<?php echo $width; ?>" onclick="setActiveOpenModel('<?php echo $this->modalState->two; ?>');" uk-toggle><?php echo $this->modalState->twoText; ?></a>
		<?php endif; ?>
		<?php if ($this->params->get('activate_' . $this->modalState->one) == 1 && $this->params->get('activate_' . $this->modalState->two) == 1): ?>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php if ($this->params->get('enable_open_ai') == 1 && ($buttons = $this->promptIntegration($this->prompts, [2])) !== null): ?>
	<?php foreach ($buttons as $button): ?>
		<a href="#" id="getbible-openai-<?php echo $this->modalState->main; ?>-<?php echo $button->guid; ?>" class="uk-button uk-button-primary uk-width-1-1 uk-margin-small-bottom"><?php echo $this->escape($button->name); ?></a>
	<?php endforeach; ?>
<?php endif ?>
