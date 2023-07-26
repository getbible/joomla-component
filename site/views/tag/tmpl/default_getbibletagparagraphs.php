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

?>
<div class="uk-child-width-1-3@m uk-grid-small getbible-tag-filter" uk-grid="masonry: true">
	<?php foreach ($this->items as $paragraph): ?>
		<div data-book="<?php echo $paragraph['data_book']; ?>">
			<div class="uk-box-shadow-small uk-padding"><?php echo JLayoutHelper::render('getbibleparagraph', $paragraph); ?></div>
        	</div>
	<?php endforeach; ?>
</div>
<?php if (!empty($this->books) && is_array($this->books) && count($this->books) > 1): ?>
</div>
<?php endif; ?>
