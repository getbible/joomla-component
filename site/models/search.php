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
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\GetBible\Factory;

/**
 * Getbible List Model for Search
 */
class GetbibleModelSearch extends ListModel
{
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
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->initSet = true; 
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get data
		// Load request parameters
		$this->loadRequestParameters();

		// only search if the search feature is activated
		if (Helper::getParams('com_getbible')->get('activate_search') != 1)
		{
			$this->translation = 'no_search_allowed';
		}

		// block any search from continuing
		if (empty($this->search) || $this->translation === 'no_search_allowed')
		{
			// Set Query.
			$query->select($db->quoteName([
					'a.abbreviation'
			]))->from('#__getbible_verse AS a')->where($db->quoteName('a.abbreviation') . ' = ' . $db->quote('no_search_allowed'));

			return $query;
		}

		// Set Query.
		$query->select([
			'a.abbreviation',
			'a.book_nr',
			'b.name',
			'a.chapter',
			'a.verse',
			'a.text'
		])->from('#__getbible_verse AS a')
			->where($db->quoteName('a.abbreviation') . ' = ' . $db->quote($this->translation))
			->join('LEFT', ($db->quoteName('#__getbible_book', 'b')) . ' ON (' . $db->quoteName('a.book_nr') . ' = ' . $db->quoteName('b.nr') . ')')
			->where($db->quoteName('b.abbreviation') . ' = '. $db->quote($this->translation))
			->join('LEFT', ($db->quoteName('#__getbible_translation', 't')) . ' ON (' . $db->quoteName('a.abbreviation') . ' = ' . $db->quoteName('t.abbreviation') . ')')
			->where($db->quoteName('t.published') . ' = 1')
			->order($db->quoteName('a.book_nr') . ' ASC')
			->setLimit(1000);

		// Search conditions
		$searchConditions = $this->getSearchConditions($db);
		if (!empty($searchConditions))
		{
			$query->where($searchConditions);
		}

		// Target conditions
		$targetConditions = $this->getTargetConditions($db);
		if (!empty($targetConditions))
		{
			$query->where($targetConditions);
		}

		// echo nl2br(str_replace('#__', 'api_', $query)); die;
		// load helper UtilitiesArrayHelper

