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
<ul class="uk-dotnav uk-position-center">
	<?php foreach ($this->item as $response): ?>
	<li>
		<a href="#" onclick="filterResponseItemsByClass('<?php echo $response->response_id; ?>');"></a>
	</li>
	<?php 
		$last = $response->response_id; 
		endforeach;
	?>
</ul>

<script type="text/javascript">
window.onload = function() {
  filterResponseItemsByClass('<?php echo $last; ?>');
};
</script>
