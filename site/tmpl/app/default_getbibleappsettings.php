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
<div class="uk-margin-small-top">
	<ul class="el-nav uk-margin-small uk-subnav uk-subnav-pill uk-flex-center" uk-switcher="connect: #get-bible-app-settings">
		<li class="uk-active"><a href="#"><?php echo Text::_('COM_GETBIBLE_ACTIVE'); ?></a></li>
		<li><a href="#"><?php echo Text::_('COM_GETBIBLE_SESSIONS'); ?></a></li>
		<!-- <li><a href="#"><?php echo Text::_('COM_GETBIBLE_STYLE'); ?></a></li> -->
	</ul>
</div>

<ul id="get-bible-app-settings" class="uk-switcher" style="touch-action: pan-y pinch-zoom;">
	<li class="uk-margin-remove">
		<?php echo $this->loadTemplate('getbibleappactivesession'); ?>
	</li>
	<li class="uk-margin-remove">
		<div id="getbible-sessions-linker-details"><?php echo Text::_('COM_GETBIBLE_LOADING'); ?>...</div>
	</li>
	<!-- <li class="uk-margin-remove"> -->
		<!-- <div><?php echo Text::_('COM_GETBIBLE_STYLE_GO_HERE'); ?></div> -->
	<!-- </li> -->
</ul>
