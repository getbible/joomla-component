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
 * Chapters List Model
 */
class GetbibleModelChapters extends ListModel
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
				'a.chapter','chapter',
				'a.book_nr','book_nr',
				'g.translation','abbreviation',
				'a.name','name'
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

		$chapter = $this->getUserStateFromRequest($this->context . '.filter.chapter', 'filter_chapter');
		if ($formSubmited)
		{
			$chapter = $app->input->post->get('chapter');
			$this->setState('filter.chapter', $chapter);
		}

		$book_nr = $this->getUserStateFromRequest($this->context . '.filter.book_nr', 'filter_book_nr');
		if ($formSubmited)
		{
			$book_nr = $app->input->post->get('book_nr');
			$this->setState('filter.book_nr', $book_nr);
		}

		$abbreviation = $this->getUserStateFromRequest($this->context . '.filter.abbreviation', 'filter_abbreviation');
		if ($formSubmited)
		{
			$abbreviation = $app->input->post->get('abbreviation');
			$this->setState('filter.abbreviation', $abbreviation);
		}

		$name = $this->getUserStateFromRequest($this->context . '.filter.name', 'filter_name');
		if ($formSubmited)
		{
			$name = $app->input->post->get('name');
			$this->setState('filter.name', $name);
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
				$access = ($user->authorise('chapter.access', 'com_getbible.chapter.' . (int) $item->id) && $user->authorise('chapter.access', 'com_getbible'));
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
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the getbible_item table
		$query->from($db->quoteName('#__getbible_chapter', 'a'));

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
				$query->where('(a.name LIKE '.$search.' OR a.chapter LIKE '.$search.' OR a.abbreviation LIKE '.$search.' OR g.translation LIKE '.$search.' OR a.sha LIKE '.$search.')');
			}
		}

		// Filter by Chapter.
		$_chapter = $this->getState('filter.chapter');
		if (is_numeric($_chapter))
		{
			if (is_float($_chapter))
			{
				$query->where('a.chapter = ' . (float) $_chapter);
			}
			else
			{
				$query->where('a.chapter = ' . (int) $_chapter);
			}
		}
		elseif (GetbibleHelper::checkString($_chapter))
		{
			$query->where('a.chapter = ' . $db->quote($db->escape($_chapter)));
		}
		elseif (GetbibleHelper::checkArray($_chapter))
		{
			// Secure the array for the query
			$_chapter = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (GetbibleHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_chapter);
			// Filter by the Chapter Array.
			$query->where('a.chapter IN (' . implode(',', $_chapter) . ')');
		}
		// Filter by Book_nr.
		$_book_nr = $this->getState('filter.book_nr');
		if (is_numeric($_book_nr))
		{
			if (is_float($_book_nr))
			{
				$query->where('a.book_nr = ' . (float) $_book_nr);
			}
			else
			{
				$query->where('a.book_nr = ' . (int) $_book_nr);
			}
		}
		elseif (GetbibleHelper::checkString($_book_nr))
		{
			$query->where('a.book_nr = ' . $db->quote($db->escape($_book_nr)));
		}
		elseif (GetbibleHelper::checkArray($_book_nr))
		{
			// Secure the array for the query
			$_book_nr = array_map( function ($val) use(&$db) {
				if (is_numeric($val))
				{
					if (is_float($val))
					{
						return (float) $val;
					}
					else
					{
						return (int) $val;
					}
				}
				elseif (GetbibleHelper::checkString($val))
				{
					return $db->quote($db->escape($val));
				}
			}, $_book_nr);
			// Filter by the Book_nr Array.
			$query->where('a.book_nr IN (' . implode(',', $_book_nr) . ')');
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
		// Check if the value is an array
		$_chapter = $this->getState('filter.chapter');
		if (GetbibleHelper::checkArray($_chapter))
		{
			$id .= ':' . implode(':', $_chapter);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_chapter)
		 || GetbibleHelper::checkString($_chapter))
		{
			$id .= ':' . $_chapter;
		}
		// Check if the value is an array
		$_book_nr = $this->getState('filter.book_nr');
		if (GetbibleHelper::checkArray($_book_nr))
		{
			$id .= ':' . implode(':', $_book_nr);
		}
		// Check if this is only an number or string
		elseif (is_numeric($_book_nr)
		 || GetbibleHelper::checkString($_book_nr))
		{
			$id .= ':' . $_book_nr;
		}
		$id .= ':' . $this->getState('filter.abbreviation');
		$id .= ':' . $this->getState('filter.name');

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
			$query->from($db->quoteName('#__getbible_chapter'));
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
				$query->update($db->quoteName('#__getbible_chapter'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
