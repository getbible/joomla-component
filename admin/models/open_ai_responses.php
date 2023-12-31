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
 * Open_ai_responses List Model
 */
class GetbibleModelOpen_ai_responses extends ListModel
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
				'a.response_id','response_id',
				'g.name','prompt',
				'a.response_model','response_model',
				'a.response_object','response_object',
				'a.total_tokens','total_tokens'
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

		$response_id = $this->getUserStateFromRequest($this->context . '.filter.response_id', 'filter_response_id');
		if ($formSubmited)
		{
			$response_id = $app->input->post->get('response_id');
			$this->setState('filter.response_id', $response_id);
		}

		$prompt = $this->getUserStateFromRequest($this->context . '.filter.prompt', 'filter_prompt');
		if ($formSubmited)
		{
			$prompt = $app->input->post->get('prompt');
			$this->setState('filter.prompt', $prompt);
		}

		$response_model = $this->getUserStateFromRequest($this->context . '.filter.response_model', 'filter_response_model');
		if ($formSubmited)
		{
			$response_model = $app->input->post->get('response_model');
			$this->setState('filter.response_model', $response_model);
		}

		$response_object = $this->getUserStateFromRequest($this->context . '.filter.response_object', 'filter_response_object');
		if ($formSubmited)
		{
			$response_object = $app->input->post->get('response_object');
			$this->setState('filter.response_object', $response_object);
		}

		$total_tokens = $this->getUserStateFromRequest($this->context . '.filter.total_tokens', 'filter_total_tokens');
		if ($formSubmited)
		{
			$total_tokens = $app->input->post->get('total_tokens');
			$this->setState('filter.total_tokens', $total_tokens);
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
				$access = ($user->authorise('open_ai_response.access', 'com_getbible.open_ai_response.' . (int) $item->id) && $user->authorise('open_ai_response.access', 'com_getbible'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}

		// return items
		return $items;
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
		$query->from($db->quoteName('#__getbible_open_ai_response', 'a'));

		// From the getbible_prompt table.
		$query->select($db->quoteName(['g.name','g.id'],['prompt_name','prompt_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_prompt', 'g') . ' ON (' . $db->quoteName('a.prompt') . ' = ' . $db->quoteName('g.guid') . ')');

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
				$query->where('(a.response_object LIKE '.$search.' OR a.response_model LIKE '.$search.')');
			}
		}

		// Filter by Response_id.
		$_response_id = $this->getState('filter.response_id');
		if (is_numeric($_response_id))
		{
			if (is_float($_response_id))
			{
				$query->where('a.response_id = ' . (float) $_response_id);
			}
			else
			{
				$query->where('a.response_id = ' . (int) $_response_id);
			}
		}
		elseif (GetbibleHelper::checkString($_response_id))
		{
			$query->where('a.response_id = ' . $db->quote($db->escape($_response_id)));
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
		// Filter by Response_model.
		$_response_model = $this->getState('filter.response_model');
		if (is_numeric($_response_model))
		{
			if (is_float($_response_model))
			{
				$query->where('a.response_model = ' . (float) $_response_model);
			}
			else
			{
				$query->where('a.response_model = ' . (int) $_response_model);
			}
		}
		elseif (GetbibleHelper::checkString($_response_model))
		{
			$query->where('a.response_model = ' . $db->quote($db->escape($_response_model)));
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
		$id .= ':' . $this->getState('filter.response_id');
		$id .= ':' . $this->getState('filter.prompt');
		$id .= ':' . $this->getState('filter.response_model');
		$id .= ':' . $this->getState('filter.response_object');
		$id .= ':' . $this->getState('filter.total_tokens');

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
			$query->from($db->quoteName('#__getbible_open_ai_response'));
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
				$query->update($db->quoteName('#__getbible_open_ai_response'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
