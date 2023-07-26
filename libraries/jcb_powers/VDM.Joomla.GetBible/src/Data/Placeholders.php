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


use VDM\Joomla\GetBible\Data\Scripture;
use VDM\Joomla\GetBible\Data\Prompt;


/**
 * The GetBible Prompt Placeholders
 * 
 * @since 2.0.1
 */
final class Placeholders
{
	/**
	 * The Scripture class
	 *
	 * @var    Scripture
	 * @since  2.0.1
	 */
	protected Scripture $scripture;

	/**
	 * The Prompt class
	 *
	 * @var    Prompt
	 * @since  2.0.1
	 */
	protected Prompt $prompt;

	/**
	 * Constructor
	 *
	 * @param Scripture   $scripture          The scripture object.
	 * @param Prompt        $prompt        The prompt object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Scripture $scripture,
		Prompt $prompt)
	{
		$this->scripture = $scripture;
		$this->prompt = $prompt;
	}

	/**
	 * Get the Open AI response
	 *
	 * @return  array   Array of response messages
	 * @since   2.0.1
	 */
	public function get(): array
	{
		$integration = $this->prompt->getIntegration();
		$cache_behaviour = $this->prompt->getCacheBehaviour();

		if ($integration === null || $cache_behaviour === null)
		{
			return [];
		}

		if (($integration == 1 || $integration == 3) && ($cache_behaviour == 0 || $cache_behaviour == 2))
		{
			return $this->all();
		}
		elseif ($integration == 2 && ($cache_behaviour == 0 || $cache_behaviour == 2))
		{
			return $this->without();
		}

		return $this->words();
	}

	/**
	 * Get All Placeholders
	 *
	 * @return  array    Array of Placeholders
	 * @since   2.0.1
	 */
	private function all(): array
	{
		return [
			'[translation_name]' => $this->scripture->get('translation_name', ''),
			'[translation_language]' => $this->scripture->get('translation_language', ''),
			'[translation_lcsh]' => $this->scripture->get('translation_lcsh', ''),
			'[translation_abbreviation]' => $this->scripture->get('translation_abbreviation', ''),
			'[book_number]' => $this->scripture->get('book_number', ''),
			'[book_name]' => $this->scripture->get('book_name', ''),
			'[chapter_number]' => $this->scripture->get('chapter_number', ''),
			'[chapter_name]' => $this->scripture->get('chapter_name', ''),
			'[chapter_text]' => $this->scripture->get('chapter_text', ''),
			'[verse_number]' => $this->scripture->get('verse_number', ''),
			'[verse_name]' => $this->scripture->get('verse_name', ''),
			'[verse_text]' => $this->scripture->get('verse_text', ''),
			'[selected_word_number]' => $this->scripture->get('selected_word_number', ''),
			'[selected_word_text]' => $this->scripture->get('selected_word_text', '')
		];
	}

	/**
	 * Get All Placeholders without words
	 *
	 * @return  array    Array of Placeholders
	 * @since   2.0.1
	 */
	private function without(): array
	{
		return [
			'[translation_name]' => $this->scripture->get('translation_name', ''),
			'[translation_language]' => $this->scripture->get('translation_language', ''),
			'[translation_lcsh]' => $this->scripture->get('translation_lcsh', ''),
			'[translation_abbreviation]' => $this->scripture->get('translation_abbreviation', ''),
			'[book_number]' => $this->scripture->get('book_number', ''),
			'[book_name]' => $this->scripture->get('book_name', ''),
			'[chapter_number]' => $this->scripture->get('chapter_number', ''),
			'[chapter_name]' => $this->scripture->get('chapter_name', ''),
			'[chapter_text]' => $this->scripture->get('chapter_text', ''),
			'[verse_number]' => $this->scripture->get('verse_number', ''),
			'[verse_name]' => $this->scripture->get('verse_name', ''),
			'[verse_text]' => $this->scripture->get('verse_text', '')
		];
	}

	/**
	 * Get Words Placeholders
	 *
	 * @return  array    Array of Placeholders
	 * @since   2.0.1
	 */
	private function words(): array
	{
		return [
			'[translation_language]' => $this->scripture->get('translation_language', ''),
			'[translation_lcsh]' => $this->scripture->get('translation_lcsh', ''),
			'[selected_word_text]' => $this->scripture->get('selected_word_text', '')
		];
	}
}

