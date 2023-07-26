<?php
/**
 * @package    GetBible
 *
 * @created    30th May, 2023
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        GetBible <https://git.vdm.dev/getBible>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\GetBible;


use VDM\Joomla\GetBible\Config;
use VDM\Joomla\GetBible\Utilities\Http;
use VDM\Joomla\GetBible\Database\Load;


/**
 * The GetBible Daily Scripture
 * 
 * @since 2.0.1
 */
final class DailyScripture
{
	/**
	 * The book number
	 *
	 * @var    string|null
	 * @since  2.0.1
	 */
	protected ?int $book = null;

	/**
	 * The chapter number
	 *
	 * @var    int|null
	 * @since  2.0.1
	 */
	protected ?int $chapter = null;

	/**
	 * The verses string
	 *
	 * @var    string|null
	 * @since  2.0.1
	 */
	protected ?string $verses = null;

	/**
	 * The reference string
	 *
	 * @var    string|null
	 * @since  2.0.1
	 */
	protected ?string $reference = null;

	/**
	 * The load object
	 *
	 * @var    Load
	 * @since  2.0.1
	 */
	protected Load $load;

	/**
	 * The active verse
	 *
	 * @var    string
	 * @since  2.0.1
	 */
	protected string $active = '';

	/**
	 * Constructor
	 *
	 * @param Config    $config    The config object.
	 * @param Http      $http      The http object.
	 * @param Load      $load      The load object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Config $config, Http $http, Load $load)
	{
		$response = $http->get($config->daily_scripture_url);

		// make sure we got the correct response
		if ($response->code == 200 && isset($response->body) && is_string($response->body))
		{
			$this->reference = $response->body;

			$this->parse($this->reference);
		}

		$this->load = $load;
	}

	/**
	 * method to validate if this is still the daily verse
	 *
	 * @return  bool true if it is daily
	 * @since   2.0.1
	 */
	public function isDaily(): bool
	{
		return (strcasecmp($this->active, $this->reference) == 0);
	}

	/**
	 * Set Current active verse
	 *
	 * @return  int|null  Book number
	 * @return  int|null  Chapter number
	 * @return  string|null  Verses
	 *
	 * @since   2.0.1
	 */
	public function setActive(?int $book, ?int $chapter, ?string $verses): void
	{
		$active = '';
		if ($book !== null)
		{
			$active = $book;
			if ($chapter !== null)
			{
				$this->book = (int) $book;
				$this->chapter = (int) $chapter;
				$active .= ' ' . $chapter;
				if ($verses !== null)
				{
					$active .= ':' . $verses;
					$this->verses = $verses;
				}
				$this->active = $active;
			}
		}
	}

	/**
	 * An option to load another reference
	 *
	 * @param string    $reference    The scriptural reference.
	 *
	 * @since   2.0.1
	 */
	public function load(string $reference)
	{
		// convert book name to book number
		if (($name = $this->extract($reference)) !== null)
		{
			if (($number = $this->load->value(
				['name' => $name], 'nr', 'book')) === null)
			{
				// the book number could not be found
				return;
			}

			$reference = $this->replace($reference, $name, $number);
		}

		$this->parse($reference);

		if ($this->book === null)
		{
			$this->parse($this->reference);
		}
	}

	/**
	 * Get the book number from the reference
	 *
	 * @return  int|null  Book number
	 * @since   2.0.1
	 */
	public function book(): ?int
	{
		return $this->book;
	}

	/**
	 * Get the chapter from the reference
	 *
	 * @return  int|null  Chapter number
	 * @since   2.0.1
	 */
	public function chapter(): ?int
	{
		return $this->chapter;
	}

	/**
	 * Get the verses from the reference
	 *
	 * @return  string|null  Verses
	 * @since   2.0.1
	 */
	public function verses(): ?string
	{
		return $this->verses;
	}

	/**
	 * Parse the scriptural reference
	 *
	 * @param string    $reference    The scriptural reference.
	 *
	 * @since   2.0.1
	 */
	private function parse(string $reference)
	{
		$this->active = $reference;
		$parts = explode(' ', $reference);

		$this->book = (isset($parts[0]) && is_numeric($parts[0])) ? intval($parts[0]) : null;
		$chapterVerses = isset($parts[1]) ? explode(':', $parts[1]) : [null, null];

		$this->chapter = (isset($chapterVerses[0]) && is_numeric($chapterVerses[0])) ? intval($chapterVerses[0]) : null;
		$this->verses = isset($chapterVerses[1]) ? trim($chapterVerses[1]) : null;
	}

	/**
	 * Extract the book name from the reference
	 *
	 * @return  string|null  Book name
	 * @since   2.0.1
	 */
	private function extract(string $reference): ?string
	{
		// Use regex to match and remove chapter:verse and their variations (if they exist) from the end of the string
		// This new regex considers Unicode word boundaries
		$bookName = preg_replace('/\b\d+(:(\d+([,-]\d+)*)?\b)*$/u', '', $reference);

		// If there's no match or the remaining string is empty or numeric, return null
		// The is_numeric check has been adjusted to work for Unicode strings
		if (mb_strlen(trim($bookName)) === 0 || preg_match('/^\d+$/u', $bookName))
		{
			return null;
		}

		return trim($bookName);
	}

	/**
	 * Replace the book name with a number in the reference
	 *
	 * @param   string  $reference  Original reference
	 * @param   string  $name       Book name
	 * @param   int     $number     Book number
	 *
	 * @return  string  New reference with the book number instead of the name
	 * @since   2.0.1
	 */
	private function replace(string $reference, string $name, int $number): string
	{
		return $this->mb_str_replace($name, "$number", $reference);
	}

	/**
	 * Build in str_replace that will work with all languages
	 *
	 * @param   string   $search    The search phrase/word
	 * @param   string   $replace   The replace phrase/word
	 * @param   string   $subject   The string to update
	 *
	 * @return  string  New updated string
	 * @since   2.0.1
	 */
	private function mb_str_replace(string $search, string $replace, string $subject): string
	{
		return mb_ereg_replace(preg_quote($search), $replace, $subject);
	}
}

