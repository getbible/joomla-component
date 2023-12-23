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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Taggedversesfilteraccess Form Field class for the Getbible component
 */
class JFormFieldTaggedversesfilteraccess extends JFormFieldList
{
	/**
	 * The taggedversesfilteraccess field type.
	 *
	 * @var		string
	 */
	public $type = 'taggedversesfilteraccess';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('access'));
		$query->from($db->quoteName('#__getbible_tagged_verse'));
		$query->order($db->quoteName('access') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = array();
		$_filter[] = Html::_('select.option', '', '- ' . Text::_('COM_GETBIBLE_FILTER_SELECT_ACCESS') . ' -');

		if ($_results)
		{
			// get tagged_versesmodel
			$_model = GetbibleHelper::getModel('tagged_verses');
			$_results = array_unique($_results);
			foreach ($_results as $access)
			{
				// Translate the access selection
				$_text = $_model->selectionTranslation($access,'access');
				// Now add the access and its text to the options array
				$_filter[] = Html::_('select.option', $access, Text::_($_text));
			}
		}
		return $_filter;
	}
}
