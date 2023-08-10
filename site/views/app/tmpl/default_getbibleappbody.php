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

$scripture_card = ($this->params->get('show_scripture_card') == 1) ? 'uk-card uk-card-' . $this->params->get('scripture_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
$translations_card = ($this->params->get('show_translations_card') == 1) ? 'uk-card uk-card-' . $this->params->get('translations_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
$books_card = ($this->params->get('show_books_card') == 1) ? 'uk-card uk-card-' . $this->params->get('books_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
$chapters_card = ($this->params->get('show_chapters_card') == 1) ? 'uk-card uk-card-' . $this->params->get('chapters_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;

if ($this->params->get('show_settings') == 1)
{
	$settings_card = ($this->params->get('show_settings_card') == 1) ? 'uk-card uk-card-' . $this->params->get('settings_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
}

if ($this->params->get('show_details') == 1)
{
	$details_card = ($this->params->get('show_details_card') == 1) ? 'uk-card uk-card-' . $this->params->get('details_card_style', 'default') . ' uk-card-body' : 'uk-card' . $menu_type;
}

?>
<ul id="get-bible-app-body" class="uk-switcher" style="touch-action: pan-y pinch-zoom;">
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $scripture_card; ?>">
			<?php echo $this->loadTemplate('getbibletext'); ?>
		</div>
	</li>
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $books_card; ?>">
			<?php echo $this->loadTemplate('getbiblebooks'); ?>
		</div>
	</li>
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $chapters_card; ?>">
			<?php echo $this->loadTemplate('getbiblechapters'); ?>
		</div>
	</li>
	<li class="el-item uk-margin-remove-first-child">
		<div class="<?php echo $translations_card; ?>">
			<?php echo $this->loadTemplate('getbibletranslations'); ?>
		</div>
	</li>
	<?php if ($this->params->get('show_settings') == 1): ?>
		<li class="el-item uk-margin-remove-first-child">
			<div class="<?php echo $settings_card; ?>">
				<?php echo $this->loadTemplate('getbibleappsettings'); ?>
			</div>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('show_details') == 1): ?>
		<li class="el-item uk-margin-remove-first-child">
			<div class="<?php echo $details_card; ?>">
				<?php echo $this->loadTemplate('getbibleappdetails'); ?>
			</div>
		</li>
	<?php endif; ?>
	<?php if ($this->params->get('set_custom_tabs') == 1): ?>
		<?php echo $this->loadTemplate('getbibleappcustomtabs'); ?>
	<?php endif; ?>
</ul>
<?php
	$this->getBibleModules = [
		'position' => 'bottom_app_position',
		'page' => 'GetBible'
	];
	echo $this->loadTemplate('getbiblemodules');
?>
