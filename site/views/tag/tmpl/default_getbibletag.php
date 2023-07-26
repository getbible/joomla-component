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
<div class="uk-section-default uk-section">
	<div class="uk-container">
		<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
			<div class="uk-first-column">
				<div class="uk-margin">
					<?php if ($this->params->get('show_top_menu') == 1): ?>
						<?php echo $this->loadTemplate('getbibletagtopmenu'); ?>
					<?php endif; ?>
					<?php echo $this->loadTemplate('getbibletagbody'); ?>
					<?php if ($this->params->get('show_bottom_menu') == 1): ?>
						<?php echo $this->loadTemplate('getbibletagbottommenu'); ?>
					<?php endif; ?>
				</div>
				<?php if ($this->params->get('show_getbible_link') == 1): ?>
					<div class="uk-margin">
						<?php echo $this->loadTemplate('getbibletagfooter'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
