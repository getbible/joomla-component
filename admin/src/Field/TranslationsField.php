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
namespace TrueChristianChurch\Component\Getbible\Administrator\Field;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Component\ComponentHelper;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;

/**
 * Translations Form Field class for the Getbible component
 */
class TranslationsField extends ListField
{
	/**
	 * The translations field type.
	 *
	 * @var        string
	 */
	public $type = 'Translations';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array    An array of Html options.
	 * @since   1.6
	 */
	protected function getOptions()
	{
		// Get the user object.
		$user = Factory::getUser();
		// Get the databse object.
		$db = Factory::getDBO();
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
		$options = [];
		if (!empty($items))
		{
			if ($this->multiple === false)
			{
				$options[] = Html::_('select.option', '', Text::_('COM_GETBIBLE_SELECT_AN_OPTION'));
			}
			foreach($items as $item)
			{
				$options[] = Html::_('select.option', $item->abbreviation, $item->abbreviation_translation.' (' .$item->abbreviation.')');
			}
		}

		// if none was found we load the KJV as the default
		if (empty($options))
		{
			$options = [];
			if ($this->multiple === false)
			{
				$options[] = Html::_('select.option', '', Text::_('COM_GETBIBLE_SELECT_AN_OPTION'));
			}
			$options[] = Html::_('select.option', 'kjv', 'King James Version (kjv)'); // this is the default at all times.
		}

		return $options;
	}
}
