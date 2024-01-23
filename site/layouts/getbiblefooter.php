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
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;



?>
<?php if ($displayData['load'] == 1): ?>
	<div class="uk-float-right">
		<a class="uk-link-muted" href="https://getbible.net/<?php echo $displayData['path'] ?? ''; ?>" target="_blank" uk-tooltip="title: <?php echo Text::_('COM_GETBIBLE_THE_WORDS_OF_ETERNAL_LIFE'); ?>; pos: left">
			<?php if ($displayData['logo'] == 1): ?>
				<?php echo Html::_('image', 'media/com_getbible/images/icon.png', 'getBible.net Logo'); ?>
			<?php else: ?>
				getBible.net
			<?php endif; ?>
		</a>
	</div>
<?php elseif ($displayData['logo'] == 1): ?>
	<div class="uk-float-right uk-text-muted">
		<?php echo Html::_('image', 'media/com_getbible/images/icon.png', 'getBible.net Logo', ['uk-tooltip' => 'title: ' . Text::_('COM_GETBIBLE_THE_WORDS_OF_ETERNAL_LIFE') . '; pos: left']); ?>
	</div>
<?php endif; ?>
