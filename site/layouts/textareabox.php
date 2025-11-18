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

// Extract all keys from $displayData as individual variables.
extract($displayData);

// Assign default values for variables that might not be present in $displayData.

// The 'id' parameter, defaulting to an empty string if not set or is null.
$id ??= '';

// The 'name' parameter, defaulting to 'id' if not set. Additionally, replace hyphens with underscores.
$name ??= $id;
$name = str_replace('-', '_', $name);

// The 'label' parameter, defaulting to a translated 'Label' string if not set.
$label ??= Text::_('COM_GETBIBLE_LABEL');

// The 'margin' parameter, defaulting to 'uk-margin-small' if not set or is null.
$margin ??= 'uk-margin-small';

?>
<div class="<?php echo $margin; ?>">
	<label class="uk-form-label" for="<?php echo $name; ?>"><?php echo $label; ?></label>
	<div class="uk-form-controls">
		<?php echo LayoutHelper::render('textarea', $displayData); ?>
	</div>
</div>
