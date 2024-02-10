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
namespace TrueChristianChurch\Component\Getbible\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use Joomla\CMS\Helper\TagsHelper;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Utilities\StringHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Translations List Model
 *
 * @since  1.6
 */
class TranslationsModel extends ListModel
{
	/**
	 * The application object.
	 *
	 * @var   CMSApplicationInterface  The application instance.
	 * @since 3.2.0
	 */
	protected CMSApplicationInterface $app;

	/**
	 * Constructor
	 *
	 * @param   array                 $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   ?MVCFactoryInterface  $factory  The factory.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.access','access',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'a.direction','direction',
				'a.translation','translation',
				'a.abbreviation','abbreviation',
				'a.language','language'
			);
		}

		parent::__construct($config, $factory);

		$this->app ??= Factory::getApplication();
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since   1.7.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = $this->app;

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// Check if the form was submitted
		$formSubmited = $app->input->post->get('form_submited');

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$direction = $this->getUserStateFromRequest($this->context . '.filter.direction', 'filter_direction');
		if ($formSubmited)
		{
			$direction = $app->input->post->get('direction');
			$this->setState('filter.direction', $direction);
		}

		$translation = $this->getUserStateFromRequest($this->context . '.filter.translation', 'filter_translation');
		if ($formSubmited)
		{
			$translation = $app->input->post->get('translation');
			$this->setState('filter.translation', $translation);
		}

		$abbreviation = $this->getUserStateFromRequest($this->context . '.filter.abbreviation', 'filter_abbreviation');
		if ($formSubmited)
		{
			$abbreviation = $app->input->post->get('abbreviation');
			$this->setState('filter.abbreviation', $abbreviation);
		}

		$language = $this->getUserStateFromRequest($this->context . '.filter.language', 'filter_language');
		if ($formSubmited)
		{
			$language = $app->input->post->get('language');
			$this->setState('filter.language', $language);
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 * @since   1.6
	 */
	public function getItems()
	{
		// Check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// Set values to display correctly.
		if (UtilitiesArrayHelper::check($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !ObjectHelper::check($user))
			{
				$user = $this->getCurrentUser();
			}
			foreach ($items as $nr => &$item)
			{
				// Remove items the user can't access.
				$access = ($user->authorise('translation.access', 'com_getbible.translation.' . (int) $item->id) && $user->authorise('translation.access', 'com_getbible'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}

		// set selection value to a translatable value
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert direction
				$item->direction = $this->selectionTranslation($item->direction, 'direction');
			}
		}


		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return  string   The translatable string.
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of direction language strings
		if ($name === 'direction')
		{
			$directionArray = array(
				'LTR' => 'COM_GETBIBLE_TRANSLATION_SINISTRODEXTRAL_LTR',
				'RTL' => 'COM_GETBIBLE_TRANSLATION_DEXTROSINISTRAL_RTL'
			);
			// Now check if value is found in this array
			if (isset($directionArray[$value]) && StringHelper::check($directionArray[$value]))
			{
				return $directionArray[$value];
			}
		}
		return $value;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string    An SQL query
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = $this->getCurrentUser();
		// Create a new query object.
		$db = $this->getDatabase();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the getbible_item table
		$query->from($db->quoteName('#__getbible_translation', 'a'));

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (UtilitiesArrayHelper::check($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_getbible'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.translation LIKE '.$search.' OR a.abbreviation LIKE '.$search.' OR a.language LIKE '.$search.' OR a.direction LIKE '.$search.' OR a.distribution_about LIKE '.$search.' OR a.distribution_license LIKE '.$search.' OR a.distribution_source LIKE '.$search.' OR a.distribution_sourcetype LIKE '.$search.' OR a.distribution_versification LIKE '.$search.' OR a.sha LIKE '.$search.' OR a.encoding LIKE '.$search.' OR a.distribution_lcsh LIKE '.$search.' OR a.distribution_version_date LIKE '.$search.' OR a.distribution_version LIKE '.$search.' OR a.lang LIKE '.$search.' OR a.distribution_abbreviation LIKE '.$search.')');
			}
		}

		// Filter by Direction.
		$_direction = $this->getState('filter.direction');
		if (is_numeric($_direction))
		{
			if (is_float($_direction))
			{
				$query->where('a.direction = ' . (float) $_direction);
			}
			else
			{
				$query->where('a.direction = ' . (int) $_direction);
			}
		}
		elseif (StringHelper::check($_direction))
		{
			$query->where('a.direction = ' . $db->quote($db->escape($_direction)));
		}

		// Add the list ordering clause.
		$orderCol = $this->getState('list.ordering', 'a.language');
		$orderDirn = $this->getState('list.direction', 'asc');
		if ($orderCol != '')
		{
			// Check that the order direction is valid encase we have a field called direction as part of filers.
			$orderDirn = (is_string($orderDirn) && in_array(strtolower($orderDirn), ['asc', 'desc'])) ? $orderDirn : 'asc';
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		// Add a permanent list ordering.
		$query->order($db->escape('a.translation asc'));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 * @since   1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (UtilitiesArrayHelper::check($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_access)
		 || StringHelper::check($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.direction');
		$id .= ':' . $this->getState('filter.translation');
		$id .= ':' . $this->getState('filter.abbreviation');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return bool
	 * @since 3.2.0
	 */
	protected function checkInNow(): bool
	{
		// Get set check in time
		$time = ComponentHelper::getParams('com_getbible')->get('check_in');

		if ($time)
		{
			// Get a db connection.
			$db = $this->getDatabase();
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__getbible_translation'));
			// Only select items that are checked out.
			$query->where($db->quoteName('checked_out') . '!=0');
			$db->setQuery($query, 0, 1);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date.
				$date = Factory::getDate()->modify($time)->toSql();
				// Reset query.
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table.
				$query->update($db->quoteName('#__getbible_translation'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				return $db->execute();
			}
		}

		return false;
	}
}
