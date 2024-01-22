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
 * Openaimessagesfilterrole Form Field class for the Getbible component
 */
class JFormFieldOpenaimessagesfilterrole extends JFormFieldList
{
	/**
	 * The openaimessagesfilterrole field type.
	 *
	 * @var        string
	 */
	public $type = 'openaimessagesfilterrole';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return    array    An array of Html options.
	 */
	protected function getOptions()
	{
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('role'));
		$query->from($db->quoteName('#__getbible_open_ai_message'));
		$query->order($db->quoteName('role') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$_results = $db->loadColumn();
		$_filter = [];
		$_filter[] = Html::_('select.option', '', '- ' . Text::_('COM_GETBIBLE_FILTER_SELECT_ROLE') . ' -');

		if ($_results)
		{
			// get open_ai_messagesmodel
			$_model = GetbibleHelper::getModel('open_ai_messages');
			$_results = array_unique($_results);
			foreach ($_results as $role)
			{
				// Translate the role selection
				$_text = $_model->selectionTranslation($role,'role');
				// Now add the role and its text to the options array
				$_filter[] = Html::_('select.option', $role, Text::_($_text));
			}
		}
		return $_filter;
	}
}
