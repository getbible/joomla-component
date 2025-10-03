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

namespace TrueChristianBible\Joomla\GetBible;


use TrueChristianBible\Joomla\Database\Load;
use TrueChristianBible\Joomla\Database\Delete;
use TrueChristianBible\Joomla\Abstraction\Database;


/**
 * The GetBible Cleaner of duplicated verses
 * 
 * Removes duplicate verses from the `verse` table, ensuring only the latest
 * entry (based on the 'created' timestamp) is retained per unique verse.
 * 
 * A verse is uniquely identified by: translation, book, chapter, verse.
 * 
 * @since 5.0.15
 */
final class Cleaner extends Database
{
	/**
	 * Value to track the number of verses being removed.
	 *
	 * @var   int
	 * @since 5.0.15
	 */
	protected int $verses;

	/**
	 * Value to track the number of chapters being removed.
	 *
	 * @var   int
	 * @since 5.0.15
	 */
	protected int $chapters;

	/**
	 * Value to track the number of books being removed.
	 *
	 * @var   int
	 * @since 5.0.15
	 */
	protected int $books;

	/**
	 * Loader service used to retrieve records from the database.
	 *
	 * @var   Load
	 * @since 5.0.15
	 */
	protected Load $loader;

	/**
	 * Deleter service used to remove records from the database.
	 *
	 * @var   Delete
	 * @since 5.0.15
	 */
	protected Delete $deleter;

	/**
	 * The number of duplicate groups to process per chunk.
	 *
	 * @var   int
	 * @since 5.0.15
	 */
	protected int $chunkSize = 1000;

	/**
	 * Constructor to inject loader and deleter services.
	 *
	 * @param  Load  $loader   The loader service instance.
	 * @param  Delete  $deleter  The deleter service instance.
	 *
	 * @since 5.0.15
	 */
	public function __construct(Load $loader, Delete $deleter)
	{
		parent::__construct();

		$this->loader = $loader;
		$this->deleter = $deleter;
	}

	/**
	 * Run the cleaning process across the verse table.
	 *
	 * Loads groups of duplicate verses in chunks and removes all but
	 * the most recent one (based on the 'created' timestamp).
	 *
	 * @return int  The number of verses removed
	 * @since  5.0.15
	 */
	public function verses(): int
	{
		$this->verses = 0;
		$this->processDuplicates('verse', ['abbreviation', 'book_nr', 'chapter', 'verse']);
		return $this->verses;
	}

	/**
	 * Run the cleaning process across the chapter table.
	 *
	 * Loads groups of duplicate verses in chunks and removes all but
	 * the most recent one (based on the 'created' timestamp).
	 *
	 * @return int  The number of chapters removed
	 * @since  5.0.15
	 */
	public function chapters(): int
	{
		$this->chapters = 0;
		$this->processDuplicates('chapter', ['abbreviation', 'book_nr', 'chapter']);
		return $this->chapters;
	}

	/**
	 * Run the cleaning process across the chapter table.
	 *
	 * Loads groups of duplicate verses in chunks and removes all but
	 * the most recent one (based on the 'created' timestamp).
	 *
	 * @return int  The number of books removed
	 * @since  5.0.15
	 */
	public function books(): int
	{
		$this->books = 0;
		$this->processDuplicates('book', ['abbreviation', 'nr']);
		return $this->books;
	}

	/**
	 * Central processing loop for duplicates.
	 *
	 * @param  string   $table     Table name
	 * @param  string[] $groupBy   Fields to group by
	 *
	 * @return void
	 * @since  5.0.15
	 */
	protected function processDuplicates(string $table, array $groupBy): void
	{
		$offset = 0;

		do {
			$duplicates = $this->getDuplicateChunk($table, $groupBy, $offset, $this->chunkSize);

			foreach ($duplicates as $group)
			{
				$this->cleanGroup($table, $group);
			}

			$offset += $this->chunkSize;
		} while (count($duplicates) === $this->chunkSize);
	}

	/**
	 * Fetch a chunk of duplicate keys.
	 *
	 * @param  string   $table    Table name
	 * @param  string[] $fields   Fields to group by
	 * @param  int      $offset   Pagination offset
	 * @param  int      $limit    Chunk size
	 *
	 * @return array<int, array<string, mixed>>
	 * @since  5.0.15
	 */
	protected function getDuplicateChunk(string $table, array $fields, int $offset, int $limit): array
	{
		$query = $this->db->createQuery()
			->select(array_merge($fields, ['COUNT(*) AS count']))
			->from($this->db->quoteName($this->getTable($table)))
			->group(array_map([$this->db, 'quoteName'], $fields))
			->having('count > 1')
			->order(implode(', ', array_map(fn($f) => $f . ' ASC', $fields)))
			->setLimit($limit, $offset);

		$this->db->setQuery($query);
		$results = $this->db->loadAssocList();

		if (!is_array($results) || empty($results))
		{
			return [];
		}

		return array_map(function ($row) use ($fields) {
			$group = [];
			foreach ($fields as $field)
			{
				$group[$field] = is_numeric($row[$field]) ? (int) $row[$field] : $row[$field];
			}
			return $group;
		}, $results);
	}

	/**
	 * Cleans a single duplicate group, retaining the most recent record.
	 *
	 * @param  string                $table  Table name
	 * @param  array<string, mixed> $keys   Unique identifiers
	 *
	 * @return void
	 * @since  5.0.15
	 */
	protected function cleanGroup(string $table, array $keys): void
	{
		$rows = $this->loader->rows(
			['key' => 'id', 'id', 'created'],
			['a' => $table],
			$keys,
			['created' => 'DESC', 'id' => 'DESC']
		);

		if ($rows === null || count($rows) <= 1)
		{
			return;
		}

		$keepId = (int) array_key_first($rows);
		unset($rows[$keepId]);

		$deleteIds = array_keys($rows);
		$count = count($deleteIds);

		switch ($table)
		{
			case 'verse':   $this->verses += $count; break;
			case 'chapter': $this->chapters += $count; break;
			case 'book':    $this->books += $count; break;
		}

		$this->deleter->items(
			[
				'id' => [
					'value' => array_values($deleteIds),
					'operator' => 'IN',
					'quote' => false,
				],
			],
			$table
		);
	}
}

