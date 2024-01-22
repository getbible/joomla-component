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
<?php if ($this->linker['share'] && !empty($this->linker['guid'])): ?>
setLocalMemory('getbible_active_linker_guid', '<?php echo $this->linker['guid']; ?>');
const getbible_linker_guid = '<?php echo $this->linker['guid']; ?>';
linkerManager.set(<?php echo json_encode($this->linker); ?>);
<?php else: ?>
// make sure the linker is set and ready for use
const getbible_linker_guid = getLocalMemory('getbible_active_linker_guid', '<?php echo $this->linker['guid'] ?? 'empty'; ?>', true);
// update server if needed
if (getbible_linker_guid !== '<?php echo $this->linker['guid'] ?? 'empty'; ?>') {
	// check if we have pass
	setLinker(getbible_linker_guid).then((data) => {
		if (data.success) {
			location.reload();
		}
	});
} else {
	linkerManager.set(<?php echo json_encode($this->linker); ?>);
}
<?php endif; ?>
<?php if ($this->params->get('show_settings') == 1): ?>
linkerManager.all().then((data) => {
	if (data) {
		getLinkersDisplay(data);
	}
});
<?php // Loading for ajax JLayoutHelper::render('getbiblelinkers', [?]); ?>
<?php endif; ?>
