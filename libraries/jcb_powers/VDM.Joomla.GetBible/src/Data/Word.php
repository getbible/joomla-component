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


use VDM\Joomla\GetBible\Data\Verse;
use VDM\Joomla\GetBible\Openai\Config;
use VDM\Joomla\GetBible\Data\Prompt;


/**
 * The GetBible Word Data
 * 
 * @since 2.0.1
 */
final class Word
{
	/**
	 * The Verse class
	 *
	 * @var    Verse
	 * @since  2.0.1
	 */
	protected Verse $verse;

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
	 * The words
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $words = [];

	/**
	 * The check if words are sequential
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $sequential = [];

	/**
	 * The active verses
	 *
	 * @var    array|null
	 * @since  2.0.1
	 */
	protected ?array $verses = null;

	/**
	 * The valid verse numbers
	 *
	 * @var    array|null
	 * @since  2.0.1
	 */
	protected ?array $valid = null;

	/**
	 * Constructor
	 *
	 * @param Verse       $verse       The verse object.
	 * @param Config      $config      The config object.
	 * @param Prompt      $prompt      The prompt object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Verse $verse, Config $config, Prompt $prompt)
	{
		$this->verse = $verse;
		$this->config = $config;
		$this->prompt = $prompt;
	}

	/**
	 * Get the word number/s
	 *
	 * @return  string     The word number/s
	 * @since  2.0.1
	 */
	public function getNumber(): string
	{
		$word = $this->get();
		return $word ? $word->number ?? '' : '';
	}

	/**
	 * Get the word text
	 *
	 * @return  string     The verse text
	 * @since   2.0.1
	 */
	public function getText(): string
	{
		$word = $this->get();
		return $word ? $word->text ?? '' : '';
	}

	/**
	 * Get the words
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
		$words = $this->config->get('words');

		if (empty($abbreviation) || empty($book) || empty($chapter) || empty($verse) || empty($words))
		{
			return null;
		}

		$cacheKey = $this->generateCacheKey($abbreviation, $book, $chapter, $verse, $words);

		if (isset($this->words[$cacheKey]))
		{
			return $this->words[$cacheKey];
		}

		return $this->loadWordData($cacheKey, $verse, $words);
	}

	/**
	 * Loads the word data
	 *
	 * @param string   $cacheKey    The cache key.
	 * @param string   $verses      The selected verses.
	 * @param string   $words       The selected words.
	 *
	 * @return object|null The loaded word data, or null if not found or an error occurred.
	 * @since  2.0.1
	 */
	private function loadWordData(string $cacheKey, string $verses, string $words): ?object
	{
		$this->valid = $this->verse->getValid();
		$this->verses = $this->verse->getVerse();

		if (empty($this->verses) || empty($this->valid))
		{
			$this->words[$cacheKey] = null;

			return null;
		}

		$data = new \stdClass();

		$data->number_array = $this->selectedWordNumbers($verses, $words);

		if (empty($data->number_array))
		{
			$this->words[$cacheKey] = null;

			return null;
		}

		$data->number = $this->selectedWordNumbersToString($data->number_array);

		$data->text_array = $this->selectedWord($data->number_array);

		if (empty($data->text_array))
		{
			$this->words[$cacheKey] = null;

			return null;
		}

		$data->text = $this->selectedWordToString($data->text_array);

		$this->words[$cacheKey] = $data;

		return $this->words[$cacheKey];
	}

	/**
	 * Build word number array from verse and words.
	 *
	 * @param   string  $verse  The verse selected.
	 * @param   string  $words  The words words.
	 *
	 * @return  array  The word number array.
	 * @since  2.0.1
	 */
	private function selectedWordNumbers(string $verse, string $words): array
	{
		$verse = $this->splitAndTrim($verse);
		$words = $this->splitAndTrim($words);

		$array = [];
		$integration = $this->prompt->getIntegration();
		$this->sequential = [];

		foreach ($verse as $key => $verse)
		{
			if (isset($words[$key]) && in_array($verse, $this->valid) &&
				$this->isValidWordNumber($verse, $words[$key]))
			{
				$array[$verse][] = $words[$key];
				if ($integration == 1)
				{
					break;
				}
			}
		}

		return $array;
	}

	/**
	 * Converts word number array to string.
	 *
	 * @param   array  $wordNumberArray  The word number array.
	 *
	 * @return  string  The word number string.
	 * @since  2.0.1
	 */
	private function selectedWordNumbersToString(array $wordNumberArray): string
	{
		$word_number = [];

		if (count($wordNumberArray) == 1)
		{
			$word_number[] = implode(',', array_values($wordNumberArray)[0]);
		}
		else
		{
			foreach ($wordNumberArray as $verse => $words) 
			{
				$word_number[] = $verse . ':' . implode(',', $words);
			}
		}

		return implode(';', $word_number);
	}

	/**
	 * Build word array from verse and words.
	 *
	 * @param   array  $words  The words array.
	 *
	 * @return  array  The word array.
	 * @since  2.0.1
	 */
	private function selectedWord(array $words): array
	{
		$word_array = [];
		foreach ($words as $verse => $word_numbers)
		{
			foreach ($word_numbers as $word)
			{
				if (isset($this->verses[$verse][$word]))
				{
					$word_array[$verse][] = $this->verses[$verse][$word];
				}
			}
		}

		return $word_array;
	}

	/**
	 * Converts word array to string.
	 *
	 * @param   array  $wordArray  The word array.
	 *
	 * @return  string  The word string.
	 * @since  2.0.1
	 */
	private function selectedWordToString(array $wordArray): string
	{
		$word_number = [];

		foreach ($wordArray as $verse => $words) 
		{
			$word_number[] = implode(' ', $words);
		}

		return implode(' ', $word_number);
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
	 * Check if a word exist in a verse
	 *
	 * @param   int     $verseNumber  The verse number.
	 * @param   int     $wordNumber   The word number.
	 *
	 * @return  bool    True on success
	 * @since   2.0.1
	 */
	private function isValidWordNumber(int $verseNumber, int $wordNumber): bool
	{
		// we add the next word number to check sequential selection of words
		$this->sequential[$verseNumber][$wordNumber] = $wordNumber;
		if (count($this->sequential[$verseNumber]) > 1 && !$this->isSequential($this->sequential[$verseNumber]))
		{
			return false;
		}

		return isset($this->verses[$verseNumber][$wordNumber]);
	}

	/**
	 * Check if an array values is sequential.
	 *
	 * @param   array  $arr  The number array.
	 *
	 * @return  bool true if sequential
	 * @since  2.0.1
	 */
	private function isSequential(array $arr): bool
	{
		$arr = array_values($arr); // Reset keys
		for ($i = 0, $len = count($arr) - 1; $i < $len; $i++)
		{
			if ($arr[$i] + 1 !== $arr[$i + 1])
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Generates a cache key based on the abbreviation, book, chapter, verses, and  words
	 *
	 * @param string  $abbreviation  The translation abbreviation.
	 * @param int     $book          The book number.
	 * @param int     $chapter       The chapter number.
	 * @param string  $verses        The selected verses.
	 * @param string  $words         The selected words.
	 *
	 * @return string The generated cache key.
	 * @since   2.0.1
	 */
	private function generateCacheKey($abbreviation, $book, int $chapter, string $verses, string $words): string
	{
		return $abbreviation . '_' . $book . '_' . $chapter . '_' . $verses . '_' . $words;
	}
}

