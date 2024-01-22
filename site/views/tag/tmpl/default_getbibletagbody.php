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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

$type = $this->params->get('top_menu_type');
if ($type == 2)
{
	$menu_type = ' uk-card-body';
}
else
{
	$menu_type = ' uk-card-small';
}

$tagged_card = ($this->params->get('show_tagged_card') == 1) ? 'uk-card uk-card-' . $this->params->get('tagged_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
$tags_card = ($this->params->get('show_tags_card') == 1) ? 'uk-card uk-card-' . $this->params->get('tags_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;

?>
<ul id="get-bible-tag-body" class="uk-switcher" style="touch-action: pan-y pinch-zoom;" data-uk-observe>
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $tagged_card; ?> direction-<?php echo strtolower($this->translation->direction); ?>" dir="<?php echo $this->translation->direction; ?>">
			<div class="uk-margin">
				<div class="uk-float-right">
					<a class="uk-icon-button" href="<?php echo $this->getBibleUrl(); ?>" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_BACK_TO_BIBLE'); ?>" uk-icon="icon: home"></a>
					<?php if ($this->params->get('activate_sharing') == 1): ?>
						<a class="uk-icon-button" href="#" id="getbible-tag-sharing-button" uk-toggle="target: #getbible-tag-sharing" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_SHARE_TAG'); ?>" uk-icon="icon: forward"></a>
					<?php endif; ?>
				</div>
				<div><span class="uk-text-large"><?php echo $this->tag->name; ?></span></div>
				<div><span class="uk-text-meta"><?php echo $this->tag->description; ?></span></div>
			</div>
			<?php echo $this->loadTemplate('getbibletagparagraphssorter'); ?>
			<?php echo $this->loadTemplate('getbibletagparagraphs'); ?>
		</div>
	</li>
	<li class="el-item uk-margin-remove-first-child">
		<div class="uk-card<?php echo $tags_card; ?>">
			<?php echo $this->loadTemplate('getbibleselecttags'); ?>
		</div>
	</li>
	<?php if ($this->params->get('set_custom_tag_tabs') == 1): ?>
		<?php echo $this->loadTemplate('getbibletagcustomtabs'); ?>
	<?php endif; ?>
</ul>
<?php if ($this->params->get('activate_sharing') == 1): ?>
	<?php echo $this->loadTemplate('getbibletagshare'); ?>
<?php endif; ?>
<?php
	$this->getBibleModules = [
		'position' => 'bottom_tag_position',
		'page' => 'GetBible Tag'
	];
	echo $this->loadTemplate('getbiblemodules');
?>
