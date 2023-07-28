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


use Joomla\CMS\Language\Text;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Database\Insert;
use VDM\Joomla\GetBible\Database\Update;
use VDM\Joomla\GetBible\Linker;
use VDM\Joomla\Utilities\GuidHelper;


/**
 * The GetBible Note
 * 
 * @since 2.0.1
 */
final class Note
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
	 * The Linker class
	 *
	 * @var    Linker
	 * @since  2.0.1
	 */
	protected Linker $linker;

	/**
	 * Constructor
	 *
	 * @param Load         $load          The load object.
	 * @param Insert       $insert        The insert object.
	 * @param Update       $update        The update object.
	 * @param Linker       $linker        The linker object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Linker $linker)
	{
		$this->load = $load;
		$this->insert = $insert;
		$this->update = $update;
		$this->linker = $linker;
	}

	/**
	 * Set a note
	 *
	 * @param   int          $book     The book in which the note is made
	 * @param   int          $chapter  The chapter in which the note is made
	 * @param   int          $verse    The verse where the note is made
	 * @param   string|null  $note     The note being made
	 *
	 * @return  array|null   Array of the note values on success
	 * @since 2.0.1
	 **/
	public function set(
		int $book,
		int $chapter,
		int $verse,
		?string $note
	): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSEBR_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// get note if it exists
		if (($_note = $this->get($linker, $book, $chapter, $verse)) !== null)
		{
			// publish if not published
			if ($_note->published != 1 && !$this->update->value(1, 'published', $_note->id, 'id', 'note'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_NOTE_ALREADY_EXIST_BUT_COULD_NOT_BE_REACTIVATED')
				];
			}

			// update the note if needed
			if ((!isset($_note->note) || $_note->note !== $note) && !$this->update->value($note, 'note', $_note->id, 'id', 'note'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_NOTE_ALREADY_EXIST_BUT_COULD_NOT_UPDATE_THE_NOTE_TEXT')
				];
			}

			$_note->published = 1;
			$_note->note = $note;
			$_note->success = Text::_('COM_GETBIBLE_THE_NOTE_WAS_SUCCESSFULLY_UPDATED');
			return (array) $_note;
		}
		// create a new tag
		elseif ($this->create($linker, $book, $chapter, $verse, $note)
			&& ($_note = $this->get($linker, $book, $chapter, $verse)) !== null)
		{
			$_note->success = Text::_('COM_GETBIBLE_THE_NOTE_WAS_SUCCESSFULLY_CREATED');
			return (array) $_note;
		}

		return null;
	}

	/**
	 * Delete a note
	 *
	 * @param   string     $note     The note GUID value
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	public function delete(string $note): bool
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return false;
		}

		// make sure the linker has access to delete this tag
		if (($id = $this->load->value(['guid' => $note, 'linker' => $linker], 'id', 'note')) !== null && $id > 0)
		{
			return $this->update->value(-2, 'published', $id, 'id', 'note');
		}

		return false;
	}

	/**
	 * Get a note
	 *
	 * @param   string     $linker        The linker GUID value
	 * @param   int        $book          The book in which the note is made
	 * @param   int        $chapter       The chapter in which the note is made
	 * @param   int        $verse         The verse where the note is made
	 *
	 * @return  object|null   Object of the note values on success
	 * @since 2.0.1
	 **/
	private function get(
		string $linker,
		int $book,
		int $chapter,
		int $verse
	): ?object
	{
		// get note if it exist
		if (($note = $this->load->item([
				'linker' => $linker,
				'book_nr' => $book,
				'chapter' => $chapter,
				'verse' => $verse
			], 'note')) !== null)
		{
			return $note;
		}

		return null;
	}

	/**
	 * Create a note
	 *
	 * @param   string        $linker        The linker GUID value
	 * @param   int           $book          The book in which the note is made
	 * @param   int           $chapter       The chapter in which the note is made
	 * @param   int           $verse         The verse where the note is made
	 * @param   string|null   $note          The note being created (allow empty notes)
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function create(
		string $linker,
		int $book,
		int $chapter,
		int $verse,
		?string $note
	): bool
	{
		$guid = (string) GuidHelper::get();
		while (!GuidHelper::valid($guid, 'note', 0, 'getbible'))
		{
			// must always be set
			$guid = (string) GuidHelper::get();
		}

		return $this->insert->row([
			'note' => $note ?? '',
			'access' => 0,
			'linker' => $linker,
			'book_nr' => $book,
			'chapter' => $chapter,
			'verse' => $verse,
			'guid' => $guid
		], 'note');
	}
}

