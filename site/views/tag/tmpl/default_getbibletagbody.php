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

$tagged_card = ($this->params->get('show_tagged_card') == 1) ? 'uk-card uk-card-' . $this->params->get('tagged_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
$tags_card = ($this->params->get('show_tags_card') == 1) ? 'uk-card uk-card-' . $this->params->get('tags_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;

?>
<ul id="get-bible-tag-body" class="uk-switcher" style="touch-action: pan-y pinch-zoom;" data-uk-observe>
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $tagged_card; ?> direction-<?php echo strtolower($this->translation->direction); ?>" dir="<?php echo $this->translation->direction; ?>">
			<h1><?php echo $this->tag->name; ?></h1>
			<p class="uk-text-meta"><?php echo $this->tag->description; ?></p>
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
