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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Prompts List Model
 */
class GetbibleModelPrompts extends ListModel
{
	public function __construct($config = array())
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
				'a.name','name',
				'a.cache_behaviour','cache_behaviour',
				'g.translation','abbreviation',
				'a.model','model',
				'a.integration','integration'
			);
		}

		parent::__construct($config);
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
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

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

		$name = $this->getUserStateFromRequest($this->context . '.filter.name', 'filter_name');
		if ($formSubmited)
		{
			$name = $app->input->post->get('name');
			$this->setState('filter.name', $name);
		}

		$cache_behaviour = $this->getUserStateFromRequest($this->context . '.filter.cache_behaviour', 'filter_cache_behaviour');
		if ($formSubmited)
		{
			$cache_behaviour = $app->input->post->get('cache_behaviour');
			$this->setState('filter.cache_behaviour', $cache_behaviour);
		}

		$abbreviation = $this->getUserStateFromRequest($this->context . '.filter.abbreviation', 'filter_abbreviation');
		if ($formSubmited)
		{
			$abbreviation = $app->input->post->get('abbreviation');
			$this->setState('filter.abbreviation', $abbreviation);
		}

		$model = $this->getUserStateFromRequest($this->context . '.filter.model', 'filter_model');
		if ($formSubmited)
		{
			$model = $app->input->post->get('model');
			$this->setState('filter.model', $model);
		}

		$integration = $this->getUserStateFromRequest($this->context . '.filter.integration', 'filter_integration');
		if ($formSubmited)
		{
			$integration = $app->input->post->get('integration');
			$this->setState('filter.integration', $integration);
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// Check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// Set values to display correctly.
		if (GetbibleHelper::checkArray($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !GetbibleHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			foreach ($items as $nr => &$item)
			{
				// Remove items the user can't access.
				$access = ($user->authorise('prompt.access', 'com_getbible.prompt.' . (int) $item->id) && $user->authorise('prompt.access', 'com_getbible'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}

		// set selection value to a translatable value
		if (GetbibleHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert integration
				$item->integration = $this->selectionTranslation($item->integration, 'integration');
				// convert cache_behaviour
				$item->cache_behaviour = $this->selectionTranslation($item->cache_behaviour, 'cache_behaviour');
				// convert model
				$item->model = $this->selectionTranslation($item->model, 'model');
			}
		}

        
		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of integration language strings
		if ($name === 'integration')
		{
			$integrationArray = array(
				1 => 'COM_GETBIBLE_PROMPT_WORDBASED',
				2 => 'COM_GETBIBLE_PROMPT_VERSEBASED',
				3 => 'COM_GETBIBLE_PROMPT_SELECTIONBASED'
			);
			// Now check if value is found in this array
			if (isset($integrationArray[$value]) && GetbibleHelper::checkString($integrationArray[$value]))
			{
				return $integrationArray[$value];
			}
		}
		// Array of cache_behaviour language strings
		if ($name === 'cache_behaviour')
		{
			$cache_behaviourArray = array(
				0 => 'COM_GETBIBLE_PROMPT_PERSISTENTLY_EXPANSIVE_CACHING',
				1 => 'COM_GETBIBLE_PROMPT_BASIC_CACHING_WORDSLANGUAGE',
				2 => 'COM_GETBIBLE_PROMPT_ADVANCED_CACHING_VERSECONTEX'
			);
			// Now check if value is found in this array
			if (isset($cache_behaviourArray[$value]) && GetbibleHelper::checkString($cache_behaviourArray[$value]))
			{
				return $cache_behaviourArray[$value];
			}
		}
		// Array of model language strings
		if ($name === 'model')
		{
			$modelArray = array(
				0 => 'COM_GETBIBLE_PROMPT_USE_GLOBAL',
				'gpt-4' => 'COM_GETBIBLE_PROMPT_GPT4',
				'gpt-4-0613' => 'COM_GETBIBLE_PROMPT_GPT40613',
				'gpt-4-32k' => 'COM_GETBIBLE_PROMPT_GPT432K',
				'gpt-4-32k-0613' => 'COM_GETBIBLE_PROMPT_GPT432K0613',
				'gpt-3.5-turbo' => 'COM_GETBIBLE_PROMPT_GPT35TURBO',
				'gpt-3.5-turbo-0613' => 'COM_GETBIBLE_PROMPT_GPT35TURBO0613',
				'gpt-3.5-turbo-16k' => 'COM_GETBIBLE_PROMPT_GPT35TURBO16K',
				'gpt-3.5-turbo-16k-0613' => 'COM_GETBIBLE_PROMPT_GPT35TURBO16K0613'
			);
			// Now check if value is found in this array
			if (isset($modelArray[$value]) && GetbibleHelper::checkString($modelArray[$value]))
			{
				return $modelArray[$value];
			}
		}
		return $value;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the getbible_item table
		$query->from($db->quoteName('#__getbible_prompt', 'a'));

		// From the getbible_translation table.
		$query->select($db->quoteName(['g.translation','g.id'],['abbreviation_translation','abbreviation_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_translation', 'g') . ' ON (' . $db->quoteName('a.abbreviation') . ' = ' . $db->quoteName('g.abbreviation') . ')');

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
		elseif (GetbibleHelper::checkArray($_access))
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
				$query->where('(a.name LIKE '.$search.' OR a.model LIKE '.$search.')');
			}
		}

		// Filter by Name.
		$_name = $this->getState('filter.name');
		if (is_numeric($_name))
		{
			if (is_float($_name))
			{
				$query->where('a.name = ' . (float) $_name);
			}
			else
			{
				$query->where('a.name = ' . (int) $_name);
			}
		}
		elseif (GetbibleHelper::checkString($_name))
		{
			$query->where('a.name = ' . $db->quote($db->escape($_name)));
		}
		// Filter by Cache_behaviour.
		$_cache_behaviour = $this->getState('filter.cache_behaviour');
		if (is_numeric($_cache_behaviour))
		{
			if (is_float($_cache_behaviour))
			{
				$query->where('a.cache_behaviour = ' . (float) $_cache_behaviour);
			}
			else
			{
				$query->where('a.cache_behaviour = ' . (int) $_cache_behaviour);
			}
		}
		elseif (GetbibleHelper::checkString($_cache_behaviour))
		{
			$query->where('a.cache_behaviour = ' . $db->quote($db->escape($_cache_behaviour)));
		}
		// Filter by Abbreviation.
		$_abbreviation = $this->getState('filter.abbreviation');
		if (is_numeric($_abbreviation))
		{
			if (is_float($_abbreviation))
			{
				$query->where('a.abbreviation = ' . (float) $_abbreviation);
			}
			else
			{
				$query->where('a.abbreviation = ' . (int) $_abbreviation);
			}
		}
		elseif (GetbibleHelper::checkString($_abbreviation))
		{
			$query->where('a.abbreviation = ' . $db->quote($db->escape($_abbreviation)));
		}
		// Filter by Model.
		$_model = $this->getState('filter.model');
		if (is_numeric($_model))
		{
			if (is_float($_model))
			{
				$query->where('a.model = ' . (float) $_model);
			}
			else
			{
				$query->where('a.model = ' . (int) $_model);
			}
		}
		elseif (GetbibleHelper::checkString($_model))
		{
			$query->where('a.model = ' . $db->quote($db->escape($_model)));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// Check if the value is an array
		$_access = $this->getState('filter.access');
		if (GetbibleHelper::checkArray($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_access)
		 || GetbibleHelper::checkString($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.name');
		$id .= ':' . $this->getState('filter.cache_behaviour');
		$id .= ':' . $this->getState('filter.abbreviation');
		$id .= ':' . $this->getState('filter.model');
		$id .= ':' . $this->getState('filter.integration');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_getbible')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = JFactory::getDbo();
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__getbible_prompt'));
			// Only select items that are checked out.
			$query->where($db->quoteName('checked_out') . '!=0');
			$db->setQuery($query, 0, 1);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date.
				$date = JFactory::getDate()->modify($time)->toSql();
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
				$query->update($db->quoteName('#__getbible_prompt'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
