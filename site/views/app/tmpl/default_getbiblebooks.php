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

?>
<div class="uk-child-width-auto uk-grid-small" uk-grid>
	<?php foreach ($this->books as $book): ?>
		<div>
			<div class="uk-card">
				<?php if ($book->nr !== $this->chapter->book_nr): ?>
					<a class="uk-button uk-button-default" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $book->abbreviation . '&ref=' . $book->name . '&c=1&tab=chapters'); ?>">
						<?php echo $book->name; ?>
					</a>
				<?php else: ?>
					<a class="uk-button uk-button-default uk-active" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $book->abbreviation . '&ref=' . $book->name . '&c=' . $this->chapter->chapter); ?>">
						<?php echo $book->name; ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
