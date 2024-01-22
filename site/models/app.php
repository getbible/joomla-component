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
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;
use VDM\Joomla\GetBible\Factory as GetBibleFactory;
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\JsonHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;

/**
 * Getbible App Item Model
 */
class GetbibleModelApp extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_getbible.app';

	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$this->app = Factory::getApplication();
		$this->input = $this->app->input;
		// Get the itme main id
		$id = $this->input->getInt('id', null);
		$this->setState('app.id', $id);

		// Load the parameters.
		$params = $this->app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the article.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$this->user = Factory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->initSet = true;

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('app.id');

		// we add a Share_His_Word option to set the session key
		if (($linker = $this->input->getString('Share_His_Word', null)) !== null
			&& GetBibleFactory::_('GetBible.Linker')->valid($linker))
		{
			GetBibleFactory::_('GetBible.Linker')->trigger($linker);
		}

		// we get all the Scripture Details
		$this->translation = $this->input->getString('translation') ?? $this->input->getString('t', Helper::getParams('com_getbible')->get('default_translation', 'kjv'));
		$this->book = $this->input->getInt('book') ?? $this->input->getInt('b');
		$this->chapter = $this->input->getInt('chapter') ?? $this->input->getInt('c');
		$this->verses = $this->input->getString('verses') ?? $this->input->getString('verse') ?? $this->input->getString('v');
		$pk = 0;

		// set daily verse (STUFF)
		if (empty($this->book) && ($ref = $this->input->getString('ref')) !== null)
		{
			GetBibleFactory::_('DailyScripture')->load($ref);
		}
		else
		{
			GetBibleFactory::_('DailyScripture')->setActive($this->book, $this->chapter, $this->verses);
		}

		// load Daily Scripture if no book value was found
		if (empty($this->book))
		{
			$this->book = GetBibleFactory::_('DailyScripture')->book();
			$this->chapter = $this->chapter ?? GetBibleFactory::_('DailyScripture')->chapter();
			$this->verses = $this->verses?? GetBibleFactory::_('DailyScripture')->verses();
		}

		// if we still have nothing... were done here!
		if ($this->book === null)
		{
			return false;
		}

		// if chapter is null change it to one
		if ($this->chapter === null)
		{
			$this->chapter = 1;
		}

		if ($this->_item === null)
		{
			$this->_item = [];
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				// Get a db connection.
				$db = Factory::getDbo();

				// Create a new query object.
				$query = $db->getQuery(true);

				// Get data
				// we load the queried chapter
				if (!GetBibleFactory::_('GetBible.Watcher')->sync($this->translation, $this->book, $this->chapter))
				{
					$book = GetBibleFactory::_('GetBible.Watcher')->getNextBook($this->translation, $this->book);
					$this->chapter = 1;
					$this->verses = null;

					// so we try to load this one last time
					if (empty($book) || !GetBibleFactory::_('GetBible.Watcher')->sync($this->translation, $book, $this->chapter))
					{
						return false;
					}

					// we found another book
					$this->book = $book;

					// since we could not find the book we where looking for, we redirect to what we found
					$app = Factory::getApplication();

					// get the book name
					$name = $this->getBookName($this->book, $this->translation) ?? $book;

					// we state this obvious result to the user
					$app->enqueueMessage(Text::sprintf("COM_GETBIBLE_WERE_SORRY_THE_TRANSLATION_YOU_SELECTED_DOES_NOT_INCLUDE_THE_BOOK_YOU_WERE_IN_PREVIOUSLY_HOWEVER_WE_HAVE_LOCATED_BSB_WHICH_MIGHT_BE_OF_INTEREST_TO_YOU", $name), 'warning');

					$app->redirect(JRoute::_('index.php?option=com_getbible&view=app&t=' . $this->translation . '&ref=' . $name));

					return false;
				}

				// [or] we load the next chapter
				if (($chapter_next = GetBibleFactory::_('GetBible.Watcher')->getNextChapter($this->translation, $this->book, $this->chapter)) !== null)
				{
					GetBibleFactory::_('GetBible.Watcher')->sync($this->translation, $this->book, $chapter_next);
				}

				// [or] we load the previous chapter
				if (($chapter_previous = GetBibleFactory::_('GetBible.Watcher')->getPreviousChapter($this->chapter)) !== null)
				{
					GetBibleFactory::_('GetBible.Watcher')->sync($this->translation, $this->book, $chapter_previous);
				}

				$data = (object) [
					'translation' => $this->translation,
					'book' => $this->book,
					'chapter' => $this->chapter,
					'verses' => $this->verses,
					'daily' => GetBibleFactory::_('DailyScripture')->isDaily()
				];

				if (empty($data))
				{
					$app = Factory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage(Text::_('COM_GETBIBLE_NOT_FOUND_OR_ACCESS_DENIED'), 'warning');
					$app->redirect(Uri::root());
					return false;
				}

				// set data object to item.
				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getChapter()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_chapter as a
		$query->select($db->quoteName(
			array('a.abbreviation','a.name','a.chapter','a.book_nr','a.sha'),
			array('abbreviation','name','chapter','book_nr','sha')));
		$query->from($db->quoteName('#__getbible_chapter', 'a'));

		// Get from #__getbible_translation as b
		$query->select($db->quoteName(
			array('b.translation','b.lang','b.language','b.direction','b.encoding'),
			array('translation','lang','language','direction','encoding')));
		$query->join('LEFT', ($db->quoteName('#__getbible_translation', 'b')) . ' ON (' . $db->quoteName('a.abbreviation') . ' = ' . $db->quoteName('b.abbreviation') . ')');

		// Get from #__getbible_book as c
		$query->select($db->quoteName(
			array('c.name'),
			array('book_name')));
		$query->join('LEFT', ($db->quoteName('#__getbible_book', 'c')) . ' ON (' . $db->quoteName('a.book_nr') . ' = ' . $db->quoteName('c.nr') . ')');
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->chapter is a string or numeric value.
		$checkValue = $this->chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('c.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('c.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a stdClass object.
		$data = $db->loadObject();

		if (empty($data))
		{
			return false;
		}
		// set chapterChapterVerseV to the $data object.
		$data->chapterChapterVerseV = $this->getChapterChapterVerseAadf_V($data->chapter);
		$data->verses = $data->chapterChapterVerseV;
		// only trigger this if either search or openai is activated
		if (Helper::getParams('com_getbible')->get('activate_search') == 1 || Helper::getParams('com_getbible')->get('enable_open_ai') == 1)
		{
			$data->html_verses = $this->modelVerses($data->chapterChapterVerseV);
		}
		else
		{
			$data->html_verses = $data->chapterChapterVerseV;
		}
		unset($data->chapterChapterVerseV);

		// return data object.
		return $data;
	}

	/**
	 * Method to get an array of Verse Objects.
	 *
	 * @return mixed  An array of Verse Objects on success, false on failure.
	 *
	 */
	public function getChapterChapterVerseAadf_V($chapter)
	{
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_verse as v
		$query->select($db->quoteName(
			array('v.name','v.book_nr','v.chapter','v.verse','v.text'),
			array('name','book_nr','chapter','verse','text')));
		$query->from($db->quoteName('#__getbible_verse', 'v'));
		$query->where('v.chapter = ' . $db->quote($chapter));
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('v.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('v.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('v.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('v.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$db->execute();

		// check if there was data returned
		if ($db->getNumRows())
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			$items = $db->loadObjectList();

			// Convert the parameter fields into objects.
			foreach ($items as $nr => &$item)
			{
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on text
				$_text = new stdClass();
				$_text->text =& $item->text; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (text) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.text', &$_text, &$params, 0));
			}
			return $items;
		}
		return false;
	}


	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getTranslations()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.id','a.language','a.lang','a.translation','a.abbreviation','a.distribution_lcsh'),
			array('id','language','lang','translation','abbreviation','lcsh')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		// Get where a.published is 1
		$query->where('a.published = 1');
		$query->order('a.abbreviation ASC');
		$query->order('a.lang ASC');
		$query->order('a.translation ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getBooks()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_book as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.nr','a.abbreviation'),
			array('id','name','nr','abbreviation')));
		$query->from($db->quoteName('#__getbible_book', 'a'));
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		$query->order('a.nr ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getChapters()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_chapter as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.chapter','a.abbreviation'),
			array('id','name','chapter','abbreviation')));
		$query->from($db->quoteName('#__getbible_chapter', 'a'));

		// Get from #__getbible_book as b
		$query->select($db->quoteName(
			array('b.name'),
			array('book_name')));
		$query->join('LEFT', ($db->quoteName('#__getbible_book', 'b')) . ' ON (' . $db->quoteName('a.book_nr') . ' = ' . $db->quoteName('b.nr') . ')');
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('b.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('b.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		$query->order('a.book_nr ASC');
		$query->order('a.chapter ASC');
		$query->group('a.chapter');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getNext()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// the target book
		$book = $this->book;

		// get the next chapter
		if (($chapter = GetBibleFactory::_('GetBible.Watcher')->getNextChapter($this->translation, $this->book, $this->chapter, true)) === null)
		{
			$book = GetBibleFactory::_('GetBible.Watcher')->getNextBook($this->translation, $this->book);
			$chapter = 1;

			// make sure its loaded
			if (empty($book) || !GetBibleFactory::_('GetBible.Watcher')->sync($this->translation, $book, $chapter))
			{
				return false;
			}
		}
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_chapter as a
		$query->select($db->quoteName(
			array('a.id','a.chapter','a.abbreviation'),
			array('id','chapter','abbreviation')));
		$query->from($db->quoteName('#__getbible_chapter', 'a'));

		// Get from #__getbible_book as b
		$query->select($db->quoteName(
			array('b.name'),
			array('name')));
		$query->join('LEFT', ($db->quoteName('#__getbible_book', 'b')) . ' ON (' . $db->quoteName('a.book_nr') . ' = ' . $db->quoteName('b.nr') . ')');
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $book is a string or numeric value.
		$checkValue = $book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $chapter is a string or numeric value.
		$checkValue = $chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('b.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('b.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a stdClass object.
		$data = $db->loadObject();

		if (empty($data))
		{
			return false;
		}

		// return data object.
		return $data;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getPrevious()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// the target book
		$book = $this->book;

		// get the next chapter
		if (($chapter = GetBibleFactory::_('GetBible.Watcher')->getPreviousChapter($this->chapter, true)) === null)
		{
			$book =  GetBibleFactory::_('GetBible.Watcher')->getPreviousBook($this->translation, $this->book);

			// make sure its loaded
			if (empty($book) || !GetBibleFactory::_('GetBible.Watcher')->sync($this->translation, $book, 1))
			{
				return false;
			}

			$chapter = GetBibleFactory::_('GetBible.Watcher')->getLastChapter($this->translation, $book);
		}
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_chapter as a
		$query->select($db->quoteName(
			array('a.id','a.chapter','a.abbreviation'),
			array('id','chapter','abbreviation')));
		$query->from($db->quoteName('#__getbible_chapter', 'a'));

		// Get from #__getbible_book as b
		$query->select($db->quoteName(
			array('b.name'),
			array('name')));
		$query->join('LEFT', ($db->quoteName('#__getbible_book', 'b')) . ' ON (' . $db->quoteName('a.book_nr') . ' = ' . $db->quoteName('b.nr') . ')');
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $book is a string or numeric value.
		$checkValue = $book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $chapter is a string or numeric value.
		$checkValue = $chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('b.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('b.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a stdClass object.
		$data = $db->loadObject();

		if (empty($data))
		{
			return false;
		}

		// return data object.
		return $data;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getTranslation()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.distribution_abbreviation','a.distribution_versification','a.distribution_version','a.distribution_version_date','a.distribution_lcsh','a.encoding','a.sha','a.language','a.lang','a.distribution_sourcetype','a.distribution_source','a.distribution_license','a.distribution_about','a.distribution_history','a.translation','a.abbreviation','a.direction'),
			array('distribution_abbreviation','distribution_versification','distribution_version','distribution_version_date','distribution_lcsh','encoding','sha','language','lang','distribution_sourcetype','distribution_source','distribution_license','distribution_about','distribution_history','translation','abbreviation','direction')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a stdClass object.
		$data = $db->loadObject();

		if (empty($data))
		{
			return false;
		}
	// Load the JEvent Dispatcher
	PluginHelper::importPlugin('content');
	$this->_dispatcher = Factory::getApplication();
		// Check if we can decode distribution_history
		if (isset($data->distribution_history) && JsonHelper::check($data->distribution_history))
		{
			// Decode distribution_history
			$data->distribution_history = json_decode($data->distribution_history, true);
		}
		// Check if item has params, or pass whole item.
		$params = (isset($data->params) && JsonHelper::check($data->params)) ? json_decode($data->params) : $data;
		// Make sure the content prepare plugins fire on distribution_about
		$_distribution_about = new stdClass();
		$_distribution_about->text =& $data->distribution_about; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_about) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new stdClass();
		$_distribution_license->text =& $data->distribution_license; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_license) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.distribution_license', &$_distribution_license, &$params, 0));

		// return data object.
		return $data;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getNotes()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_note as a
		$query->select($db->quoteName(
			array('a.id','a.verse','a.book_nr','a.chapter','a.note'),
			array('id','verse','book_nr','chapter','note')));
		$query->from($db->quoteName('#__getbible_note', 'a'));
		// Check if $this->chapter is a string or numeric value.
		$checkValue = $this->chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 1
		$query->where('a.access = 1');
		// Get where a.published is 1
		$query->where('a.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on note
				$_note = new stdClass();
				$_note->text =& $item->note; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (note) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.note', &$_note, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getLinkerNotes()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_note as a
		$query->select($db->quoteName(
			array('a.id','a.verse','a.book_nr','a.chapter','a.note','a.linker','a.guid'),
			array('id','verse','book_nr','chapter','note','linker','guid')));
		$query->from($db->quoteName('#__getbible_note', 'a'));
		// Check if GetBibleFactory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = GetBibleFactory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.linker = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.linker = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->chapter is a string or numeric value.
		$checkValue = $this->chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 0
		$query->where('a.access = 0');
		// Get where a.published is 1
		$query->where('a.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on note
				$_note = new stdClass();
				$_note->text =& $item->note; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (note) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.note', &$_note, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getTags()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.description','a.guid'),
			array('id','name','description','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Get where a.access is 1
		$query->where('a.access = 1');
		// Get where a.published is 1
		$query->where('a.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.description', &$_description, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getTaggedVerses()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tagged_verse as a
		$query->select($db->quoteName(
			array('a.id','a.verse','a.book_nr','a.chapter','a.guid'),
			array('id','verse','book_nr','chapter','guid')));
		$query->from($db->quoteName('#__getbible_tagged_verse', 'a'));

		// Get from #__getbible_tag as t
		$query->select($db->quoteName(
			array('t.name','t.description','t.guid'),
			array('name','description','tag')));
		$query->join('LEFT', ($db->quoteName('#__getbible_tag', 't')) . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('t.guid') . ')');
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->chapter is a string or numeric value.
		$checkValue = $this->chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 1
		$query->where('a.access = 1');
		// Get where a.published is 1
		$query->where('a.published = 1');
		// Get where t.published is 1
		$query->where('t.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.description', &$_description, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getPrompts()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_prompt as a
		$query->select($db->quoteName(
			array('a.id','a.integration','a.name','a.guid'),
			array('id','integration','name','guid')));
		$query->from($db->quoteName('#__getbible_prompt', 'a'));
		// Check if ($globalParams->get('enable_open_ai') == 1) ? ($this->translation ? [$db->quote('all'), $db->quote($this->translation)] : [$db->quote('all')]) : null is an array with values.
		$array = ($globalParams->get('enable_open_ai') == 1) ? ($this->translation ? [$db->quote('all'), $db->quote($this->translation)] : [$db->quote('all')]) : null;
		if (isset($array) && UtilitiesArrayHelper::check($array))
		{
			$query->where('a.abbreviation IN (' . implode(',', $array) . ')');
		}
		else
		{
			return false;
		}
		// Get where a.published is 1
		$query->where('a.published = 1');
		$query->order('a.ordering ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getLinkerTaggedVerses()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tagged_verse as a
		$query->select($db->quoteName(
			array('a.id','a.linker','a.verse','a.book_nr','a.chapter','a.access','a.published','a.guid'),
			array('id','linker','verse','book_nr','chapter','access','published','guid')));
		$query->from($db->quoteName('#__getbible_tagged_verse', 'a'));

		// Get from #__getbible_tag as b
		$query->select($db->quoteName(
			array('b.name','b.description'),
			array('name','description')));
		$query->join('LEFT', ($db->quoteName('#__getbible_tag', 'b')) . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('b.guid') . ')');

		// Get from #__getbible_tag as t
		$query->select($db->quoteName(
			array('t.guid'),
			array('tag')));
		$query->join('LEFT', ($db->quoteName('#__getbible_tag', 't')) . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('t.guid') . ')');
		// Check if GetBibleFactory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = GetBibleFactory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.linker = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.linker = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->book is a string or numeric value.
		$checkValue = $this->book;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.book_nr = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.book_nr = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->chapter is a string or numeric value.
		$checkValue = $this->chapter;
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.chapter = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.chapter = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 0
		$query->where('a.access = 0');
		// Get where b.published is 1
		$query->where('b.published = 1');
		// Get where t.published is 1
		$query->where('t.published = 1');
		$query->order('a.book_nr ASC');
		$query->order('a.chapter ASC');
		$query->order('a.verse ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.description', &$_description, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getLinkerTags()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = Factory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.linker','a.name','a.description','a.published','a.guid'),
			array('id','linker','name','description','published','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Check if GetBibleFactory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = GetBibleFactory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && StringHelper::check($checkValue))
		{
			$query->where('a.linker = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.linker = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 0
		$query->where('a.access = 0');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.app.description', &$_description, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Get the book name
	 *
	 * @param   int  $book  The book number
	 *
	 * @return  string|null  The book name on success
	 */
	protected function getBookName(int $book, string $translation): ?string
	{
		// Get a db connection.
		$db = Factory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_book as a
		$query->select($db->quoteName(
			array('a.name'),
			array('name')));
		$query->from($db->quoteName('#__getbible_book', 'a'));

		$query->where('a.abbreviation = ' . $db->quote($translation));
		$query->where('a.nr = ' . $book);

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Method to add html span tag to each word in each verse
	 *
	 * @param   array  $verses  The array of verses
	 *
	 * @return  array  The modelled array of verses.
	 */
	protected function modelVerses(array $verses): array
	{
		$result = array_map(function($source) {
			// Clone the object first, then modify the clone.
			$obj = clone $source;
			$words = $this->splitSentence($obj->text);
			$counter = 1;
			$words = array_map(function($word) use(&$obj, &$counter) {
				return $this->addSpan($word, $counter, $obj->verse);
			}, $words);

			$obj->text = implode('', $words);

			return $obj;

		}, $verses);

		return $result;
	}

	/**
	 * Add the span tag
	 *
	 * @param   string  $word  A word being marked
	 * @param   int      $nr        The word number
	 * @param   string  $verse  A verse number
	 *
	 * @return  string  The marked word
	 */
	protected function addSpan(string $word, int &$nr, string &$verse): string
	{
		$plainWord = preg_replace('/[^\p{L}\p{N}\s]/u', '', $word);
		$encodedWord = urlencode($plainWord);

		if ($this->hasLength($plainWord))
		{ 
			$word = '<span class="getbible-word" data-word-nr="' . $nr . '" data-verse="' . $verse . '" data-url-word="' . $encodedWord . '" data-word="' . $plainWord . '"> ' . $word . ' </span>';
			$nr++;
		}

		return $word;
	}

	/**
	 * Return an array of words
	 *
	 * @param   string  $text  The actual sentence
	 *
	 * @return  array  An array of words
	 */
	protected function splitSentence(string $text): array
	{
		return GetBibleFactory::_('GetBible.Utilities.String')->split($text);
	}

	/**
	 * Make sure a string has a length
	 *
	 * @param   string  $word  The actual string to check
	 *
	 * @return  bool  True if its a string with characters.
	 */
	protected function hasLength(string $word): bool
	{
		return GetBibleFactory::_('GetBible.Utilities.String')->hasLength($word);
	}
}
