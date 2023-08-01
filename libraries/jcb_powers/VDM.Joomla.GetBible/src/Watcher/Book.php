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

namespace VDM\Joomla\GetBible\Watcher;


use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Database\Insert;
use VDM\Joomla\GetBible\Database\Update;
use VDM\Joomla\GetBible\Api\Books;
use VDM\Joomla\GetBible\Abstraction\Watcher;


/**
 * The GetBible Book Watcher
 * 
 * @since 2.0.1
 */
final class Book extends Watcher
{
	/**
	 * The Books class
	 *
	 * @var    Books
	 * @since  2.0.1
	 */
	protected Books $books;

	/**
	 * Constructor
	 *
	 * @param Load           $load        The load object.
	 * @param Insert         $insert      The insert object.
	 * @param Update         $update      The update object.
	 * @param Books          $books       The books API object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Books $books)
	{
		// load the parent constructor
		parent::__construct($load, $insert, $update);

		$this->books = $books;

		// set the table
		$this->table = 'book';
	}

	/**
	 * Sync the target being watched
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	public function sync(string $translation, int $book): bool
	{
		// load the target if not found
		if ($this->load($translation, $book))
		{
			if ($this->isNew() || $this->hold())
			{
				return true;
			}

			// get API hash value
			$hash = $this->books->sha($translation, $book);

			// confirm hash has not changed
			if (hash_equals($hash, $this->target->sha))
			{
				return $this->bump();
			}

			if ($this->update($translation))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Load Book
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 *
	 * @return  bool   True if translation found
	 * @since   2.0.1
	 */
	private function load(string $translation, int $book): bool
	{
		// check local value
		if (($this->target = $this->load->item(['abbreviation' => $translation, 'nr' => $book], $this->table)) !== null)
		{
			return true;
		}

		// get all this translation books
		$books = $this->books->list($translation);

		// check return data
		if (!isset($books->{$book}) || !isset($books->{$book}->sha))
		{
			return false;
		}

		// add them to the database
		$this->insert->items((array) $books, 'book');

		if (($this->target = $this->load->item(['abbreviation' => $translation, 'nr' => $book], $this->table)) !== null)
		{
			$this->fresh = true;
		}

		return $this->fresh;
	}

	/**
	 * Trigger the update of all books of this translation
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  bool    True if update was a success
	 * @since   2.0.1
	 */
	private function update(string $translation): bool
	{
		// get translations from the API
		if (($books = $this->books->list($translation)) === null)
		{
			return false;
		}

		// get the local books
		$local_books = $this->load->items(['abbreviation' => $translation], $this->table);

		$update = [];
		$insert = [];
		$match = ['key' => 'nr', 'value' => ''];

		// dynamic update all books
		foreach ($books as $book)
		{
			// check if the verse exist
			$match['value'] = (string) $book->nr;
			if (($object = $this->getTarget($match, $local_books)) !== null)
			{
				$book->id = $object->id;
				$book->created = $this->today;
				$update[] = $book;
			}
			else
			{
				$insert[] = $book;
			}
		}

		// check if we have values to insert
		if ($insert !== [])
		{
			 $this->insert->items($insert, $this->table);
		}

		// update the local values
		if ($update !== [] && $this->update->items($update, 'id', $this->table))
		{
			return true;
		}

		return false;
	}
}

