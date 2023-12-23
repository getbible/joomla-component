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
use Joomla\CMS\Component\ComponentHelper;

/**
 * Routing class from com_getbible
 *
 * @since  3.3
 */
class GetbibleRouter extends JComponentRouterBase
{	
	/**
	 * Build the route for the com_getbible component
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */

	protected ?string $defaultTranslation = null;
	public function build(&$query)
	{
		$segments = [];
		$view = $query['view'] ?? 'app';
		$this->defaultTranslation ??= JComponentHelper::getParams('com_getbible')->get('default_translation', 'kjv');

		if ($view === 'search')
		{
			$segments[0] = 'search';
			$segments[1] = $query['t'] ?? $query['version'] ?? $query['translation'] ?? $this->defaultTranslation;

			$criteria = $query['criteria'] ?? null;
			if ($criteria === null)
			{
				$word = $query['words'] ?? 1;
				$match = $query['match'] ?? 2;
				$case = $query['case'] ?? 1;
				$target = $query['target'] ?? 1000;

				$criteria = "{$word}-{$match}-{$case}-{$target}";
			}

			if (strpos($criteria, '-') !== false)
			{
				$word = $this->criteriaString(
					$criteria, 0,
					[
						'allwords' => 'allwords',
						'anywords' => 'anywords',
						'exactwords' => 'exactwords',
						1 => 'allwords',
						2 => 'anywords',
						3 => 'exactwords'
					], 'allwords');

				$match = $this->criteriaString(
					$criteria, 1,
					[
						'exactmatch' => 'exactmatch',
						'partialmatch' => 'partialmatch',
						1 => 'exactmatch',
						2 => 'partialmatch'
					], 'partialmatch');

				$case = $this->criteriaString(
					$criteria, 2,
					[
						'caseinsensitive' => 'caseinsensitive',
						'casesensitive' => 'casesensitive',
						1 => 'caseinsensitive',
						2 => 'casesensitive'
					], 'caseinsensitive');

				$target = $this->criteriaString(
					$criteria, 3,
					[
						'allbooks' => 'allbooks',
						'oldtestament' => 'oldtestament',
						'newtestament' => 'newtestament',
						1000 => 'allbooks',
						2000 => 'oldtestament',
						3000 => 'newtestament',
						4000 => 'booknames'
					], 'allbooks');

				if ($target === 'booknames')
				{
					$target = $this->criteriaBook($criteria, 3);
				}

				$segments[2] = "{$word}-{$match}-{$case}-{$target}";
			}
			else
			{
				$segments[2] = 'allwords-partialmatch-caseinsensitive-allbooks';
			}

			$segments[3] = $query['search'] ?? $query['s'] ?? '';
		}
		elseif ($view === 'openai')
		{
			$segments[0] = 'openai';
			$segments[1] = $query['guid'] ?? '';
			$segments[2] = $query['t'] ?? $query['version'] ?? $query['translation'] ?? $this->defaultTranslation;
			$segments[3] = $query['book'] ?? '';
			$segments[4] = $query['chapter'] ?? '';
			$segments[5] = $query['verse'] ?? '';
			$segments[6] = $query['words'] ?? '';

			// remove what is not there
			if (empty($segments[5]))
			{
				unset($segments[5]);
			}
			if (empty($segments[6]))
			{
				unset($segments[6]);
			}
		}
		elseif ($view === 'api')
		{
			$segments[0] = 'api';
			$segments[1] = $query['t'] ?? $query['version'] ?? $query['translation'] ?? $this->defaultTranslation;
			$segments[2] = $query['get'] ?? '';
		}
		elseif ($view === 'tag')
		{
			$segments[0] = 'tag';
			$segments[1] = $query['t'] ?? $query['version'] ?? $query['translation'] ?? $this->defaultTranslation;
			$segments[2] = $query['guid'] ?? '';
			if (!empty($query['guid']) && ($tag_name = $this->getVar('tag', $query['guid'], 'guid', 'name')) !== null)
			{
				$tag_name = preg_replace('/[^\p{L}\p{N}\s]/u', '', $tag_name);
				$segments[3] = urlencode($tag_name);
			}
		}
		else
		{
			$segments[0] = $query['t'] ?? $query['translation'] ?? $this->defaultTranslation;
			$book = $query['ref'] ?? $query['b'] ?? $query['book'] ?? '';
			if (is_numeric($book) && $book > 0)
			{
				$book = $this->getBookName((int) $book, $segments[0]);
			}
			$segments[1] = $book;

			$chapter = $query['chapter'] ?? $query['c'] ?? '';
			if (strlen($chapter) && is_numeric($chapter))
			{
				$segments[2] = $chapter;
			}

			$verse = $query['verse'] ?? $query['v'] ?? '';
			if (strlen($verse))
			{
				$segments[3] = $verse;
			}
		}

		// remove all values used
		unset($query['view']);
		unset($query['ref']);
		unset($query['t']);
		unset($query['version']);
		unset($query['translation']);
		unset($query['b']);
		unset($query['book']);
		unset($query['target_book']);
		unset($query['c']);
		unset($query['chapter']);
		unset($query['verse']);
		unset($query['v']);
		unset($query['criteria']);
		unset($query['words']);
		unset($query['match']);
		unset($query['case']);
		unset($query['target']);
		unset($query['search']);
		unset($query['guid']);

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		$vars = [];
		$vars['view'] = 'app';

		$this->defaultTranslation ??= JComponentHelper::getParams('com_getbible')->get('default_translation', 'kjv');

		$key = 0;
		$vars['t'] = $segments[$key] ?? '';

		// if first value is a valid translation, we are on the app page
		if ($this->validTranslation($vars['t']))
		{
			$key++;
			$this->setAppVars($vars, $key, $segments);
		}
		// if the first value is search we are on the search page
		elseif ($vars['t'] === 'search')
		{
			$key++;
			$this->setSearchVars($vars, $key, $segments);
		}
		// if the first api is search we are on the api page
		elseif ($vars['t'] === 'api')
		{
			$vars['view'] = 'api';

			// set the translation
			$key++;
			$this->setTranslation($vars, $key, $segments);

			$vars['ref'] = $segments[$key] ?? '';
		}
		// if the first openai is search we are on the openai page
		elseif ($vars['t'] === 'openai')
		{
			$vars['view'] = 'openai';

			// set the AI guid
			$key++;
			$vars['guid'] = $segments[$key] ?? '';

			// set the translation
			$key++;
			$this->setTranslation($vars, $key, $segments);

			// set the targets
			$vars['book'] = $segments[$key] ?? '';
			$key++;
			$vars['chapter'] = $segments[$key] ?? '';
			$key++;
			$vars['verse'] = $segments[$key] ?? '';
			$key++;
			$vars['words'] = $segments[$key] ?? '';
		}
		// if the first tag is search we are on the tag page
		elseif ($vars['t'] === 'tag')
		{
			$vars['view'] = 'tag';

			// set the translation
			$key++;
			$this->setTranslation($vars, $key, $segments);

			// set the Tag guid
			$vars['guid'] = $segments[$key] ?? '';
		}
		// if the first tag is none of the above, we are probably on the app page
		else
		{
			$vars['t'] = $this->defaultTranslation;
			$this->setAppVars($vars, $key, $segments);
		}

		return $vars;
	}

