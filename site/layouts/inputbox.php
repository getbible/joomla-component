<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
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

// The 'name' parameter, defaulting to 'id' if not set or is null. Additionally, replace hyphens with underscores.
$name ??= $id;
$name = str_replace('-', '_', $name);

// The 'label' parameter, defaulting to the 'Label' translation if not set or is null.
$label ??= Text::_('COM_GETBIBLE_LABEL');

// The 'class_label' parameter, defaulting to 'uk-form-label' if not set or is null.
$class_label ??= 'uk-form-label';

// The 'class_other_label' parameter, prepended with a space if set, otherwise defaulting to an empty string.
$class_other_label = isset($class_other_label) ? ' ' . $class_other_label : '';

// The 'margin' parameter, defaulting to 'uk-margin-small' if not set or is null.
$margin ??= 'uk-margin-small';

?>
<div class="<?php echo $margin; ?>">
	<label class="<?php echo $class_label ; echo $class_other_label; ?>" for="<?php echo $name; ?>"><?php echo $label; ?></label>
	<div class="uk-form-controls">
		<?php echo LayoutHelper::render('input', $displayData); ?>
	</div>
</div>
