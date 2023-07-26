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


use Joomla\CMS\Date\Date;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Database\Insert;
use VDM\Joomla\GetBible\Database\Update;
use VDM\Joomla\GetBible\Api\Translations;
use VDM\Joomla\GetBible\Api\Books;
use VDM\Joomla\GetBible\Api\Chapters;
use VDM\Joomla\GetBible\Api\Verses;


/**
 * The GetBible Watcher
 * 
 * @since 2.0.1
 */
final class Watcher
{
	/**
	 * The Load class
	 *
	 * @var    Load
	 * @since  2.0.1
	 */
	protected Load $load;

	/**
	 * The Insert class
	 *
	 * @var    Insert
	 * @since  2.0.1
	 */
	protected Insert $insert;

	/**
	 * The Update class
	 *
	 * @var    Update
	 * @since  2.0.1
	 */
	protected Update $update;

	/**
	 * The Translations class
	 *
	 * @var    Translations
	 * @since  2.0.1
	 */
	protected Translations $translations;

	/**
	 * The Books class
	 *
	 * @var    Books
	 * @since  2.0.1
	 */
	protected Books $books;

	/**
	 * The Chapters class
	 *
	 * @var    Chapters
	 * @since  2.0.1
	 */
	protected Chapters $chapters;

	/**
	 * The Verses class
	 *
	 * @var    Verses
	 * @since  2.0.1
	 */
	protected Verses $verses;

	/**
	 * The fresh load switch
	 *
	 * @var    bool
	 * @since  2.0.1
	 */
	protected bool $fresh = false;

	/**
	 * The sql date of today
	 *
	 * @var    string
	 * @since  2.0.1
	 */
	protected string $today;

	/**
	 * The target verse
	 *
	 * @var    object|null
	 * @since  2.0.1
	 */
	protected ?object $verse;

	/**
	 * Constructor
	 *
	 * @param Load           $load            The load object.
	 * @param Insert         $insert          The insert object.
	 * @param Update         $update          The update object.
	 * @param Translations   $translations    The translations API object.
	 * @param Books          $books           The books API object.
	 * @param Chapters       $chapters        The chapters API object.
	 * @param Verses         $verses          The verses API object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Translations $translations,
		Books $books,
		Chapters $chapters,
		Verses $verses)
	{
		$this->load = $load;
		$this->insert = $insert;
		$this->update = $update;
		$this->translations = $translations;
		$this->books = $books;
		$this->chapters = $chapters;
		$this->verses = $verses;

		// just in-case we set a date
		$this->today = (new Date())->toSql();
	}

	/**
	 * The see if new chapters was installed, and therefore new
	 *
	 * @return  bool  true if is new
	 * @since   2.0.1
	 */
	public function isNew(): bool
	{
		return $this->fresh;
	}

	/**
	 * Check if a translation has enough verses
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return bool
	 * @since   2.0.1
	 */
	public function enoughVerses(string $translation = 'kjv'): bool
	{
		$total = $this->totalVerses($translation);

		if ($total < 10000)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get the total number of verses in the database of a given translation
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  int|null
	 * @since   2.0.1
	 */
	public function totalVerses(string $translation = 'kjv'): ?int
	{
		return $this->load->count(
			['abbreviation' => $translation], 'verse'
		);
	}

	/**
	 * Watching that the local Database and API is in sync
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	public function api(string $translation, int $book, int $chapter): bool
	{
		// does the translation exist
		if ($this->translation($translation) === null)
		{
			return false;
		}

		// does the book exist
		if ($this->book($translation, $book) === null)
		{
			return false;
		}

		// does the chapter exist, and get hash value
		if (($hash = $this->chapter($translation, $book, $chapter)) === null)
		{
			return false;
		}

		// load the verses if not found
		if ($this->verses($translation, $book, $chapter))
		{
			if ($this->isNew() || $this->hold())
			{
				return true;
			}

			// get API chapter hash value
			$api_hash = $this->chapters->sha($translation, $book, $chapter);

			// confirm chapter hash has not changed
			if (hash_equals($hash, $api_hash))
			{
				return $this->bump();
			}

			if ($this->update($translation, $book, $chapter))
			{
				// now update the hash of this chapter
				return $this->updateHash($translation, $book, $chapter, $api_hash);
			}
		}

		return false;
	}

	/**
	 * Get the next chapter
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 * @param   bool   $force          The switch to force the getting.
	 *
	 * @return  int|null   Number if there is a next, else null
	 * @since   2.0.1
	 */
	public function getNextChapter(string $translation, int $book, int $chapter, bool $force = false): ?int
	{
		// we load the next chapter
		$next = $chapter + 1;
		// check if this books has this next chapter
		if (($force || !$this->isNew()) && $this->load->value(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $next],
				'sha', 'chapter'
			))
		{
			return $next;
		}

		return null;
	}

