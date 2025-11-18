<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    2015-12-03 01:42:15
    @author     Llewellyn van der Merwe <https://getbible.life>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;

// No direct access to this file
defined('_JEXEC') or die;

?>
<div class="uk-child-width-1-4@m uk-grid-small" uk-grid>
	<?php foreach ($this->tags as $tag): ?>
		<div>
			<?php if (!empty($this->tag) && isset($this->tag->guid) && $tag->guid == $this->tag->guid) :?>
				<button class="uk-button uk-width-1-1" uk-tooltip="<?php echo Text::_('COM_GETBIBLE_ACTIVE_TAG'); ?>" disabled><span uk-icon="icon: tag"></span> <b><?php echo $tag->name; ?></b></button>
			<?php else: ?>
				<a class="uk-button uk-button-default uk-width-1-1" href="<?php echo $tag->url; ?>"  uk-tooltip="<?php echo $tag->description; ?>"><span uk-icon="icon: tag"></span> <?php echo $tag->name; ?></a>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
