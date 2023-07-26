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

namespace VDM\Joomla\GetBible\Utilities;


/**
 * The GetBible String Helper
 * 
 * @since 2.0.1
 */
final class StringHelper
{
	/**
	 * Return an array of words
	 *
	 * @param   string  $text  The actual sentence
	 *
	 * @return  array  An array of words
	 */
	public function split(string $text): array
	{
		if ($this->isCJK($text))
		{
			// Split by characters for languages that don't use spaces
			$words = (array) preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
		}
		elseif (strpos($text, ' ') !== false)
		{
			// Split by spaces for languages that use them
			$words = (array) preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
		}
		else
		{
			$words = [$text];
		}

		return $words;
	}

	/**
	 * Return an array of only words
	 *
	 * @param   string   $text          The actual sentence
	 * @param   bool     $punctuation   Switch to keep or remove punctuation
	 *
	 * @return  array  An array of words
	 */
	public function splitToWords(string $text, bool $punctuation = true): array
	{
		$words = array_filter($this->split($text), function($word) {
			$plain_word = preg_replace('/[^\p{L}\p{N}\s]/u', '', $word);
			if ($this->hasLength($plain_word)) {
				return true;
			} return false;
		});

		if ($words === [])
		{
			return $words;
		}

		// remove punctuation
		if (!$punctuation)
		{
			$words = array_map(function($word) {
				return preg_replace('/[^\p{L}\p{N}\s]/u', '', $word);
			}, $words);
		}

		// make the array 1 based
		// so that the key 1 is the first word
		array_unshift($words, null);
		unset($words[0]);

		return $words;
	}

	/**
	 * Checks if a string contains characters typically used in East Asian languages (Chinese, Japanese, Korean)
	 * These languages do not typically use word boundaries in the same way as languages written in Latin script
	 *
	 * @param string $text The string to be checked for CJK characters
	 *
	 * @return bool True if the string contains at least one CJK character, false otherwise
	 */
	public function isCJK(string $text): bool
	{
		if (preg_match('/[\x{4E00}-\x{9FFF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{AC00}-\x{D7AF}]/u', $text))
		{
			return true;
		}
		return false;
	}

	/**
	 * Make sure a string has a length
	 *
	 * @param   string  $word  The actual string to check
	 *
	 * @return  bool  True if its a string with characters.
	 */
	public function hasLength(string $word): bool
	{
		// Trim the string
		$trimmed = trim($word);

		// Return true if the trimmed string is not empty, false otherwise
		return !empty($trimmed);
	}
}

