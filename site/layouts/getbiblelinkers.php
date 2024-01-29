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



use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;

// No direct access to this file
defined('JPATH_BASE') or die;

$load = false;

if (!empty($displayData))
{
	foreach ($displayData as $linker)
	{
		if (!empty($linker->name))
		{
			$load = true;
			break;
		}
	}
}

?>
<?php if ($load): ?>
<ul class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@s" uk-grid>
	<?php foreach ($displayData as $linker): ?>
		<?php if ($linker->name !== null): ?>
			<li id="getbible-local-linker-display-<?php echo $linker->guid; ?>">
				<?php echo LayoutHelper::render('inputbox', ['id' => 'get-session-name-' . $linker->guid, 'label' => Text::_('COM_GETBIBLE_SESSION_NAME'), 'value' => $linker->name, 'readonly' => true]); ?>
				<?php echo LayoutHelper::render('inputbox', ['id' => 'get-session-key-' . $linker->guid, 'label' => Text::_('COM_GETBIBLE_SESSION_KEY'), 'value' => $linker->guid, 'readonly' => true]); ?>
				<div class="uk-button-group uk-width-1-1">
					<button class="uk-button uk-button-default uk-width-1-3" onclick="loadGetBiblePersistentSessionLinker('<?php echo $linker->guid; ?>');"><?php echo Text::_('COM_GETBIBLE_LOAD'); ?></button>
					<button class="uk-button uk-button-default uk-width-1-3" onclick="copyGetBiblePersistentSessionUrl('<?php echo $linker->guid; ?>');"><?php echo Text::_('COM_GETBIBLE_COPY'); ?></button>
					<button class="uk-button uk-button-default uk-width-1-3" onclick="removeGetBiblePersistentSession('<?php echo $linker->guid; ?>');"><?php echo Text::_('COM_GETBIBLE_REMOVE'); ?></button>
				</div>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<div class="uk-alert-success" uk-alert>
	<h3><?php echo Text::_('COM_GETBIBLE_THIS_AREA_DISPLAYS_YOUR_RECENTLY_ACCESSED_SESSIONS'); ?></h3>
	<p><?php echo Text::_("COM_GETBIBLE_IF_YOU_SWITCH_FROM_YOUR_ACTIVE_SESSION_TO_A_SHARED_ONE_YOUR_ORIGINAL_SESSION_WILL_BE_PRESERVED_HERE_FOR_EASY_ACCESS"); ?></p>
</div>
<?php endif; ?>
