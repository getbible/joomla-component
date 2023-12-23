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
<hr />
<h1><?php echo Text::_('COM_GETBIBLE_DEBUG'); ?></h1> 
<h4><a class="uk-link-heading" href="https://git.vdm.dev/getBible/support/issues" target="_blank" title="<?php echo Text::_('COM_GETBIBLE_FOUND_AN_ISSUE_REPORT_IT_TODAY'); ?>"><?php echo Text::_('COM_GETBIBLE_OPEN_AN_ISSUE'); ?></a></h4>
<ul uk-accordion>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_QUERY'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->item); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_SCRIPTURE'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->chapter); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_TRANSLATIONS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->translations); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_TRANSLATION'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->translation); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_BOOKS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->books); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_CHAPTERS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->chapters); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_NEXT_CHAPTER'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->next); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_PREVIOUS_CHAPTER'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->previous); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_PARAMS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->params); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_TAB_NAME_PLACEHOLDERS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->tab_name_placeholders); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_NOTES'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->notes); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_TAGS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->tags); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_TAGGED_VERSES'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->taggedverses); ?>
</pre>
		</div>
	</li>
	<li>
		<a class="uk-accordion-title" href="#"><?php echo Text::_('COM_GETBIBLE_PROMPTS'); ?></a>
		<div class="uk-accordion-content">
<pre>
<?php var_dump($this->prompts); ?>
</pre>
		</div>
	</li>
</ul>
