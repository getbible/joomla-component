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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

?>
<?php if ($this->params->get('previous_next_navigation') == 1): ?>
	<div class="uk-margin uk-margin-remove-bottom">
		<?php echo JLayoutHelper::render('previouschapter', $this->previous); ?>
	</div>
<?php endif; ?>
<?php if ($this->params->get('activate_sharing') == 1 || $this->params->get('activate_search') == 1 || empty($this->item->daily)): ?>
	<div class="uk-float-right">
		<?php if (empty($this->item->daily)): ?>
			<a class="uk-icon-button" href="<?php echo $this->getDailyVerseUrl(); ?>" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_DAILY_VERSE'); ?>" uk-icon="icon: home"></a>
		<?php endif; ?>
		<?php if ($this->params->get('activate_search') == 1): ?>
			<a class="uk-icon-button" href="<?php echo $this->getSearchUrl(); ?>" id="getbible-main-search-button"  uk-tooltip="<?php echo Text::_('COM_GETBIBLE_SEARCH'); ?>" uk-icon="icon: search"></a>
		<?php endif; ?>
		<?php if ($this->params->get('activate_sharing') == 1): ?>
			<a class="uk-icon-button" href="#" id="getbible-main-sharing-button" uk-toggle="target: #getbible-app-sharing" onclick="setSharedValues(<?php echo $this->verses->first; ?>, <?php echo $this->verses->last; ?>)" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_SHARE'); ?>" uk-icon="icon: forward"></a>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php if ($this->params->get('activate_search') == 1 || ($this->params->get('enable_open_ai') == 1 && !empty($this->prompts))): ?>
	<?php echo $this->loadTemplate('getbibleappword'); ?>
<?php endif; ?>
<div id="getbible-holy-scripture" class="direction-<?php echo strtolower($this->translation->direction); ?>" dir="<?php echo $this->translation->direction; ?>">
	<?php if ($this->params->get('show_header') == 1): ?>
		<div>
			<span class="uk-text-large"><?php echo $this->chapter->name; ?></span>
		</div>
	<?php endif; ?>
	<?php if ($this->params->get('verse_per_line') == 1): ?>
		<?php echo $this->loadTemplate('versesunorderedlist'); ?>
	<?php else: ?>
		<?php echo $this->loadTemplate('versesparagraph'); ?>
	<?php endif; ?>
</div>
<?php if ($this->params->get('previous_next_navigation') == 1): ?>
	<div class="uk-margin">
		<?php echo JLayoutHelper::render('nextchapter', $this->next); ?>
	</div>
<?php endif; ?>
