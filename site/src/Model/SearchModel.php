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
namespace TrueChristianChurch\Component\Getbible\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\RouteHelper;
use Joomla\CMS\Helper\TagsHelper;
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\JsonHelper;
use VDM\Joomla\GetBible\Factory as GetBibleFactory;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible List Model for Search
 *
 * @since  1.6
 */
class SearchModel extends ListModel
{
	/**
	 * Represents the current user object.
	 *
	 * @var   User  The user object representing the current user.
	 * @since 3.2.0
	 */
	protected User $user;

	/**
	 * The unique identifier of the current user.
	 *
	 * @var   int|null  The ID of the current user.
	 * @since 3.2.0
	 */
	protected ?int $userId;

	/**
	 * Flag indicating whether the current user is a guest.
	 *
	 * @var   int  1 if the user is a guest, 0 otherwise.
	 * @since 3.2.0
	 */
	protected int $guest;

	/**
	 * An array of groups that the current user belongs to.
	 *
	 * @var   array|null  An array of user group IDs.
	 * @since 3.2.0
	 */
	protected ?array $groups;

	/**
	 * An array of view access levels for the current user.
	 *
	 * @var   array|null  An array of access level IDs.
	 * @since 3.2.0
	 */
	protected ?array $levels;

	/**
	 * The application object.
	 *
	 * @var   CMSApplicationInterface  The application instance.
	 * @since 3.2.0
	 */
	protected CMSApplicationInterface $app;

	/**
	 * The input object, providing access to the request data.
	 *
	 * @var   Input  The input object.
	 * @since 3.2.0
	 */
	protected Input $input;

	/**
	 * The styles array.
	 *
	 * @var    array
	 * @since  4.3
	 */
	protected array $styles = [
		'components/com_getbible/assets/css/site.css',
		'components/com_getbible/assets/css/search.css'
 	];

	/**
	 * The scripts array.
	 *
	 * @var    array
	 * @since  4.3
	 */
	protected array $scripts = [
		'components/com_getbible/assets/js/site.js'
 	];

	/**
	 * A custom property for UIKit components. (not used unless you load v2)
	 */
	protected $uikitComp;

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
		parent::__construct($config, $factory);

		$this->app ??= Factory::getApplication();
		$this->input ??= $this->app->getInput();

		// Set the current user for authorisation checks (for those calling this model directly)
		$this->user ??= $this->getCurrentUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();

