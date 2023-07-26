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
<hr />
<h1><?php echo JText::_('COM_GETBIBLE_DEBUG'); ?></h1> 
<h4><a class="uk-link-heading" href="https://git.vdm.dev/getBible/support/issues" target="_blank" title="<?php echo JText::_('COM_GETBIBLE_FOUND_AN_ISSUE_REPORT_IT_TODAY'); ?>"><?php echo JText::_('COM_GETBIBLE_OPEN_AN_ISSUE'); ?></a></h4>
<ul uk-accordion>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo JText::_('COM_GETBIBLE_TAGGED_VERSES'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->items); ?>
</pre>
		</div>
	</li>
</ul>
