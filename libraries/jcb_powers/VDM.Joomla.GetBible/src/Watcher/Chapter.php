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
use VDM\Joomla\GetBible\Api\Chapters;
use VDM\Joomla\GetBible\Api\Verses;
use VDM\Joomla\GetBible\Abstraction\Watcher;


/**
 * The GetBible Chapter Watcher
 * 
 * @since 2.0.1
 */
final class Chapter extends Watcher
{
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
	 * Constructor
	 *
	 * @param Load           $load        The load object.
	 * @param Insert         $insert      The insert object.
	 * @param Update         $update      The update object.
	 * @param Chapters       $chapters        The chapters API object.
	 * @param Verses         $verses          The verses API object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Chapters $chapters,
		Verses $verses)
	{
		// load the parent constructor
		parent::__construct($load, $insert, $update);

		$this->chapters = $chapters;
		$this->verses = $verses;

		// set the table
		$this->table = 'chapter';
	}

	/**
	 * Sync the target being watched
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	public function sync(string $translation, int $book, int $chapter): bool
	{
		// load the target if not found
		if ($this->chapter($translation, $book, $chapter)
			&& $this->verses($translation, $book, $chapter))
		{
			if ($this->isNew() || $this->hold())
			{
				return true;
			}

			// get API hash value
			$hash = $this->chapters->sha($translation, $book, $chapter);

			// confirm hash has not changed
			if (hash_equals($hash, $this->target->sha))
			{
				return $this->bump();
			}

			if ($this->update($translation, $book, $chapter)
				&& $this->hash($hash))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Load the chapter numbers
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	private function chapter(string $translation, int $book, int $chapter): bool
	{
		// check local value
		if (($this->target = $this->load->item(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter],
				'chapter'
			)) !== null)
		{
			return true;
		}

		// get all the books
		$chapters = $this->chapters->list($translation, $book);

		// check return data
		if (!isset($chapters->{$chapter}) || !isset($chapters->{$chapter}->sha))
		{
			return false;
		}

		// add them to the database
		$this->insert->items((array) $chapters, 'chapter');

		// check local value
		if (($this->target = $this->load->item(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter],
				'chapter'
			)) !== null)
		{
			return true;
		}

		return false;
	}

	/**
	 * Load verses
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
		if (($text = $this->load->value(
				['abbreviation' => $translation, 'book_nr' => $book, 'chapter' => $chapter, 'verse' => 1],
				'text', 'verse'
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

		// add them to the database
		$this->fresh = $this->insert->items((array) $verses->verses, 'verse');

		return true;
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
			$match = ['key' => 'verse', 'value' => ''];

			// dynamic update all verse objects
			foreach ($api->verses as $verse)
			{
				// check if the verse exist
				$match['value'] = (string) $verse->verse;
				if (($object = $this->getTarget($match, $verses)) !== null)
				{
					$verse->id = $object->id;
					$verse->modified = $this->today;
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
	 * Trigger the update of a chapter hash value
	 *
	 * @param   string  $hash   The API chapter hash value.
	 *
	 * @return  bool    True if update was a success
	 * @since   2.0.1
	 */
	private function hash(string $hash): bool
	{
		// load the chapter
		$update = [];
		$update['id'] = $this->target->id;
		$update['sha'] = $hash;
		$update['created'] = $this->today;

		// update the local chapter
		return $this->update->row($update, 'id', 'chapter');
	}
}

