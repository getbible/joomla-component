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
defined('JPATH_BASE') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

$id = (isset($displayData['id'])) ? $displayData['id'] : '';
$name = (isset($displayData['name'])) ? $displayData['name'] : $id;
$name = str_replace('-', '_', $name);
$label = (isset($displayData['label'])) ? $displayData['label'] : Text::_('COM_GETBIBLE_LABEL');
$margin = (isset($displayData['margin'])) ? $displayData['margin'] : 'uk-margin-small';

?>
<div class="<?php echo $margin; ?>">
	<label class="uk-form-label" for="<?php echo $name; ?>"><?php echo $label; ?></label>
	<div class="uk-form-controls">
		<?php echo LayoutHelper::render('select', $displayData); ?>
	</div>
</div>
