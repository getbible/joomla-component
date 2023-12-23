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
<h2 class="uk-card-title"><?php echo $this->translation->translation; ?> (<?php echo $this->translation->abbreviation; ?> - <?php echo $this->translation->distribution_version; ?>)</h2>
<span class="uk-text-muted"><?php echo $this->translation->distribution_version_date; ?></span>
<h3><?php echo $this->translation->language; ?> (<?php echo $this->translation->lang; ?>)</h3>
<p><?php echo str_replace('\par', '<br />', $this->translation->distribution_about); ?></p>
<ul class="uk-list uk-list-circle uk-list-collapse uk-list-muted">
	<?php if (isset($this->translation->encoding) && strlen($this->translation->encoding) > 0): ?>
		<li><?php echo Text::_('COM_GETBIBLE_ENCODING'); ?>: <?php echo $this->translation->encoding;  ?></li>
	<?php endif; ?>
	<?php if (isset($this->translation->direction) && strlen($this->translation->direction) > 0): ?>
		<li><?php echo Text::_('COM_GETBIBLE_DIRECTION'); ?>: <?php echo $this->translation->direction;  ?></li>
	<?php endif; ?>
	<?php if (isset($this->translation->distribution_lcsh) && strlen($this->translation->distribution_lcsh) > 0): ?>
		<li>LCSH: <?php echo $this->translation->distribution_lcsh;  ?></li>
	<?php endif; ?>
	<?php if (isset($this->translation->distribution_abbreviation) && strlen($this->translation->distribution_abbreviation) > 0): ?>
		<li><?php echo Text::_('COM_GETBIBLE_DISTRIBUTION_ABBREVIATION'); ?>: <?php echo $this->translation->distribution_abbreviation;  ?></li>
	<?php endif; ?>
</ul>
<h4><?php echo Text::_('COM_GETBIBLE_LICENSE'); ?></h4>
<p><?php echo $this->translation->distribution_license; ?></p>
<h4><?php echo Text::_('COM_GETBIBLE_SOURCE'); ?> (<?php echo $this->translation->distribution_sourcetype; ?>)</h4>
<p><?php echo $this->translation->distribution_source; ?></p>
<?php if (isset($this->translation->distribution_history) && is_array($this->translation->distribution_history) && $this->translation->distribution_history !== []): ?>
	<dl class="uk-description-list">
		<?php foreach ($this->translation->distribution_history as $history): ?>
			<dt><?php echo $history['version']; ?></dt>
			<dd><?php echo $history['description']; ?></dd>
		<?php endforeach; ?>
	</dl>
<?php endif; ?>
