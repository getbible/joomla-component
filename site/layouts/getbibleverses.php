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



use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianBible\Component\GetBible\Site\Helper\GetbibleHelper;

// No direct access to this file
defined('JPATH_BASE') or die;



?>
<ul class="uk-list uk-list-collapse">
	<?php foreach ($displayData as $verse): ?>
		<li><?php echo $verse['number']; ?> <?php echo $verse['text']; ?></li>
	<?php endforeach; ?>
</ul>
