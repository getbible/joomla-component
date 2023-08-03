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
<?php if (!empty($this->defaultTranslation)): ?>
<a class="uk-button uk-button-default uk-button-small uk-width-1-1" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $this->defaultTranslation->abbreviation . '&ref=' . $this->chapter->book_name . '&c=' . $this->chapter->chapter); ?>">
	<?php echo $this->defaultTranslation->translation; ?> (<?php echo $this->defaultTranslation->abbreviation; ?>)
</a>
<?php endif; ?>
<ul uk-accordion>
	<?php foreach ($this->languages as $language => $translations): ?>
	<?php if ($this->activeLanguage === $language): ?>
	<li class="uk-open">
	<?php else: ?>
	<li>
	<?php endif; ?>
		<a class="uk-accordion-title" href="#"><?php echo $language; ?></a>
		<div class="uk-accordion-content">
		<?php foreach ($translations as $translation): ?>
			<div class="uk-child-width-auto uk-grid-small" uk-grid>
				<div>
					<div class="uk-card">
						<?php if ($translation->abbreviation !== $this->chapter->abbreviation): ?>
							<a class="uk-button uk-button-default uk-button-small" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $translation->abbreviation . '&ref=' . $this->chapter->book_name . '&c=' . $this->chapter->chapter); ?>">
								<?php echo $translation->translation; ?> (<?php echo $translation->abbreviation; ?>)
							</a>
						<?php else: ?>
							<a class="uk-button uk-button-default uk-button-small uk-active" href="<?php echo JRoute::_('index.php?option=com_getbible&view=app&t=' . $translation->abbreviation . '&ref=' . $this->chapter->book_name . '&c=' . $this->chapter->chapter); ?>">
								<?php echo $translation->translation; ?> (<?php echo $translation->abbreviation; ?>)
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		</div>
	</li>
	<?php endforeach; ?>
</ul>