		// will be removed
		$this->initSet = true;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return   string  An SQL query
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = $this->getDatabase();

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

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 * @since   1.6
	 */
	public function getItems()
	{
		$user = $this->user;
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
			}
		}

		// return items
		return $items;
	}

	/**
	 * Method to get the styles that have to be included on the view
	 *
	 * @return  array    styles files
	 * @since   4.3
	 */
	public function getStyles(): array
	{
		return $this->styles;
	}

	/**
	 * Method to set the styles that have to be included on the view
	 *
	 * @return  void
	 * @since   4.3
	 */
	public function setStyles(string $path): void
	{
		$this->styles[] = $path;
	}

	/**
	 * Method to get the script that have to be included on the view
	 *
	 * @return  array    script files
	 * @since   4.3
	 */
	public function getScripts(): array
	{
		return $this->scripts;
	}

	/**
	 * Method to set the script that have to be included on the view
	 *
	 * @return  void
	 * @since   4.3
	 */
	public function setScript(string $path): void
	{
		$this->scripts[] = $path;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getTranslations()
	{

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = $this->getDatabase();

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
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
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

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = $this->getDatabase();

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
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
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
		// Get a db connection.
		$db = $this->getDatabase();

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
		$_distribution_about = new \stdClass();
		$_distribution_about->text =& $data->distribution_about; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_about) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.search.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new \stdClass();
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
		$globalParams = ComponentHelper::getParams('com_getbible', true);

		$this->translation = $this->input->getString('t') ?? $this->input->getString('translation', $globalParams->get('default_translation', 'kjv'));
		$this->words = $this->input->getInt('words', $globalParams->get('search_words', 1));
		$this->match = $this->input->getInt('match', $globalParams->get('search_match', 1));
		$this->case = $this->input->getInt('case', $globalParams->get('search_case', 1));
		$this->target = $this->input->getInt('target', 1000);
		$this->book = $this->input->getString('target_book');
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

		// Determine the database type and version
		$db_type = $this->getDatabaseType($db); // Implement this method based on your environment
		$use_modern_regex = $db_type === 'mysql' && $this->isModernMySQL($db); // Implement isModernMySQL to detect MySQL 8+

		// Adjust REGEXP syntax based on the database type and version
		$word_boundary_start = $use_modern_regex ? '\\b' : '[[:<:]]';
		$word_boundary_end = $use_modern_regex ? '\\b' : '[[:>:]]';

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
					if ($this->hasLength($word))
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
					if ($this->hasLength($word))
					{
						if ($i == 0)
						{
							$condition .= $db->quote($word_boundary_start . $db->escape($word, true). $word_boundary_end);
						}
						else
						{
							$condition .= ' OR ' . $case . ' a.text REGEXP '.
								$db->quote($word_boundary_start . $db->escape($word, true) . $word_boundary_end);
						}
						$i++;
					}
				}
				$conditions[] = $condition . ')';
			}
		}
		// 3 = EXACT PHRASE
		elseif ($this->words == 3)
		{
			// 2 = partial match
			if ($this->match == 2) {
				$words = $this->splitSentence($this->search);
				$search = [];
				foreach ($words as $word)
				{
					if ($this->hasLength($word))
					{
						$search[] = '%' . $db->escape($word, true) . '%';
					}
				}

				// Construct the LIKE clause with wildcards between each word for partial matches
				$conditions[] = '(' . $case . ' a.text LIKE ' . $db->quote(implode('%', $search)) . ')';
			}
			elseif ($this->match == 1)
			{
				// 1 = exact match
				// For exact phrase, escape and quote the entire phrase and use REGEXP to match it exactly
				$search = $case . ' a.text REGEXP ' .
					$db->quote($word_boundary_start . $db->escape($this->search, true) . $word_boundary_end);
				$conditions[] = '(' . $search . ')';
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
					if ($this->hasLength($word))
					{
						$search = $db->quote('%' . $db->escape($word, true) . '%');
						$conditions[] = '( '.$case.' a.text LIKE ' . $search . ')';
					}
				}
			}
			// 1 = exact match
			elseif($this->match == 1)
			{
				$words = $this->splitSentence($this->search);
				foreach ($words as $word)
				{
					if ($this->hasLength($word))
					{
						$search = $case . ' a.text REGEXP '.
							$db->quote($word_boundary_start . $db->escape($word, true) . $word_boundary_end);
						$conditions[] = '( ' . $search . ')';
					}
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

		// 1 = exact match
		if ($this->match == 3)
		{
			// The search string is considered as separate words
			foreach ($this->searchWords as $search_word)
			{
				if ($word === $search_word)
				{
					return true;
				}
			}
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

	/**
	 * Determines whether the database is MySQL or MariaDB.
	 *
	 * This function executes a query to fetch the version comment from the database.
	 * MariaDB includes its name in the version comment, allowing us to distinguish between MySQL and MariaDB.
	 *
	 * @param   \JDatabaseDriver  $db  The database driver object from Joomla.
	 *
	 * @return  string  Returns 'mysql' for MySQL, 'mariadb' for MariaDB, or 'unknown' for other or undetectable types.
	 */
	protected function getDatabaseType($db): string
	{
		try {
			// Attempt to get the version comment from the database
			$versionComment = $db->setQuery("SELECT VERSION()")->loadResult();
			if (strpos(strtolower($versionComment), 'mariadb') !== false) {
				return 'mariadb';
			} else {
				return 'mysql'; // Assuming MySQL if MariaDB is not detected
			}
		} catch (\Exception $e) {
			// Handle exceptions or fallback for other databases
			return 'unknown';
		}
	}

	/**
	 * Checks if the MySQL version is 8.0 or higher.
	 *
	 * This function queries the database version directly and compares it against 8.0
	 * to determine if modern MySQL features, such as enhanced regular expression syntax, can be used.
	 * It's specifically designed for MySQL and does not apply to MariaDB or other databases.
	 *
	 * @param   \JDatabaseDriver   $db  The database driver object.
	 *
	 * @return  bool  Returns true if the MySQL version is 8.0 or higher, false otherwise.
	 */
	protected function isModernMySQL($db): bool
	{
		// Query the database for its version
		$version = $db->getVersion();

		// Compare the version to determine if it's modern MySQL
		return version_compare($version, '8.0', '>=');
	}
}
