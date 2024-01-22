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
<div class="uk-section-default uk-section">
	<div class="uk-container">
		<div class="tm-grid-expand uk-child-width-1-1 uk-grid-margin uk-grid uk-grid-stack" uk-grid>
			<div class="uk-first-column">
				<div class="uk-margin">
					<h1><?php echo $this->tag->name ?? Text::_('COM_GETBIBLE_NO_TAG_SELECTED'); ?></h1>
					<p class="uk-text-meta"><?php echo $this->tag->description ?? ''; ?></p>
					<div uk-alert>
						<h3><?php echo Text::_('COM_GETBIBLE_NO_TAGGED_VERSES_FOUND'); ?>!</h3>
						<p><?php echo Text::_("COM_GETBIBLE_THIS_TAG_CURRENTLY_DOESNT_HAVE_ANY_VERSES_LINKED_TO_IT_PLEASE_SELECT_ANOTHER_TAG_OR_ATTACH_SOME_VERSES_TO_THIS_ONE"); ?></p>
						<a href="<?php echo $this->getBibleUrl(); ?>" class="uk-button uk-button-default uk-width-1-1 uk-margin-small-bottom"><?php echo Text::_('COM_GETBIBLE_BACK_TO_BIBLE'); ?></a>
					</div>
					<?php echo $this->loadTemplate('getbibleselecttags'); ?>
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
