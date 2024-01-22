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
 * Openairesponses Form Field class for the Getbible component
 */
class JFormFieldOpenairesponses extends JFormFieldList
{
	/**
	 * The openairesponses field type.
	 *
	 * @var        string
	 */
	public $type = 'openairesponses';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return    array    An array of Html options.
	 */
	protected function getOptions()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Get the databse object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.response_id','a.response_id'),array('response_id','open_ai_response_response_id')));
		$query->from($db->quoteName('#__getbible_open_ai_response', 'a'));
		$query->order('a.response_id ASC');
		// Implement View Level Access (if set in table)
		if (!$user->authorise('core.options', 'com_getbible'))
		{
			$columns = $db->getTableColumns('#__getbible_open_ai_response');
			if(isset($columns['access']))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}
		}
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
			if ($this->multiple === false)
			{
				$options[] = JHtml::_('select.option', '', JText::_('COM_GETBIBLE_SELECT_AN_OPTION'));
			}
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->response_id, $item->open_ai_response_response_id);
			}
		}
		return $options;
	}
}
