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
<div class="uk-child-width-auto uk-text-center" uk-grid>
	<?php foreach ($this->chapters as $chapter): ?>
		<div>
			<div class="uk-card">
				<?php if ($chapter->chapter !== $this->chapter->chapter): ?>
					<a class="uk-button uk-button-default" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $chapter->abbreviation . '&ref=' . $chapter->book_name . '&c=' . $chapter->chapter); ?>">
						<?php echo $chapter->chapter; ?>
					</a>
				<?php else: ?>
					<a class="uk-button uk-button-default uk-active" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $chapter->abbreviation . '&ref=' . $chapter->book_name . '&c=' . $chapter->chapter); ?>">
						<?php echo $chapter->chapter; ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
