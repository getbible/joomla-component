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


use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Watcher\Translation;
use VDM\Joomla\GetBible\Watcher\Book;
use VDM\Joomla\GetBible\Watcher\Chapter;


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
	 * The Translation class
	 *
	 * @var    Translation
	 * @since  2.0.1
	 */
	protected Translation $translation;

	/**
	 * The Book class
	 *
	 * @var    Book
	 * @since  2.0.1
	 */
	protected Book $book;

	/**
	 * The Chapter class
	 *
	 * @var    Chapter
	 * @since  2.0.1
	 */
	protected Chapter $chapter;

	/**
	 * The fresh load switch
	 *
	 * @var    bool
	 * @since  2.0.1
	 */
	protected bool $fresh = false;

	/**
	 * Constructor
	 *
	 * @param Load          $load           The load object.
	 * @param Translation   $translation    The translations API object.
	 * @param Book          $book           The books API object.
	 * @param Chapter       $chapter        The chapters API object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Translation $translation,
		Book $book,
		Chapter $chapter)
	{
		$this->load = $load;
		$this->translation = $translation;
		$this->book = $book;
		$this->chapter = $chapter;
	}

	/**
	 * Watching that the local Database and API is in sync
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 * @param   bool    $force        The switch to force an update.
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	public function sync(string $translation, int $book, int $chapter, bool $force = false): bool
	{
		// set the update state
		$this->forceUpdate($force);

		// is the translation details in sync
		if (!$this->translation->sync($translation))
		{
			return false;
		}

		// is the book details in sync
		if (!$this->book->sync($translation, $book))
		{
			return false;
		}

		// is the chapter and its verses in sync
		if (!$this->chapter->sync($translation, $book, $chapter))
		{
			return false;
		}

		// if any is new, then this is class should not load any more stuff
		$this->fresh = ($this->translation->isNew() || $this->book->isNew() || $this->chapter->isNew());

		return true;
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
	 * The method to set the update state
	 *
	 * @param   bool    $state   The switch to force an update.
	 *
	 * @return  void
	 * @since   2.0.1
	 */
	private function forceUpdate(bool $state): void
	{
		// force all updates
		if ($state)
		{
			$this->translation->forceUpdate();
			$this->book->forceUpdate();
			$this->chapter->forceUpdate();
		}
	}
}

