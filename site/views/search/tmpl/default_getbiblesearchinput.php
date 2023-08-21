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
<div dir="<?php echo $this->translation->direction; ?>">
	<?php echo $this->loadTemplate('getbiblesearchbox'); ?>
	<?php echo $this->loadTemplate('getbiblesearchoptions'); ?>
</div>
<script type="text/javascript">
	// set the options and form triggers
	const searchField = document.getElementById('getbible-search-field');
	const searchFieldTranslation = document.getElementById('getbible-search-translation');
	const searchFieldWord = document.getElementById('getbible-search-words');
	const searchFieldMatch = document.getElementById('getbible-search-match');
	const searchFieldCase = document.getElementById('getbible-search-case');
	const searchFieldTarget = document.getElementById('getbible-search-target');
	// Event listeners for the search field and options
	searchField.addEventListener('keydown', function(event) {
		if (event.key === 'Enter') {
			event.preventDefault();
			if (searchField.value.length > 0) {
				handleSearch();
			}
		}
	});
	document.querySelectorAll('.getbible-search-option').forEach(function(select) {
		select.addEventListener('change', function(event) {
			if (searchField.value.length > 0) {
				handleSearch();
			}
		});
	});
</script>
