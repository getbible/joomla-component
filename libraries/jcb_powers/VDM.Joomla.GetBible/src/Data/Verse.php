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

namespace VDM\Joomla\GetBible\Data;


use VDM\Joomla\GetBible\Data\Chapter;
use VDM\Joomla\GetBible\Openai\Config;
use VDM\Joomla\GetBible\Data\Prompt;
use VDM\Joomla\GetBible\Utilities\StringHelper;


/**
 * The GetBible Verse Data
 * 
 * @since 2.0.1
 */
final class Verse
{
	/**
	 * The Chapter class
	 *
	 * @var    Chapter
	 * @since  2.0.1
	 */
	protected Chapter $chapter;

	/**
	 * The Config class
	 *
	 * @var    Config
	 * @since  2.0.1
	 */
	protected Config $config;

	/**
	 * The Prompt class
	 *
	 * @var    Prompt
	 * @since  2.0.1
	 */
	protected Prompt $prompt;

	/**
	 * The StringHelper class
	 *
	 * @var    StringHelper
	 * @since  2.0.1
	 */
	protected StringHelper $stringHelper;

	/**
	 * The verses
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $verses = [];

	/**
	 * Constructor
	 *
	 * @param Chapter         $chapter        The chapter object.
	 * @param Config          $config         The config object.
	 * @param Prompt          $prompt         The prompt object.
	 * @param StringHelper    $stringHelper   The string helper object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Chapter $chapter, Config $config, Prompt $prompt, StringHelper $stringHelper)
	{
		$this->chapter = $chapter;
		$this->config = $config;
		$this->prompt = $prompt;
		$this->stringHelper = $stringHelper;
	}

	/**
	 * Get the verse number/s
	 *
	 * @return  string     The verse number/s
	 * @since   2.0.1
	 */
	public function getNumber(): string
	{
		$verse = $this->get();
		return $verse ? $verse->number ?? '' : '';
	}

	/**
	 * Get the verse name
	 *
	 * @return  string     The verse name
	 * @since   2.0.1
	 */
	public function getName(): string
	{
		$verse = $this->get();
		return $verse ? $verse->name ?? '' : '';
	}

	/**
	 * Get the verse text
	 *
	 * @return  string     The verse text
	 * @since   2.0.1
	 */
	public function getText(): string
	{
		$verse = $this->get();
		return $verse ? $verse->text ?? '' : '';
	}

	/**
	 * Get the verse array
	 *
	 * @return  array|null     The verse array
	 * @since   2.0.1
	 */
	public function getVerse(): ?array
	{
		$verse = $this->get();
		return $verse ? $verse->verse ?? null : null;
	}

	/**
	 * Get the valid verse numbers
	 *
	 * @return  array|null     The verse number array
	 * @since   2.0.1
	 */
	public function getValid(): ?array
	{
		$verse = $this->get();
		return $verse ? $verse->valid ?? null : null;
	}

	/**
	 * Get the verse
	 *
	 * @return  object|null   True on success
	 * @since   2.0.1
	 */
	private function get(): ?object
	{
		$abbreviation = $this->config->get('translation');
		$book = $this->config->get('book');
		$chapter = $this->config->get('chapter');
		$verse = $this->config->get('verse');

		if (empty($abbreviation) || empty($book) || empty($chapter) || empty($verse))
		{
			return null;
		}

		$cacheKey = $this->generateCacheKey($abbreviation, $book, $chapter, $verse);

		if (isset($this->verses[$cacheKey]))
		{
			return $this->verses[$cacheKey];
		}

		return $this->loadVerseData($cacheKey, $verse);
	}

	/**
	 * Loads the verse data
	 *
	 * @param string   $cacheKey    The cache key.
	 * @param string   $verses      The selected verses.
	 *
	 * @return object|null The loaded verse data, or null if not found or an error occurred.
	 * @since  2.0.1
	 */
	private function loadVerseData(string $cacheKey, string $verses): ?object
	{
		$chapter = $this->chapter->getVerses();

		if (empty($chapter))
		{
			$this->verses[$cacheKey] = null;

			return null;
		}

		$data = new \stdClass();

		$data->range = $this->selectedVersesRange($verses);

		$data->number = $this->validateSelectedVersesNumbers($data->range, $chapter);

		$chapter_name = $this->chapter->getName();

		if (empty($data->number) || empty($chapter_name))
		{
			$this->verses[$cacheKey] = null;

			return null;
		}

		$data->name = $chapter_name . ':' . $data->number;

		$data->text = $this->getVersesString($data->range, $chapter);

		$data->verse = $this->getVersesArray($data->range, $chapter);

		if (empty($data->text) || empty($data->verse))
		{
			$this->verses[$cacheKey] = null;

			return null;
		}

		$data->valid = $this->validVerseNumbers($data->range);

		$this->verses[$cacheKey] = $data;

		return $this->verses[$cacheKey];
	}

