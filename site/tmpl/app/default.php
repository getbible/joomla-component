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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;
?>

<?php if ($this->item): ?>
	<?php echo $this->loadTemplate('getbibleapp'); ?>
<?php else: ?>
	<div uk-alert>
		<h3><?php echo Text::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR'); ?></h3>
		<p><?php echo Text::_('COM_GETBIBLE_FOR_SOME_REASON_YOUR_REQUEST_COULD_NOT_BE_PROCESSED_AT_THIS_TIME'); ?></p>
	</div>
<?php endif; ?>
<?php if ($this->params->get('debug') == 1): ?>
	<?php echo $this->loadTemplate('getbibleappdebug'); ?>
<?php endif; ?>
<?php echo $this->loadTemplate('getbibleinstallbutton'); ?>
