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
 * Linkersfilterpublicnotes Form Field class for the Getbible component
 */
class JFormFieldLinkersfilterpublicnotes extends JFormFieldList
{
	/**
	 * The linkersfilterpublicnotes field type.
	 *
	 * @var		string
	 */
	public $type = 'linkersfilterpublicnotes';

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
		$query->select($db->quoteName('public_notes'));
		$query->from($db->quoteName('#__getbible_linker'));
		$query->order($db->quoteName('public_notes') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = array();
		$_filter[] = JHtml::_('select.option', '', '- ' . JText::_('COM_GETBIBLE_FILTER_SELECT_PUBLIC_NOTES') . ' -');

		if ($_results)
		{
			// get linkersmodel
			$_model = GetbibleHelper::getModel('linkers');
			$_results = array_unique($_results);
			foreach ($_results as $public_notes)
			{
				// Translate the public_notes selection
				$_text = $_model->selectionTranslation($public_notes,'public_notes');
				// Now add the public_notes and its text to the options array
				$_filter[] = JHtml::_('select.option', $public_notes, JText::_($_text));
			}
		}
		return $_filter;
	}
}
