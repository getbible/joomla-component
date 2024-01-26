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
<p>
	<?php foreach ($this->chapter->html_verses as $verse): ?>
			<?php
				echo LayoutHelper::render('getbibleverse', [
					'verse' => $verse,
					'active' => $this->active,
					'tag' => $this->taggedVerse((int) $verse->verse),
					'selected' => $this->selectedVerse((int) $verse->verse)
				]);
				// add note if found
				if (($note = $this->getVerseNote((int) $verse->verse)) !== null)
				{
					echo '&nbsp;' . LayoutHelper::render('getbibleappnotelink', ['number' => $verse->verse, 'note' => $note]);
				}
			?>
	<?php endforeach; ?>
</p>
