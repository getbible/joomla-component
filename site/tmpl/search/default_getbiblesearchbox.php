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
<nav class="uk-navbar-container uk-margin direction-<?php echo strtolower($this->translation->direction); ?>" uk-navbar>
	<div class="uk-navbar-left">
		<ul class="uk-navbar-nav">
			<li><a href="<?php echo $this->getBibleUrl(); ?>"><?php echo Text::_('COM_GETBIBLE_BIBLE'); ?></a></li>
		</ul>
		<div class="uk-navbar-item">
			<form class="uk-search uk-search-navbar">
				<span uk-search-icon></span>
				<input id="getbible-search-field" class="uk-search-input" type="search" placeholder="<?php echo Text::_('COM_GETBIBLE_SEARCH'); ?>" aria-label="<?php echo Text::_('COM_GETBIBLE_SEARCH'); ?>" value="<?php echo $this->getSearch(); ?>">
			</form>
		</div>
		<ul class="uk-navbar-nav">
			<li><a href="#" onclick="event.preventDefault(); handleSearch();"><?php echo Text::_('COM_GETBIBLE_SEARCH'); ?></a></li>
		</ul>
	</div>
</nav>
<script>
window.onload = function() {
	let input = document.getElementById('getbible-search-field');
	let value = input.value;
	input.focus();
	input.value = '';
	input.value = value;
}
</script>
