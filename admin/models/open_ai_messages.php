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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Open_ai_messages List Model
 */
class GetbibleModelOpen_ai_messages extends ListModel
{
	public function __construct($config = [])
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
				'a.role','role',
				'g.response_id','open_ai_response',
				'h.name','prompt',
				'a.source','source'
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
		$app = Factory::getApplication();

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

		$role = $this->getUserStateFromRequest($this->context . '.filter.role', 'filter_role');
		if ($formSubmited)
		{
			$role = $app->input->post->get('role');
			$this->setState('filter.role', $role);
		}

		$open_ai_response = $this->getUserStateFromRequest($this->context . '.filter.open_ai_response', 'filter_open_ai_response');
		if ($formSubmited)
		{
			$open_ai_response = $app->input->post->get('open_ai_response');
			$this->setState('filter.open_ai_response', $open_ai_response);
		}

		$prompt = $this->getUserStateFromRequest($this->context . '.filter.prompt', 'filter_prompt');
		if ($formSubmited)
		{
			$prompt = $app->input->post->get('prompt');
			$this->setState('filter.prompt', $prompt);
		}

		$source = $this->getUserStateFromRequest($this->context . '.filter.source', 'filter_source');
		if ($formSubmited)
		{
			$source = $app->input->post->get('source');
			$this->setState('filter.source', $source);
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
		if (UtilitiesArrayHelper::check($items))
		{
			// Get the user object if not set.
			if (!isset($user) || !ObjectHelper::check($user))
			{
				$user = Factory::getUser();
			}
			foreach ($items as $nr => &$item)
			{
				// Remove items the user can't access.
				$access = ($user->authorise('open_ai_message.access', 'com_getbible.open_ai_message.' . (int) $item->id) && $user->authorise('open_ai_message.access', 'com_getbible'));
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
				// convert role
				$item->role = $this->selectionTranslation($item->role, 'role');
				// convert source
				$item->source = $this->selectionTranslation($item->source, 'source');
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
		// Array of role language strings
		if ($name === 'role')
		{
			$roleArray = array(
				'system' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_SYSTEM',
				'user' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_USER',
				'assistant' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_ASSISTANT',
				'function' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_FUNCTION'
			);
			// Now check if value is found in this array
			if (isset($roleArray[$value]) && StringHelper::check($roleArray[$value]))
			{
				return $roleArray[$value];
			}
		}
		// Array of source language strings
		if ($name === 'source')
		{
			$sourceArray = array(
				1 => 'COM_GETBIBLE_OPEN_AI_MESSAGE_PROMPT',
				2 => 'COM_GETBIBLE_OPEN_AI_MESSAGE_OPEN_AI'
			);
			// Now check if value is found in this array
			if (isset($sourceArray[$value]) && StringHelper::check($sourceArray[$value]))
			{
				return $sourceArray[$value];
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
		$user = Factory::getUser();
		// Create a new query object.
		$db = Factory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the getbible_item table
		$query->from($db->quoteName('#__getbible_open_ai_message', 'a'));

		// From the getbible_open_ai_response table.
		$query->select($db->quoteName(['g.response_id','g.id'],['open_ai_response_response_id','open_ai_response_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_open_ai_response', 'g') . ' ON (' . $db->quoteName('a.open_ai_response') . ' = ' . $db->quoteName('g.response_id') . ')');

		// From the getbible_prompt table.
		$query->select($db->quoteName(['h.name','h.id'],['prompt_name','prompt_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_prompt', 'h') . ' ON (' . $db->quoteName('a.prompt') . ' = ' . $db->quoteName('h.guid') . ')');

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
				$query->where('(a.content LIKE '.$search.')');
			}
		}

		// Filter by Role.
		$_role = $this->getState('filter.role');
		if (is_numeric($_role))
		{
			if (is_float($_role))
			{
				$query->where('a.role = ' . (float) $_role);
			}
			else
			{
				$query->where('a.role = ' . (int) $_role);
			}
		}
		elseif (GetbibleHelper::checkString($_role))
		{
			$query->where('a.role = ' . $db->quote($db->escape($_role)));
		}
		// Filter by Open_ai_response.
		$_open_ai_response = $this->getState('filter.open_ai_response');
		if (is_numeric($_open_ai_response))
		{
			if (is_float($_open_ai_response))
			{
				$query->where('a.open_ai_response = ' . (float) $_open_ai_response);
			}
			else
			{
				$query->where('a.open_ai_response = ' . (int) $_open_ai_response);
			}
		}
		elseif (GetbibleHelper::checkString($_open_ai_response))
		{
			$query->where('a.open_ai_response = ' . $db->quote($db->escape($_open_ai_response)));
		}
		// Filter by Prompt.
		$_prompt = $this->getState('filter.prompt');
		if (is_numeric($_prompt))
		{
			if (is_float($_prompt))
			{
				$query->where('a.prompt = ' . (float) $_prompt);
			}
			else
			{
				$query->where('a.prompt = ' . (int) $_prompt);
			}
		}
		elseif (GetbibleHelper::checkString($_prompt))
		{
			$query->where('a.prompt = ' . $db->quote($db->escape($_prompt)));
		}
		// Filter by Source.
		$_source = $this->getState('filter.source');
		if (is_numeric($_source))
		{
			if (is_float($_source))
			{
				$query->where('a.source = ' . (float) $_source);
			}
			else
			{
				$query->where('a.source = ' . (int) $_source);
			}
		}
		elseif (GetbibleHelper::checkString($_source))
		{
			$query->where('a.source = ' . $db->quote($db->escape($_source)));
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
		$id .= ':' . $this->getState('filter.role');
		$id .= ':' . $this->getState('filter.open_ai_response');
		$id .= ':' . $this->getState('filter.prompt');
		$id .= ':' . $this->getState('filter.source');

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
		$time = ComponentHelper::getParams('com_getbible')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = Factory::getDbo();
			// Reset query.
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__getbible_open_ai_message'));
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
				$query->update($db->quoteName('#__getbible_open_ai_message'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
