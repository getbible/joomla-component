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


use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Openai\Config;


/**
 * The GetBible Chapter Data
 * 
 * @since 2.0.1
 */
final class Chapter
{
	/**
	 * The Load class
	 *
	 * @var    Load
	 * @since  2.0.1
	 */
	protected Load $load;

	/**
	 * The Config class
	 *
	 * @var    Config
	 * @since  2.0.1
	 */
	protected Config $config;

	/**
	 * The chapters
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $chapters = [];

	/**
	 * Constructor
	 *
	 * @param Load            $load           The load object.
	 * @param Config          $config         The config object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Load $load, Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}

	/**
	 * Get the chapter number
	 *
	 * @return  string     The chapter number
	 * @since   2.0.1
	 */
	public function getNumber(): string
	{
		$chapter = $this->get();
		return $chapter ? $chapter->chapter ?? '' : '';
	}

	/**
	 * Get the chapter name
	 *
	 * @return  string     The chapter name
	 * @since   2.0.1
	 */
	public function getName(): string
	{
		$chapter = $this->get();
		return $chapter ? $chapter->name ?? '' : '';
	}

	/**
	 * Get the chapter text
	 *
	 * @return  string     The chapter text
	 * @since   2.0.1
	 */
	public function getText(): string
	{
		$chapter = $this->get();
		return $chapter ? $chapter->text ?? '' : '';
	}

	/**
	 * Get the chapter verses
	 *
	 * @return  array|null     The chapter verses
	 * @since   2.0.1
	 */
	public function getVerses(): ?array
	{
		$chapter = $this->get();
		return $chapter ? $chapter->verses ?? null : null;
	}

	/**
	 * Get the chapter
	 *
	 * @return  object|null   True on success
	 * @since   2.0.1
	 */
	private function get(): ?object
	{
		$abbreviation = $this->config->get('translation');
		$book = $this->config->get('book');
		$chapter = $this->config->get('chapter');

		if (empty($abbreviation) || empty($book) || empty($chapter))
		{
			return null;
		}

		$cacheKey = $this->generateCacheKey($abbreviation, $book, $chapter);

		if (isset($this->chapters[$cacheKey]))
		{
			return $this->chapters[$cacheKey];
		}

		return $this->loadChapterData($abbreviation, (int) $book, (int) $chapter);
	}

	/**
	 * Loads the chapter data from the database and updates the cache
	 *
	 * @param string $abbreviation The translation abbreviation.
	 * @param int $book The book number.
	 * @param int $chapter The chapter number.
	 *
	 * @return object|null The loaded chapter data, or null if not found or an error occurred.
	 * @since  2.0.1
	 */
	private function loadChapterData(string $abbreviation, int $book, int $chapter): ?object
	{
		$data = $this->load->item(
			['abbreviation' => $abbreviation, 'book_nr' => $book, 'chapter' => $chapter, 'published' => 1],
			'chapter'
		);

		if (!is_object($data))
		{
			return null;
		}

		$data->verses = $this->load->items(
			['abbreviation' => $abbreviation, 'book_nr' => $book, 'chapter' => $chapter, 'published' => 1],
			'verse'
		);

		if ($data->verses === null || !is_array($data->verses) || $data->verses === [])
		{
			return null;
		}

		$cacheKey = $this->generateCacheKey($abbreviation, $book, $chapter);

		$data->text = $this->convertVersesToText($data->verses);

		$this->chapters[$cacheKey] = $data;

		return $this->chapters[$cacheKey];
	}

	/**
	 * Convert verses to text
	 *
	 * @param array $verses  The chapter verses.
	 *
	 * @return string The verses in text.
	 * @since   2.0.1
	 */
	private function convertVersesToText(array $verses): string
	{
		$text = [];
		foreach ($verses as $verse)
		{
			$text[] = trim($verse->verse) . ' ' . trim($verse->text);
		}

		return implode(' ', $text);
	}

	/**
	 * Generates a cache key based on the abbreviation, book, and chapter
	 *
	 * @param string $abbreviation  The translation abbreviation.
	 * @param int    $book          The book number.
	 * @param int    $chapter       The chapter number.
	 *
	 * @return string The generated cache key.
	 * @since   2.0.1
	 */
	private function generateCacheKey(string $abbreviation, int $book, int $chapter): string
	{
		return $abbreviation . '_' . $book . '_' . $chapter;
	}
}

