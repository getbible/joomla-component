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
 * Targettranslations Form Field class for the Getbible component
 */
class JFormFieldTargettranslations extends JFormFieldList
{
	/**
	 * The targettranslations field type.
	 *
	 * @var		string
	 */
	public $type = 'targettranslations';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Get the databse object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.abbreviation','a.translation'),array('abbreviation','abbreviation_translation')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.translation ASC');
		// Implement View Level Access (if set in table)
		if (!$user->authorise('core.options', 'com_getbible'))
		{
			$columns = $db->getTableColumns('#__getbible_translation');
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
			$options[] = JHtml::_('select.option', 'all', JText::_('COM_GETBIBLE_ALL_TRANSLATIONS'));
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->abbreviation, $item->abbreviation_translation.' (' .$item->abbreviation.')');
			}
		}
		return $options;
	}
}