	/**
	 * Set the app variables
	 *
	 * @param   array  $vars     The active variables found
	 * @param   int    $key      The active key state of the segment array pointer
	 * @param   array  $segments The URL segments
	 *
	 * @return  void
	 * @since   3.3
	 */
	protected function setAppVars(array &$vars, int &$key, array $segments): void
	{
		$value = $segments[$key] ?? null;

		$book_number = 0;
		$book_name = $this->getBook($value, $book_number, $vars['t']);

		if ($book_name !== null && $book_number > 0)
		{
			$vars['ref'] = $book_name;
			$vars['book'] = $book_number;
			$key++;

			$chapter_number = $this->getChapter($vars, $key, $segments);
			$has_verses = $this->getVerses($vars, $key, $segments);
		}

		$this->checkAndRedirectApiCall(
			$vars,
			$key,
			$segments,
			$book_number ?? null,
			$chapter_number ?? null,
			$has_verses ?? false
		);
	}

	/**
	 * Set the Search Variables
	 *
	 * @param   array  $vars     The active variables found
	 * @param   int    $key      The active key state of the segment array pointer
	 * @param   array  $segments The URL segments
	 *
	 * @return  void
	 * @since   3.3
	 */
	protected function setSearchVars(array &$vars, int &$key, array $segments): void
	{
		$vars['view'] = 'search';

		// set the translation
		$this->setTranslation($vars, $key, $segments);

		// set the criteria for the search
		$this->setSearchCriteria($vars, $key, $segments);

		// get the search value
		$vars['search'] = $segments[$key] ?? '';

		// check if this is an API call
		if (!empty($vars['search']))
		{
			$key++;

			$api_call = $segments[$key] ?? 'not_api_call';
			if ($api_call === 'get_bible.json')
			{
				echo '<pre>';
				var_dump('We have an API call!');
				var_dump($vars);
				exit;
			}
		}
	}

