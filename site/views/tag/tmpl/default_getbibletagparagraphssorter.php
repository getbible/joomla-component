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
<?php if (!empty($this->books) && is_array($this->books) && count($this->books) > 1): ?>
<div uk-filter="target: .getbible-tag-filter">
	<ul class="uk-subnav uk-subnav-pill">
		<li class="uk-active" uk-filter-control><a href="#"><?php echo Text::_('COM_GETBIBLE_ALL'); ?></a></li>
		<?php foreach($this->books as $key => $name): ?>
		<li uk-filter-control="[data-book='<?php echo $key; ?>']"><a href="#"><?php echo $name; ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