	/**
	 * Get the range of verses selected
	 *
	 * @param string  $verses  The selected verses
	 *
	 * @return array   The the raw selected verses
	 * @since   2.0.1
	 */
	private function selectedVersesRange(string $verses): array
	{
		$bucket = [];

		if (strpos($verses, '-') !== false)
		{
			$_verses = $this->splitAndTrim($verses);

			$min_verse = min($_verses);
			$max_verse = max($_verses);

			$bucket[] = $min_verse;
			if ($min_verse != $max_verse && $this->prompt->getIntegration() == 2)
			{
				$bucket[] = $max_verse;
			}
		}
		else
		{
			$bucket[] = trim($verses);
		}

		return $bucket;
	}

	/**
	 * Split string by '-' and trim each element.
	 *
	 * @param   string  $str  The string to be split.
	 *
	 * @return  array   The splitted and trimmed array.
	 * @since  2.0.1
	 */
	private function splitAndTrim(string $str): array 
	{
		if (strpos($str, '-') !== false)
		{
			$array = array_map('trim', explode('-', $str));

			sort($array);

			return $array;
		}

		return [trim($str)];
	}

	/**
	 * Validate that these verse numbers exist in chapter
	 *
	 * @param array   $verses   The selected verse numbers
	 * @param array   $chapter  The chapter verses
	 *
	 * @return string|null The the valid verse numbers or empty string
	 * @since   2.0.1
	 */
	private function validateSelectedVersesNumbers(array &$verses, array $chapter): ?string
	{
		$valid = [];

		foreach ($chapter as $verse)
		{
			if (isset($verses[0]) && $verse->verse == $verses[0])
			{
				$valid[0] = $verses[0];
			}
			elseif (isset($verses[1]) && $verse->verse == $verses[1])
			{
				$valid[1] = $verses[1];
			}
		}

		if ($valid !== [])
		{
			// update the verse array to its valid state
			$verses = $valid;

			return implode('-', $valid);
		}

		$verses = [];

		return null;
	}

	/**
	 * Get the verses selected as text string
	 *
	 * @param array   $verses   The valid selected verse numbers
	 * @param array   $chapter  The chapter verses
	 *
	 * @return string|null   The selected verses as a string
	 * @since   2.0.1
	 */
	private function getVersesString(array $verses, array $chapter): ?string
	{
		$text = [];

		$add = false;

		foreach ($chapter as $verse)
		{
			if ($verse->verse == $verses[0])
			{
				$add = true;
			}
			elseif (!isset($verses[1]))
			{
				$add = false;
			}

			if ($add)
			{
				$text[] = trim($verse->verse) . ' ' . trim($verse->text);
			}

			if (isset($verses[1]) && $verse->verse == $verses[1])
			{
				$add = false;
			}
		}

		if (empty($text))
		{
			return null;
		}

		return implode(' ', $text);
	}

	/**
	 * Get the verses selected as multidimensional array
	 *
	 * @param array   $verses   The valid selected verse numbers
	 * @param array   $chapter  The chapter verses
	 *
	 * @return array|null   The selected verses as an array
	 * @since   2.0.1
	 */
	private function getVersesArray(array $verses, array $chapter): ?array
	{
		$text = [];

		$add = false;

		foreach ($chapter as $verse)
		{
			if ($verse->verse == $verses[0])
			{
				$add = true;
			}
			elseif (!isset($verses[1]))
			{
				$add = false;
			}

			if ($add)
			{
				$text[$verse->verse] = $this->stringHelper->splitToWords(trim($verse->text), false);
			}

			if (isset($verses[1]) && $verse->verse == $verses[1])
			{
				$add = false;
			}
		}

		if (empty($text))
		{
			return null;
		}

		return $text;
	}

	/**
	 * Get all valid verse numbers
	 *
	 * @param array  $verses  The verse range
	 *
	 * @return array|null   The the valid verses
	 * @since   2.0.1
	 */
	private function validVerseNumbers(array $range): ?array
	{
		if(count($range) == 1)
		{
			return $range;
		}
		elseif(count($range) == 2)
		{
			// sort the array in ascending order to make sure the range goes from lower to higher
			sort($range);

			return range($range[0], $range[1]);
		}

		return null;
	}

	/**
	 * Generates a cache key based on the abbreviation, book, chapter, and verses
	 *
	 * @param string $abbreviation  The translation abbreviation.
	 * @param int    $book          The book number.
	 * @param int    $chapter       The chapter number.
	 * @param string $verses        The selected verses.
	 *
	 * @return string The generated cache key.
	 * @since   2.0.1
	 */
	private function generateCacheKey($abbreviation, $book, int $chapter, string $verses): string
	{
		return $abbreviation . '_' . $book . '_' . $chapter . '_' . $verses;
	}
}