	/**
	 * Retrieve book based on the value provided
	 *
	 * @param   mixed         $value       
	 * @param   int          &$bookNumber
	 * @param   string|null   $translation    The book translation.
	 *
	 * @return  string|null
	 * @since   3.3
	 */
	private function getBook($value, int &$bookNumber, ?string $translation = null): ?string
	{
		if (is_numeric($value))
		{
			$bookNumber = $value;

			return $this->getBookName((int) $value, $translation);
		}
		elseif (!empty($value) && ($bookNumber = $this->getBookNumber($value)) !== null)
		{
			return $value;
		}

		return null;
	}

	/**
	 * Retrieve chapter from the segments
	 *
	 * @param   array  &$vars     
	 * @param   int    &$key      
	 * @param   array  $segments 
	 *
	 * @return  int|null
	 * @since   3.3
	 */
	private function getChapter(array &$vars, int &$key, array $segments): ?int
	{
		$value = $segments[$key] ?? null;
		if (!empty($value) && is_numeric($value) && $value > 0)
		{
			$vars['ref'] .= ' ' . $value;
			$vars['chapter'] = $value;
			$key++;

			return (int) $value;
		}

		return null;
	}

	/**
	 * Retrieve verses from the segments
	 *
	 * @param   array  &$vars     
	 * @param   int    &$key      
	 * @param   array  $segments 
	 *
	 * @return  bool
	 * @since   3.3
	 */
	private function getVerses(array &$vars, int &$key, array $segments): bool
	{
		$value = $segments[$key] ?? null;
		if (!empty($value) && (is_numeric($value) || strpos($value, '-') !== false))
		{
			$vars['ref'] .= ':' . $value;
			$vars['verse'] = $value;
			$key++;

			return true;
		}

		return false;
	}

	/**
	 * Check if the request is an API call and redirect if necessary
	 *
	 * @param   array     $vars          
	 * @param   int       &$key          
	 * @param   array     $segments      
	 * @param   int|null  $bookNumber    
	 * @param   int|null  $chapterNumber 
	 * @param   bool      $hasVerses     
	 *
	 * @return  void
	 * @since   3.3
	 */
	private function checkAndRedirectApiCall(
		array $vars, int $key, array $segments,
		?int $bookNumber, ?int $chapterNumber, bool $hasVerses): void
	{
		$apiCall = $segments[$key] ?? 'not_api_call';
		if ($apiCall === 'get_bible.json')
		{
			if ($hasVerses)
			{
				echo '<pre>';
				var_dump('We have an API call, will return it from local DB. Soon!');
				var_dump($vars);
				exit;
			}
			elseif (!empty($bookNumber) && !empty($chapterNumber))
			{
				header("Location: https://api.getbible.net/v2/{$vars['t']}/$bookNumber/$chapterNumber.json");
				exit;
			}
			elseif (!empty($bookNumber))
			{
				header("Location: https://api.getbible.net/v2/{$vars['t']}/$bookNumber.json");
				exit;
			}
			else
			{
				header("Location: https://api.getbible.net/v2/{$vars['t']}.json");
				exit;
			}
		}
	}

