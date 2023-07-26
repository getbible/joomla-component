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


use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Database\Insert;
use VDM\Joomla\GetBible\Database\Update;
use VDM\Joomla\GetBible\Linker;
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\Utilities\Component\Helper;


/**
 * The GetBible Tagged
 * 
 * @since 2.0.1
 */
final class Tagged
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
	 * The Registry class
	 *
	 * @var    Registry
	 * @since  2.0.1
	 */
	protected Registry $params;

	/**
	 * Application object.
	 *
	 * @var    CMSApplication
	 * @since 3.2.0
	 **/
	protected CMSApplication $app;

	/**
	 * Constructor
	 *
	 * @param Load                 $load        The load object.
	 * @param Insert               $insert      The insert object.
	 * @param Update               $update      The update object.
	 * @param Linker               $linker      The linker object.
	 * @param Registry|null        $params      The params object.
	 * @param CMSApplication|null  $app         The app object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Linker $linker,
		?Registry $params = null,
		?CMSApplication $app = null)
	{
		$this->load = $load;
		$this->insert = $insert;
		$this->update = $update;
		$this->linker = $linker;
		$this->params = $params ?: Helper::getParams('com_getbible');
		$this->app = $app ?: Factory::getApplication();
	}

	/**
	 * Set a tagged verse
	 *
	 * @param   string     $translation   The translation in which the note is made
	 * @param   int        $book          The book in which the note is made
	 * @param   int        $chapter       The chapter in which the note is made
	 * @param   int        $verse         The verse where the note is made
	 * @param   string     $tag           The tag being added
	 *
	 * @return  array|null   Array of the tagged values on success
	 * @since 2.0.1
	 **/
	public function set(
		string $translation,
		int $book,
		int $chapter,
		int $verse,
		string $tag
	): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSE_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// make sure the tag is active
		if (($published = GuidHelper::item($tag, 'tag', 'a.published', 'getbible')) === null
			|| $published != 1)
		{
			return [
				'error' => Text::_('COM_GETBIBLE_THE_TAG_SELECTED_IS_NOT_ACTIVE_PLEASE_SELECT_AN_ACTIVE_TAG')
			];
		}

		// get tagged verse if it exist
		if (($tagged = $this->get($linker, $book, $chapter, $verse, $tag)) !== null)
		{
			// publish if not published
			if ($tagged->published != 1 && !$this->update->value(1, 'published', $tagged->id, 'id', 'tagged_verse'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_TAGGED_VERSE_ALREADY_EXIST_BUT_COULD_NOT_BE_REACTIVATED')
				];
			}

			$tagged->published = 1;
			$tagged->success = Text::_('COM_GETBIBLE_THE_VERSE_WAS_SUCCESSFULLY_TAGGED');
			return (array) $tagged;
		}
		// create a new tagged verse
		elseif ($this->create($linker, $translation, $book, $chapter, $verse, $tag)
			&& ($tagged = $this->get($linker, $book, $chapter, $verse, $tag)) !== null)
		{
			$tagged->success = Text::_('COM_GETBIBLE_THE_VERSE_WAS_SUCCESSFULLY_TAGGED');
			return (array) $tagged;
		}

		return null;
	}

	/**
	 * Delete a tagged verse
	 *
	 * @param   string    $tagged     The tagged verse GUID value
	 *
	 * @return  array|null   True on success
	 * @since 2.0.1
	 **/
	public function delete(
		string $tagged
	): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSE_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// make sure the linker has access to delete this tag
		if (($id = $this->load->value(['linker' => $linker, 'guid' => $tagged], 'id', 'tagged_verse')) !== null && $id > 0
			&& $this->update->value(-2, 'published', $id, 'id', 'tagged_verse'))
		{
			return [
				'success' => Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_REMOVED_FROM_THE_VERSE')
			];
		}

		// lets check if this is a systems tag
		if (($_tagged = $this->getByGuid($tagged)) !== null && $_tagged->access == 1)
		{
			if ($this->params->get('allow_untagging') == 1
				&& $this->createForLinker($linker, $_tagged, -2))
			{
				return [
					'success' => Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_REMOVED_FROM_THE_VERSE')
				];
			}

			return [
				'notice' => Text::sprintf("COM_GETBIBLE_THIS_IS_A_GLOBAL_TAG_SET_BY_US_AT_BSB_FOR_YOUR_CONVENIENCE_WE_HOLD_THE_PRIVILEGE_TO_MODIFY_THESE_TAGS_IF_YOU_BELIEVE_ITS_SET_IN_ERROR_KINDLY_INFORM_US", $this->app->get('sitename'))
			];
		}

		return [
			'error' => Text::_('COM_GETBIBLE_THIS_TAG_COULD_NOT_BE_REMOVED')
		];
	}

	/**
	 * Get a tagged verse
	 *
	 * @param   string    $guid    The tagged verse GUID value
	 *
	 * @return  object|null   Object of the tagged verse values on success
	 * @since 2.0.1
	 **/
	private function getByGuid(string $guid): ?object
	{
		// get tagged verse if it exist
		if (($tagged = $this->load->item(['guid' => $guid], 'tagged_verse')) !== null)
		{
			return $tagged;
		}

		return null;
	}

	/**
	 * Get a tagged verse
	 *
	 * @param   string     $linker        The linker GUID value
	 * @param   int        $book          The book in which the note is made
	 * @param   int        $chapter       The chapter in which the note is made
	 * @param   int        $verse         The verse where the note is made
	 * @param   string     $tag           The tag being added
	 *
	 * @return  object|null   Object of the tagged verse values on success
	 * @since 2.0.1
	 **/
	private function get(
		string $linker,
		int $book,
		int $chapter,
		int $verse,
		string $tag
	): ?object
	{
		// get tagged verse if it exist
		if (($tagged = $this->load->item([
				'linker' => $linker,
				'book_nr' => $book,
				'chapter' => $chapter,
				'verse' => $verse,
				'tag' => $tag
			], 'tagged_verse')) !== null)
		{
			$tagged->name = $this->load->value([
				'guid' => $tag
			], 'name', 'tag');

			$tagged->description = $this->load->value([
				'guid' => $tag
			], 'description', 'tag');

			return $tagged;
		}

		return null;
	}

	/**
	 * Create a Tagged verse
	 *
	 * @param   string     $linker        The linker GUID value
	 * @param   string     $translation   The translation in which the note is made
	 * @param   int        $book          The book in which the note is made
	 * @param   int        $chapter       The chapter in which the note is made
	 * @param   int        $verse         The verse where the note is made
	 * @param   string     $tag           The tag being added
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function create(
		string $linker,
		string $translation,
		int $book,
		int $chapter,
		int $verse,
		string $tag
	): bool
	{
		$guid = (string) GuidHelper::get();
		while (!GuidHelper::valid($guid, 'tagged_verse', 0, 'getbible'))
		{
			// must always be set
			$guid = (string) GuidHelper::get();
		}

		return $this->insert->row([
			'tag' => $tag,
			'access' => 0,
			'linker' => $linker,
			'abbreviation' => $translation,
			'book_nr' => $book,
			'chapter' => $chapter,
			'verse' => $verse,
			'guid' => $guid
		], 'tagged_verse');
	}

	/**
	 * Create a Tagged verse for a linker using a system tagged verse
	 *
	 * @param   string     $linker        The linker GUID value
	 * @param   object     $tagged        The system tagged verse
	 * @param   int        $published     The new tagged verse state [default trashed = -2]
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function createForLinker(
		string $linker,
		object $tagged,
		int $published = -2
	): bool
	{
		$guid = (string) GuidHelper::get();
		while (!GuidHelper::valid($guid, 'tagged_verse', 0, 'getbible'))
		{
			// must always be set
			$guid = (string) GuidHelper::get();
		}

		return $this->insert->row([
			'tag' => $tagged->tag,
			'access' => 0,
			'linker' => $linker,
			'abbreviation' => $tagged->abbreviation,
			'book_nr' => $tagged->book_nr,
			'chapter' => $tagged->chapter,
			'verse' => $tagged->verse,
			'guid' => $guid,
			'published' => $published
		], 'tagged_verse');
	}
}

