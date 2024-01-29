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

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;

// No direct access to this file
defined('_JEXEC') or die;

$table_id = 'getbible_search_result_table';
$headers = [
	'abbreviation' => Text::_('COM_GETBIBLE_TRANSLATION'),
	'book_nr' => Text::_('COM_GETBIBLE_BOOK_NUMBER'),
	'name' => Text::_('COM_GETBIBLE_BOOK_NAME'),
	'chapter' => Text::_('COM_GETBIBLE_CHAPTER'),
	'verse' => Text::_('COM_GETBIBLE_VERSE'),
	'text' => Text::_('COM_GETBIBLE_SCRIPTURE')
];

?>
<?php if (count($this->items) >= 1000): ?>
	<small><?php echo Text::_('COM_GETBIBLE_KINDLY_BE_AWARE_THAT_THE_RESULTS_HAVE_BEEN_TRUNCATED_BY_REFINING_YOUR_SEARCH_PARAMETERS_YOU_CAN_SIGNIFICANTLY_ENHANCE_THE_ACCURACY_AND_RELEVANCE'); ?></small>
<?php endif; ?>
<?php echo LayoutHelper::render('table',
	[
		'id' => $table_id,
		'table_class' => 'uk-table uk-table-hover uk-table-striped uk-width-1-1 direction-' . strtolower($this->translation->direction) . '" dir="' . $this->translation->direction . '"',
		'name' => Text::_('COM_GETBIBLE_SEARCH_RESULTS'),
		'headers' => $headers,
		'items' => $this->items,
		'init' => false
	]
); ?>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function() {
		// Ensure that DataTables is loaded before running this script
		if (window.DataTable) {
			// Extending the DataTable defaults in pure JavaScript
			Object.assign(DataTable.defaults, {
				"searching": false
			});
		} else {
			console.error('DataTable is not defined');
		}
		let <?php echo $table_id; ?> = new DataTable('#<?php echo $table_id; ?>', {
			dom: '<"top"i>rt<"bottom"flp><"clear">',
			responsive: true,
			select: true,
			order: [[ 1, "asc" ]],
			scrollY: 400,
			lengthMenu: [
				[100, 250, 500, -1],
				[100, 250, 500, 'All'],
			],
			columnDefs: [
				{ 'targets': [ 0, 1 ], 'visible': false, 'searchable': false },
				{ responsivePriority: 1, targets: 1 },
				{ responsivePriority: 2, targets: -1 }
			],
			columns: [
				{
					data: 'abbreviation'
				},
				{
					data: 'book_nr'
				},
				{
					data: 'name'
				},
				{
					data: 'chapter'
				},
				{
					data: 'verse'
				},
				{
					data: 'text'
				}
			]
		});
		<?php echo $table_id; ?>.on( 'select', function ( e, dt, type, indexes ) {
			if ( type === 'row' ) {
				// get the data from the row
				let data = <?php echo $table_id; ?>.rows( indexes ).data();
				UIkit.modal.confirm('<?php echo Text::_('COM_GETBIBLE_REDIRECTING_TO'); ?>: ' + data[0].name + ' ' + data[0].chapter + ':' + data[0].verse).then(function () {
					handleApp(data[0].name, data[0].chapter, data[0].verse, data[0].abbreviation);
				}, function () {
					console.log('no redirection')
				});
			}
		});
	});
</script>
