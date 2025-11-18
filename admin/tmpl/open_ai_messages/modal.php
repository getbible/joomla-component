<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.life>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use TrueChristianBible\Component\GetBible\Administrator\Helper\GetbibleHelper;

// No direct access to this file
defined('_JEXEC') or die;

/** @var \TrueChristianBible\Component\GetBible\Administrator\View\Open_ai_messages\HtmlView $this */

$app = Factory::getApplication();

if ($app->isClient('site'))
{
    Session::checkToken('get') or die(Text::_('JINVALID_TOKEN'));
}

// dynamic selection of title key (link in modal)
$input = method_exists($app, 'getInput') ? $app->getInput() : $app->input;
$this->modalTitleKey = $input->get('titleKey', 'id', 'word');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('core')
    ->useScript('multiselect')
    ->useScript('modal-content-select');
?>
<form action="<?php echo Route::_('index.php?option=com_getbible&view=open_ai_messages&layout=modal&tmpl=component&titleKey=' . $this->getModalTitleKey()); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
<?php
	// Add the trash helper layout
	echo LayoutHelper::render('trashhelper', $this);
	// Add the searchtools
	echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
?>
<?php if (empty($this->items)): ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<table class="table table-striped" id="open_ai_messageList">
		<thead><?php echo $this->loadTemplate('head');?></thead>
		<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
		<tbody><?php echo $this->loadTemplate('body');?></tbody>
	</table>
	<input type="hidden" name="boxchecked" value="0" />
	</div>
<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<?php echo Html::_('form.token'); ?>
</form>