	/**
	 * Get the previous chapter
	 *
	 * @param   int     $chapter      The chapter number.
	 * @param   bool   $force          The switch to force the getting.
	 *
	 * @return  int|null   Number if there is a previous, else null
	 * @since   2.0.1
	 */
	public function getPreviousChapter(int $chapter, bool $force = false): ?int
	{
		// we load the previous chapter
		$previous = $chapter - 1;
		if (($force || !$this->isNew()) && $previous > 0)
		{
			return $previous;
		}

		return null;
	}

	/**
	 * Get the last chapter of a book
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book      The book number.
	 *
	 * @return  int|null   Number if there is a previous, else null
	 * @since   2.0.1
	 */
	public function getLastChapter(string $translation, int $book): ?int
	{
		// we load the last chapter
		return $this->load->max(
			['abbreviation' => $translation, 'book_nr' => $book],
			'chapter', 'chapter'
		);
	}

	/**
	 * Get the next book
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $try            The number of tries
	 *
	 * @return  int|null   Number if there is a next, else null
	 * @since   2.0.1
	 */
	public function getNextBook(string $translation, int $book, int $try = 0): ?int
	{
		// we load the next chapter
		$next = $book + 1;

		// if we already looked over 90
		if ($next >= 90)
		{
			$next = 1;
		}

		// check if this book exist
		if ($this->load->value(
				['abbreviation' => $translation, 'nr' => $next],
				'sha', 'book'
			))
		{
			return $next;
		}

		$try++;

		// could not be found :(
		if ($try >= 180)
		{
			return null;
		}

		return $this->getNextBook($translation, $next, $try);
	}

	/**
	 * Get the previous book
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $try            The number of tries
	 *
	 * @return  int|null   Number if there is a previous, else null
	 * @since   2.0.1
	 */
	public function getPreviousBook(string $translation, int $book, int $try = 0): ?int
	{
		// we load the previous book
		$previous = $book - 1;

		// if we already looked over 90
		if ($previous <= 0)
		{
			$previous = 90;
		}

		// check if this book exist
		if ($this->load->value(
				['abbreviation' => $translation, 'nr' => $previous],
				'sha', 'book'
			))
		{
			return $previous;
		}

		$try++;

		// could not be found :(
		if ($try >= 180)
		{
			return null;
		}

		return $this->getPreviousBook($translation, $previous, $try);
	}

	/**
	 * Get Translation Hash Value
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  string|null   The sha of the translation
	 * @since   2.0.1
	 */
	private function translation(string $translation): ?string
	{
		// check local value
		if (($translation_sha = $this->load->value(['abbreviation' => $translation], 'sha', 'translation')) !== null)
		{
			return $translation_sha;
		}

		// get all the translations
		$translations = $this->translations->list();

		// check return data
		if (!isset($translations->{$translation}) || !isset($translations->{$translation}->sha))
		{
			return null;
		}

		// add them to the database
		$this->insert->items((array) $translations, 'translation');

		return $translations->{$translation}->sha;
	}

	/**
	 * Get Book Hash Value
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 *
	 * @return  string|null   The sha of the book
	 * @since   2.0.1
	 */
	private function book(string $translation, int $book): ?string
	{
		// check local value
		if (($book_sha = $this->load->value(['abbreviation' => $translation, 'nr' => $book], 'sha', 'book')) !== null)
		{
			return $book_sha;
		}

		// get all the books
		$books = $this->books->list($translation);

		// check return data
		if (!isset($books->{$book}) || !isset($books->{$book}->sha))
		{
			return null;
		}

		// add them to the database
		$this->insert->items((array) $books, 'book');

		return $books->{$book}->sha;
	}

	/**
	 * Get Chapter Hash Value
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  string|null   The sha of the chapter
	 * @since   2.0.1
	 */
	private function chapter(string $translation, int $book, int $chapter): ?string
	{
		// check local value
		if (($chapter_sha = $this->load->value(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter],
				'sha', 'chapter'
			)) !== null)
		{
			return $chapter_sha;
		}

