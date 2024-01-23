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
defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;

$id = (isset($displayData['id'])) ? $displayData['id'] : '';
$name = (isset($displayData['name'])) ? $displayData['name'] : $id;
$name = str_replace('-', '_', $name);
$label = (isset($displayData['label'])) ? $displayData['label'] : Text::_('COM_GETBIBLE_LABEL');
$class_label = (isset($displayData['class_label'])) ? $displayData['class_label'] : 'uk-form-label';
$class_other_label = (isset($displayData['class_other_label'])) ? ' ' . $displayData['class_other_label'] : '';
$margin = (isset($displayData['margin'])) ? $displayData['margin'] : 'uk-margin-small';

?>
<div class="<?php echo $margin; ?>">
	<label class="<?php echo $class_label ; echo $class_other_label; ?>" for="<?php echo $name; ?>"><?php echo $label; ?></label>
	<div class="uk-form-controls">
		<?php echo JLayoutHelper::render('input', $displayData); ?>
	</div>
</div>
