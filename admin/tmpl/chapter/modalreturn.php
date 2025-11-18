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

use Joomla\CMS\Router\Route;

// No direct access to this file
defined('_JEXEC') or die;

/** @var \TrueChristianBible\Component\GetBible\Administrator\View\Chapter\HtmlView $this */

$icon = 'icon-check';
$title_key = $this->item->id ?? '';
$title_column = $this->item->name ?? '';
$data = [
	'contentType' => 'com_getbible.chapter',
	'id' => $title_key,
	'title' => $title_column,
	'uri' => Route::_('index.php?option=com_getbible&layout=modal&tmpl=component&id='. (int) ($this->item->id ?? 0))
];

// Add Content select script
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('modal-content-select');

// The data for Content select script
$this->getDocument()->addScriptOptions('content-select-on-load', $data, false);

?>

<div class="px-4 py-5 my-5 text-center">
    <span class="fa-8x mb-4 <?php echo $icon; ?>" aria-hidden="true"></span>
    <h1 class="display-5 fw-bold"><?php echo $title_column; ?></h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">
            <?php echo $title_column; ?>
        </p>
    </div>
</div>