	/**
	 * Set The Translation
	 *
	 * @param   array  $vars     The active variables found
	 * @param   int    $key      The active key state of the segment array pointer
	 * @param   array  $segments The URL segments
	 *
	 * @return  void
	 * @since   3.3
	 */
	private function setTranslation(array &$vars, int &$key, array $segments): void
	{
		$vars['t'] = $segments[$key] ?? $this->defaultTranslation;

		if ($this->validTranslation($vars['t']))
		{
			$key++;
		}
		else
		{
			$vars['t'] = $this->defaultTranslation;
		}
	}

	/**
	 * Set The Search Criteria
	 *
	 * @param   array  $vars     The active variables found
	 * @param   int    $key      The active key state of the segment array pointer
	 * @param   array  $segments The URL segments
	 *
	 * @return  void
	 * @since   3.3
	 */
	private function setSearchCriteria(array &$vars, int &$key, array $segments): void
	{
		// set the criteria values
		$criteria = $segments[$key] ?? null;
		if ($criteria === null || strpos($criteria , '-') === false)
		{
			// the default is greedy
			$criteria = 'allwords-partialmatch-caseinsensitive-allbooks';
		}
		else
		{
			$key++;
		}

		/**
		 * > words (0)
		 * 1 = allwords, 2 = anywords, 3 = exactwords
		 */
		$vars['words'] = $this->criteria(
			$criteria, 0,
			[
				'allwords' => 1,
				'anywords' => 2,
				'exactwords' => 3,
				1 => 1, 2 => 2, 3 => 3
			], 1);

		/**
		 * > match (1)
		 * 1 = exactmatch, 2 = partialmatch
		 */
		$vars['match'] = $this->criteria(
			$criteria, 1,
			[
				'exactmatch' => 1,
				'partialmatch' => 2,
				1 => 1, 2 => 2
			], 2);

		/**
		 * > case (2)
		 * 1 = caseinsensitive, 2 = casesensitive
		 */
		$vars['case'] = $this->criteria(
			$criteria, 2,
			[
				'caseinsensitive' => 1,
				'casesensitive' => 2,
				1 => 1, 2 => 2
			], 1);

		/**
		 * > target (3)
		 * 1000 = all, 2000 = old, 3000 = new, 4000 = book_name
		 */
		$vars['target'] = $this->criteria(
			$criteria, 3,
			[
				'allbooks' => 1000,
				'oldtestament' => 2000,
				'newtestament' => 3000,
				1000 => 1000, 2000 => 2000, 3000 => 3000
			], 4000);

		/**
		 * When we have 4000 we need to get the book name
		 */
		if ($vars['target'] == 4000)
		{
			$vars['target_book'] = $this->criteriaBook($criteria, 3);

			if ($vars['target_book'] === null)
			{
				$vars['target'] = 1000;
			}
		}
	}

	/**
	 * Get the int value of the criteria string
	 *
	 * @param   string  $value     The criteria string.
	 * @param   int     $position  The criteria position.
	 * @param   array   $criteria  The criteria target.
	 * @param   int     $default   The criteria default.
	 *
	 * @return  int  The int value of the targeted criteria.
	 * @since   3.3
	 */
	private function criteria(string $value, int $position, array $criteria, int $default): int
	{
		if (strpos($value, '-') !== false)
		{
			$array = explode('-', $value);
			if (isset($array[$position]) && isset($criteria[$array[$position]]))
			{
				return $criteria[$array[$position]];
			}
		}

		return $default;
	}

	/**
	 * Get the book value from the criteria string
	 *
	 * @param   string  $value     The criteria string.
	 * @param   int     $position  The criteria position.
	 *
	 * @return  mixed  the book value
	 * @since   3.3
	 */
	private function criteriaBook(string $value, int $position)
	{
		if (strpos($value, '-') !== false)
		{
			$array = explode('-', $value);
			if (isset($array[$position]))
			{
				return $array[$position];
			}
		}

		return null;
	}