		// get all the books
		$chapters = $this->chapters->list($translation, $book);

		// check return data
		if (!isset($chapters->{$chapter}) || !isset($chapters->{$chapter}->sha))
		{
			return null;
		}

		// add them to the database
		$this->insert->items((array) $chapters, 'chapter');

		return $chapters->{$chapter}->sha;
	}

	/**
	 * Load verses if not in local database
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  bool    True if in local database
	 * @since   2.0.1
	 */
	private function verses(string $translation, int $book, int $chapter): bool
	{
		// check local value
		if (($this->verse = $this->load->item(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter, 'verse' => 1],
				'verse'
			)) !== null)
		{
			return true;
		}

		// get all the verses
		if (($verses = $this->verses->get($translation, $book, $chapter)) === null)
		{
			return false;
		}

		// dynamic update all verse objects
		$insert = ['book_nr' => $book, 'abbreviation' => $translation];
		array_walk($verses->verses, function ($item, $key) use ($insert) {
			foreach ($insert as $k => $v) { $item->$k = $v; }
		});

		$this->fresh = true;

		// add them to the database
		return $this->insert->items((array) $verses->verses, 'verse');
	}

	/**
	 * Trigger the update of the verses of a translation-book-chapter
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  bool    True if update was a success
	 * @since   2.0.1
	 */
	private function update(string $translation, int $book, int $chapter): bool
	{
		// load all the verses from the local database
		if (($verses = $this->load->items(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter],
				'verse'
			)) !== null)
		{
			// get verses from the API
			if (($api = $this->verses->get($translation, $book, $chapter)) === null)
			{
				return false;
			}

			$update = [];
			$insert = [];

			// dynamic update all verse objects
			foreach ($api->verses as $verse)
			{
				// check if the verse exist
				if (($object = $this->getVerse((int) $verse->verse, $verses)) !== null)
				{
					$verse->id = $object->id;
					$verse->created = $this->today;
					$update[] = $verse;
				}
				else
				{
					$insert[] = $verse;
				}
			}

			// check if we have values to insert
			if ($insert !== [])
			{
				 $this->insert->items($insert, 'verse');
			}

			// update the local verses
			if ($update !== [] && $this->update->items($update, 'id', 'verse'))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a verse text from the API array of verses
	 *
	 * @param   int     $number     The verse number.
	 * @param   array   $verses     The api verses
	 *
	 * @return  object|null   The verse string
	 * @since   2.0.1
	 */
	private function getVerse(int $number, array &$verses): ?object
	{
		foreach ($verses as $verse)
		{
			if ($verse->verse === $number)
			{
				return $verse;
			}
		}

		return null;
	}

	/**
	 * Trigger the update of a chapter hash value
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 * @param   string  $hash         The API chapter hash value.
	 *
	 * @return  bool    True if update was a success
	 * @since   2.0.1
	 */
	private function updateHash(string $translation, int $book, int $chapter, string $hash): bool
	{
		// load the chapter
		if (($item = $this->load->item(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter],
				'chapter'
			)) !== null)
		{
			$update = [];
			$update['id'] = $item->id;
			$update['sha'] = $hash;
			$update['created'] = $this->today;

			// update the local chapter
			return $this->update->row($update, 'id', 'chapter');
		}

		return false;
	}

	/**
	 * Check if its time to match the API hash
	 *
	 * @return  bool    false if its time to check for an update
	 * @since   2.0.1
	 */
	private function hold(): bool
	{
		// Create DateTime objects from the strings
		try {
			$today = new \DateTime($this->today);
			$created = new \DateTime($this->verse->created);
		} catch (\Exception $e) {
			return false;
		}

		// Calculate the difference
		$interval = $today->diff($created);

		// Check if the interval is more than 1 month
		if ($interval->m >= 1 || $interval->y >= 1)
		{
			return false; // More than a month, it's time to check for an update
		}
		else
		{
			return true; // Within the last month, hold off on the update check
		}
	}

	/**
	 * Bump the checking time
	 *
	 * @return  bool    true when the update was a success
	 * @since   2.0.1
	 */
	private function bump(): bool
	{
		$update = [];
		$update['id'] = $this->verse->id;
		$update['created'] = $this->today;

		// update the local verse
		return $this->update->row($update, 'id', 'verse');
	}
}