		return $query;

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{

		// load parent items
		$items = parent::getItems();

		if (UtilitiesArrayHelper::check($items))
		{
			$items = $this->modelVerses($items);
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
	public function getTranslations()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.id','a.language','a.lang','a.translation','a.abbreviation'),
			array('id','language','lang','translation','abbreviation')));
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
		if (GetbibleHelper::checkArray($items))
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
	public function getBooks()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_book as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.nr','a.abbreviation'),
			array('id','name','nr','abbreviation')));
		$query->from($db->quoteName('#__getbible_book', 'a'));
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
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
		if (GetbibleHelper::checkArray($items))
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
	public function getTranslation()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.distribution_abbreviation','a.distribution_versification','a.distribution_version','a.distribution_version_date','a.distribution_lcsh','a.encoding','a.sha','a.language','a.lang','a.distribution_sourcetype','a.distribution_source','a.distribution_license','a.distribution_about','a.distribution_history','a.translation','a.abbreviation','a.direction'),
			array('distribution_abbreviation','distribution_versification','distribution_version','distribution_version_date','distribution_lcsh','encoding','sha','language','lang','distribution_sourcetype','distribution_source','distribution_license','distribution_about','distribution_history','translation','abbreviation','direction')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
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
	JPluginHelper::importPlugin('content');
	$this->_dispatcher = JFactory::getApplication();
		// Check if we can decode distribution_history
		if (GetbibleHelper::checkJson($data->distribution_history))
		{
			// Decode distribution_history
			$data->distribution_history = json_decode($data->distribution_history, true);
		}
		// Check if item has params, or pass whole item.
		$params = (isset($data->params) && GetbibleHelper::checkJson($data->params)) ? json_decode($data->params) : $data;
		// Make sure the content prepare plugins fire on distribution_about
		$_distribution_about = new stdClass();
		$_distribution_about->text =& $data->distribution_about; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_about) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.search.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new stdClass();
		$_distribution_license->text =& $data->distribution_license; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_license) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.search.distribution_license', &$_distribution_license, &$params, 0));

		// return data object.
		return $data;
	}

	/**
	 * Load all the Request Parameters.
	 *
	 * @return  void
	 */
	protected function loadRequestParameters()
	{
		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);

		$this->translation = $this->input->getString('t') ?? $this->input->getString('translation', $globalParams->get('default_translation', 'kjv'));
		$this->words = $this->input->getInt('words', $globalParams->get('search_words', 1));
		$this->match = $this->input->getInt('match', $globalParams->get('search_match', 1));
		$this->case = $this->input->getInt('case', $globalParams->get('search_case', 1));
		$this->target = $this->input->getInt('target', 1000);
		$this->book = $this->input->getString('book');
		$search = $this->input->getString('search') ?? $this->input->getString('s');
		$this->search = trim($search);
	}

	/**
	 * Prepare the search condition part of the SQL query.
	 *
	 * @param   object  $db     Database object
	 *
	 * @return  string  The SQL conditions for search
	 */
	protected function getSearchConditions($db): string
	{
		$conditions = [];
		$case = $this->case == 2 ? 'BINARY' : ' ';

		// 2 = ANY WORDS
		if($this->words == 2)
		{
			// 2 = partial match
			if($this->match == 2)
			{
				$words = $this->splitSentence($this->search);
				$condition = '( ' . $case . ' a.text LIKE ';
				$i = 0;
				foreach ($words as $word)
				{
					if ($i == 0)
					{
						$condition .= $db->quote('%' . $db->escape($word, true) . '%');
					}
					else
					{
						$condition .= ' OR ' . $case . ' a.text LIKE ' . $db->quote('%' . $db->escape($word, true) . '%');
					}
					$i++;
				}

				$conditions[] = $condition . ')';
			}
			// 1 = exact match
			elseif($this->match == 1)
			{
				$words = $this->splitSentence($this->search);
				$condition = '( ' . $case . ' a.text  REGEXP ';
				$i = 0;
				foreach ($words as $word)
				{
					if ($i == 0)
					{
						$condition .= $db->quote('[[:<:]]' . $db->escape($word, true). '[[:>:]]');
					}
					else
					{
						$condition .= ' OR ' . $case . ' a.text REGEXP '. $db->quote('[[:<:]]' . $db->escape($word, true) . '[[:>:]]');
					}
					$i++;
				}
				$conditions[] = $condition . ')';
			}
		}
		// 3 = EXACT PHRASE
		elseif ($this->words == 3)
		{
			if($this->match == 2)
			{
				// 2 = partial match
				$search = $db->quote('%' . $db->escape($this->search, true) . '%');
				$conditions[] = '( ' . $case . ' a.text LIKE ' . $search . ')';
			}
			elseif($this->match == 1)
			{
				// exact match
				$search = $case . ' a.text  REGEXP ' . $db->quote('[[:<:]]' . $db->escape($this->search, true) . '[[:>:]]');
				$conditions[] = '( '. $search . ')';		
			}
		}
		// 1 = ALL WORDS
		elseif ($this->words == 1)
		{
			// 2 = partial match
			if($this->match == 2)
			{
				$words = $this->splitSentence($this->search);
				foreach ($words as $word)
				{
					$search = $db->quote('%' . $db->escape($word, true) . '%');
					$conditions[] = '( '.$case.' a.text LIKE ' . $search . ')';
				}
			}
			// 1 = exact match
			elseif($this->match == 1)
			{
				$words = $this->splitSentence($this->search);
				foreach ($words as $word)
				{
					$search = $case . ' a.text REGEXP '. $db->quote('[[:<:]]' . $db->escape($word, true) . '[[:>:]]');
					$conditions[] = '( ' . $search . ')';
				}
			}
		}

		return implode(' AND ', $conditions);
	}

	/**
	 * Prepare the target condition part of the SQL query.
	 *
	 * @param   object  $db     Database object
	 *
	 * @return  string|null  The SQL conditions for target
	 */
	protected function getTargetConditions($db): ?string
	{
		switch($this->target)
		{
			case 1000: // Whole Bible
				// No additional condition
				return null;
			break;

			case 2000: // Old Testament
				$books = range(1, 39);
				return $db->quoteName('a.book_nr') . ' IN (' . implode(',', $books) . ')';
			break;

			case 3000: // New Testament
				$books = range(40, 66);
				return $db->quoteName('a.book_nr') . ' IN (' . implode(',', $books) . ')';
			break;

			default: // Specific book
				if (is_numeric($this->book))
				{
					return $db->quoteName('a.book_nr') . ' = ' . (int) $this->book;
				}
				elseif (is_string($this->book))
				{
					return $db->quoteName('b.name') . ' = ' . $db->quote($this->book);
				}
			break;
		}

		return null;
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
		// Split the search phrase into words
		$this->searchWords = $this->splitSentence($this->search);

		$result = array_map(function($source) {
			// Clone the object first, then modify the clone.
			$obj = clone $source;
			$words = $this->splitSentence($obj->text);
			$words = array_map(function($word) {
				return $this->addSpan($word);
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
	 *
	 * @return  string  The marked word
	 */
	protected function addSpan(string $word): string
	{
		if ($this->hasLength($word) && $this->foundWord($word))
		{
			$word = '<span class="getbible-word-found">' . $word . '</span>';
		}

		return $word;
	}

	/**
	 * Make confirm that its the searched word
	 *
	 * @param   string  $word  The actual string to check
	 *
	 * @return  bool  True if its a string with characters.
	 */
	protected function foundWord(string $word): bool
	{
		$word = preg_replace('/[^\p{L}\p{N}\s]/u', '', $word);

		// If case insensitive, convert both search words and input word to lower case
		if ($this->case == 1)
		{
			$word = mb_strtolower($word);
			$this->searchWords = array_map('mb_strtolower', $this->searchWords);
		}

		// If we are looking for an exact match
		if ($this->match == 1)
		{
			if ($this->words == 3)
			{
				// The search string is considered as one phrase
				return $word == $this->search;
			}
			else
			{
				// The search string is considered as separate words
				return in_array($word, $this->searchWords);
			}
		}
		else
		{
			if ($this->words == 3)
			{
				// The search string is considered as one phrase
				return mb_strpos($word, $this->search) !== false;
			}
			else
			{
				// The search string is considered as separate words
				foreach ($this->searchWords as $search_word)
				{
					if (mb_strpos($word, $search_word) !== false)
					{
						return true;
					}
				}
			}
		}

		return false;
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
		return Factory::_('GetBible.Utilities.String')->split($text);
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
		return Factory::_('GetBible.Utilities.String')->hasLength($word);
	}
}
