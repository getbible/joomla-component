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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Versesfilterbooknr Form Field class for the Getbible component
 */
class JFormFieldVersesfilterbooknr extends JFormFieldList
{
	/**
	 * The versesfilterbooknr field type.
	 *
	 * @var		string
	 */
	public $type = 'versesfilterbooknr';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('book_nr'));
		$query->from($db->quoteName('#__getbible_verse'));
		$query->order($db->quoteName('book_nr') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = array();
		$_filter[] = JHtml::_('select.option', '', '- ' . JText::_('COM_GETBIBLE_FILTER_SELECT_BOOK_NUMBER') . ' -');

		if ($_results)
		{
			$_results = array_unique($_results);
			foreach ($_results as $book_nr)
			{
				// Now add the book_nr and its text to the options array
				$_filter[] = JHtml::_('select.option', $book_nr, $book_nr);
			}
		}
		return $_filter;
	}
}