	/**
	 * Get the string value of the criteria int
	 *
	 * @param   string  $value     The criteria string.
	 * @param   int     $position  The criteria position.
	 * @param   array   $criteria  The criteria target.
	 * @param   int     $default   The criteria default.
	 *
	 * @return  string  The string value of the targeted criteria.
	 * @since   3.3
	 */
	private function criteriaString(string $value, int $position, array $criteria, string $default): string
	{
		if (strpos($value, '-') !== false)
		{
			$array = explode('-', $value);
			if (isset($array[$position]) && isset($criteria[$array[$position]]))
			{
				return $criteria[$array[$position]];
			}
		}

		return $default;
	}

	/**
	 * Get a Book number
	 *
	 * @param   string  $value   The book name.
	 *
	 * @return  int|null  The book number
	 * @since   3.3
	 */
	private function getBookNumber(string $name): ?int
	{
		if (($number = $this->getVar('book', $name, 'name', 'nr')) !== null
			&& $number > 0)
		{
			return $number;
		}

		return null;
	}

	/**
	 * Get a Book name
	 *
	 * @param   int           $value          The book number.
	 * @param   string|null   $translation    The book translation.
	 *
	 * @return  string|null  The book name
	 * @since   3.3
	 */
	private function getBookName(int $value, ?string $translation = null): ?string
	{
		if (!empty($translation) && is_numeric($value) && $value > 0)
		{
			// Get a db connection.
			$db = Factory::getDbo();

			// Create a new query object.
			$query = $db->getQuery(true);
			$query->select($db->quoteName('name'));		
			$query->from($db->quoteName('#__getbible_book'));
			$query->where($db->quoteName('nr') . ' = '. (int) $value);
			$query->where($db->quoteName('abbreviation') . ' = ' . $db->quote((string) $translation));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				return $db->loadResult();
			}
		}

		if (($name = $this->getVar('book', $value, 'nr', 'name')) !== null)
		{
			return $name;
		}

		return null;
	}

	/**
	 * Validate if this is a active translation
	 *
	 * @param   string  $value     The criteria string.
	 *
	 * @return  bool  True if its a valid translation
	 * @since   3.3
	 */
	private function validTranslation(string $value): bool
	{
		if (strlen($value) > 0)
		{
			if (($published = $this->getVar('translation', $value, 'abbreviation', 'published')) !== null
				&& $published == 1)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a variable 
	 *
	 * @param   string   $table        The table from which to get the variable
	 * @param   string   $where        The value where
	 * @param   string   $whereString  The target/field string where/name
	 * @param   string   $what         The return field
	 * @param   string   $operator     The operator between $whereString/field and $where/value
	 * @param   string   $main         The component in which the table is found
	 *
	 * @return  mix string/int/float
	 * @since   3.3
	 */
	private function getVar($table, $where = null, $whereString = 'user', $what = 'id', $operator = '=', $main = 'getbible')
	{
		if(!$where)
		{
			$where = Factory::getUser()->id;
		}
		// Get a db connection.
		$db = Factory::getDbo();
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array($what)));		
		if (empty($table))
		{
			$query->from($db->quoteName('#__'.$main));
		}
		else
		{
			$query->from($db->quoteName('#__'.$main.'_'.$table));
		}
		if (is_numeric($where))
		{
			$query->where($db->quoteName($whereString) . ' '.$operator.' '.(int) $where);
		}
		elseif (is_string($where))
		{
			$query->where($db->quoteName($whereString) . ' '.$operator.' '. $db->quote((string)$where));
		}
		else
		{
			return false;
		}
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadResult();
		}
		return false;
	}
	protected function checkString($string)
	{
		if (isset($string) && is_string($string) && strlen($string) > 0)
		{
			return true;
		}
		return false;
	}
}

function GetbibleBuildRoute(&$query)
{
	$router = new GetbibleRouter;
	
	return $router->build($query);
}

function GetbibleParseRoute($segments)
{
	$router = new GetbibleRouter;

	return $router->parse($segments);
}