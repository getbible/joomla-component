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
 * Notes List Model
 *
 * @since  1.6
 */
class NotesModel extends ListModel
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
				'a.book_nr','book_nr',
				'g.name','linker',
				'a.verse','verse',
				'a.chapter','chapter'
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

		$book_nr = $this->getUserStateFromRequest($this->context . '.filter.book_nr', 'filter_book_nr');
		if ($formSubmited)
		{
			$book_nr = $app->input->post->get('book_nr');
			$this->setState('filter.book_nr', $book_nr);
		}

		$linker = $this->getUserStateFromRequest($this->context . '.filter.linker', 'filter_linker');
		if ($formSubmited)
		{
			$linker = $app->input->post->get('linker');
			$this->setState('filter.linker', $linker);
		}

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$verse = $this->getUserStateFromRequest($this->context . '.filter.verse', 'filter_verse');
		if ($formSubmited)
		{
			$verse = $app->input->post->get('verse');
			$this->setState('filter.verse', $verse);
		}

		$chapter = $this->getUserStateFromRequest($this->context . '.filter.chapter', 'filter_chapter');
		if ($formSubmited)
		{
			$chapter = $app->input->post->get('chapter');
			$this->setState('filter.chapter', $chapter);
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
				$access = ($user->authorise('note.access', 'com_getbible.note.' . (int) $item->id) && $user->authorise('note.access', 'com_getbible'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

				$item->book_nr = $item->book_nr . ' ' . $item->chapter . ':' . $item->verse;
			}
		}

		// set selection value to a translatable value
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert access
				$item->access = $this->selectionTranslation($item->access, 'access');
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
		// Array of access language strings
		if ($name === 'access')
		{
			$accessArray = array(
				1 => 'COM_GETBIBLE_NOTE_PUBLIC',
				0 => 'COM_GETBIBLE_NOTE_PRIVATE'
			);
			// Now check if value is found in this array
			if (isset($accessArray[$value]) && StringHelper::check($accessArray[$value]))
			{
				return $accessArray[$value];
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
		$query->from($db->quoteName('#__getbible_note', 'a'));

		// From the getbible_linker table.
		$query->select($db->quoteName(['g.name','g.id'],['linker_name','linker_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_linker', 'g') . ' ON (' . $db->quoteName('a.linker') . ' = ' . $db->quoteName('g.guid') . ')');

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
				$query->where('(a.book_nr LIKE '.$search.' OR a.linker LIKE '.$search.' OR g.name LIKE '.$search.' OR a.note LIKE '.$search.')');
			}
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
		elseif (StringHelper::check($_book_nr))
		{
			$query->where('a.book_nr = ' . $db->quote($db->escape($_book_nr)));
		}
		// Filter by Linker.
		$_linker = $this->getState('filter.linker');
		if (is_numeric($_linker))
		{
			if (is_float($_linker))
			{
				$query->where('a.linker = ' . (float) $_linker);
			}
			else
			{
				$query->where('a.linker = ' . (int) $_linker);
			}
		}
		elseif (StringHelper::check($_linker))
		{
			$query->where('a.linker = ' . $db->quote($db->escape($_linker)));
		}
		// Filter by Access.
		$_access = $this->getState('filter.access');
		if (is_numeric($_access))
		{
			if (is_float($_access))
			{
				$query->where('a.access = ' . (float) $_access);
			}
			else
			{
				$query->where('a.access = ' . (int) $_access);
			}
		}
		elseif (StringHelper::check($_access))
		{
			$query->where('a.access = ' . $db->quote($db->escape($_access)));
		}
		// Filter by Verse.
		$_verse = $this->getState('filter.verse');
		if (is_numeric($_verse))
		{
			if (is_float($_verse))
			{
				$query->where('a.verse = ' . (float) $_verse);
			}
			else
			{
				$query->where('a.verse = ' . (int) $_verse);
			}
		}
		elseif (StringHelper::check($_verse))
		{
			$query->where('a.verse = ' . $db->quote($db->escape($_verse)));
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
		elseif (StringHelper::check($_chapter))
		{
			$query->where('a.chapter = ' . $db->quote($db->escape($_chapter)));
		}

		// Add the list ordering clause.
		$orderCol = $this->getState('list.ordering', 'a.id');
		$orderDirn = $this->getState('list.direction', 'desc');
		if ($orderCol != '')
		{
			// Check that the order direction is valid encase we have a field called direction as part of filers.
			$orderDirn = (is_string($orderDirn) && in_array(strtolower($orderDirn), ['asc', 'desc'])) ? $orderDirn : 'desc';
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

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
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.book_nr');
		$id .= ':' . $this->getState('filter.linker');
		$id .= ':' . $this->getState('filter.verse');
		$id .= ':' . $this->getState('filter.chapter');

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
			$query->from($db->quoteName('#__getbible_note'));
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
				$query->update($db->quoteName('#__getbible_note'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				return $db->execute();
			}
		}

		return false;
	}
}
