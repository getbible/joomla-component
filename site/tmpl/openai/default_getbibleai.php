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

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;

// No direct access to this file
defined('_JEXEC') or die;

?>
<div class="uk-section-default uk-section">
	<div class="uk-container">
		<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
			<div class="uk-first-column">
				<a class="uk-button uk-button-small uk-width-1-1" href="<?php echo $this->getBibleUrl(); ?>"><?php echo Text::_('COM_GETBIBLE_BIBLE'); ?></a>
				<h2 class="uk-heading-small uk-heading-line uk-text-center"><?php echo Text::_('COM_GETBIBLE_OPEN_AI_RESPONSE'); ?></h2>
				<div class="uk-margin">
					<?php if ($this->params->get('show_top_menu') == 1): ?>
						<?php echo $this->loadTemplate('getbibleaitopmenu'); ?>
					<?php endif; ?>
					<?php echo $this->loadTemplate('getbibleaibody'); ?>
					<?php if ($this->params->get('show_bottom_menu') == 1): ?>
						<?php echo $this->loadTemplate('getbibleaibottommenu'); ?>
					<?php endif; ?>
				</div>
				<?php if ($this->params->get('show_getbible_link') == 1): ?>
					<div class="uk-margin">
						<?php echo $this->loadTemplate('getbibleaifooter'); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
