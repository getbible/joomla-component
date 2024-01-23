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
<?php echo $this->loadTemplate('getbiblesearchinput'); ?>
<?php echo $this->loadTemplate('getbiblesearchnotenoughverses'); ?>
<?php if ($this->items): ?>
	<?php echo $this->loadTemplate('getbiblesearchtable'); ?>
<?php else: ?>
	<div class="uk-alert-primary" uk-alert>
		<?php if (strlen($this->getSearch()) > 0): ?>
			<p><?php echo Text::_("COM_GETBIBLE_YOUR_SEARCH_DIDNT_YIELD_ANY_RESULTS_PLEASE_TYPE_A_DIFFERENT_KEYWORD_OR_PHRASE_INTO_THE_SEARCH_BOX_AND_PRESS_ENTER_TO_TRY_AGAIN"); ?></p>
		<?php else: ?>
			<p><?php echo Text::_('COM_GETBIBLE_TYPE_YOUR_SEARCH_PHRASE_INTO_THE_SEARCH_BOX_AND_PRESS_ENTER_TO_SEARCH_THE_BSCRIPTURESB_DAILY'); ?></p>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php
	$this->getBibleModules = [
		'position' => 'bottom_search_position',
		'page' => 'GetBible Search'
	];
	echo $this->loadTemplate('getbiblemodules');
?>
