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
 * The GetBible Book Data
 * 
 * @since 2.0.1
 */
final class Book
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
	 * The books
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $books = [];

	/**
	 * Constructor
	 *
	 * @param Load       $load      The load object.
	 * @param Config     $config    The config object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Load $load, Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}

	/**
	 * Get the book number
	 *
	 * @return  string   The book number
	 * @since   2.0.1
	 */
	public function getNumber(): string
	{
		$book = $this->get();
		return $book ? $book->nr ?? '' : '';
	}

	/**
	 * Get the book name
	 *
	 * @return  string   The book name
	 * @since   2.0.1
	 */
	public function getName(): string
	{
		$book = $this->get();
		return $book ? $book->name ?? '' : '';
	}

	/**
	 * Get the book
	 *
	 * @return  object|null   True on success
	 * @since   2.0.1
	 */
	private function get(): ?object
	{
		$abbreviation = $this->config->get('translation');
		$book = $this->config->get('book');

		if (empty($abbreviation) || empty($book))
		{
			return null;
		}

		$cacheKey = $this->generateCacheKey($abbreviation, $book);

		if (isset($this->books[$cacheKey]))
		{
			return $this->books[$cacheKey];
		}

		return $this->loadBookData($abbreviation, $book);
	}

	/**
	 * Loads the book data from the database and updates the cache
	 *
	 * @param string  $abbreviation   The translation abbreviation.
	 * @param int     $book           The book number.
	 *
	 * @return object|null  The loaded book data, or null if not found or an error occurred.
	 * @since  2.0.1
	 */
	private function loadBookData(string $abbreviation, int $book): ?object
	{
		$data = $this->load->item(
			['abbreviation' => $abbreviation, 'nr' => $book],
			'book'
		);

		if (!is_object($data))
		{
			return null;
		}

		$cacheKey = $this->generateCacheKey($abbreviation, $book);

		$this->books[$cacheKey] = $data;

		return $this->books[$cacheKey];
	}

	/**
	 * Generates a cache key based on the abbreviation, and book
	 *
	 * @param string $abbreviation  The translation abbreviation.
	 * @param int    $book          The book number.
	 *
	 * @return string The generated cache key.
	 * @since   2.0.1
	 */
	private function generateCacheKey(string $abbreviation, int $book): string
	{
		return $abbreviation . $book;
	}
}

