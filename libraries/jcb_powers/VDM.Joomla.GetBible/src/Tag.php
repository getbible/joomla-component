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
 * The GetBible Tag
 * 
 * @since 2.0.1
 */
final class Tag
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
	 * Set a tag
	 *
	 * @param   string     $name          The tag name being created
	 *
	 * @return  array|null   Array of the tag values on success
	 * @since 2.0.1
	 **/
	public function set(string $name): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSE_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// get tag if it exist
		if (($tag = $this->get($linker, $name)) !== null)
		{
			// publish if not published
			if ($tag->published != 1 && !$this->update->value(1, 'published', $tag->id, 'id', 'tag'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_TAG_ALREADY_EXIST_BUT_COULD_NOT_BE_REACTIVATED')
				];
			}

			$tag->published = 1;
			$tag->success = Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_SET');
			return (array) $tag;
		}
		// create a new tag
		elseif ($this->create($linker, $name)
			&& ($tag = $this->get($linker, $name)) !== null)
		{
			$tag->success = Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_SET');
			return (array) $tag;
		}

		return null;
	}

	/**
	 * Delete a tag
	 *
	 * @param   string     $tag        The tagged verse GUID value
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	public function delete(string $tag): bool
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return false;
		}

		// make sure the linker has access to delete this tag
		if (($id = $this->load->value(['guid' => $tag, 'linker' => $linker], 'id', 'tag')) !== null && $id > 0)
		{
			return $this->update->value(-2, 'published', $id, 'id', 'tag');
		}

		return false;
	}

	/**
	 * Get a tag
	 *
	 * @param   string     $linker        The linker GUID value
	 * @param   string     $name          The tag name
	 *
	 * @return  array|null   Array of the tagged verse values on success
	 * @since 2.0.1
	 **/
	private function get(
		string $linker,
		string $name
	): ?array
	{
		// get tag if it exist
		if (($tag = $this->load->item([
				'linker' => $linker,
				'name' => $name
			], 'tag')) !== null)
		{
			return $tag;
		}

		return null;
	}

	/**
	 * Create a Tag
	 *
	 * @param   string     $linker        The linker GUID value
	 * @param   string     $name          The tag name
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function create(
		string $linker,
		string $name
	): bool
	{
		$guid = (string) GuidHelper::get();
		while (!GuidHelper::valid($guid, 'tag', 0, 'getbible'))
		{
			// must always be set
			$guid = (string) GuidHelper::get();
		}

		return $this->insert->row([
			'access' => 0,
			'linker' => $linker,
			'name' => $name,
			'guid' => $guid
		], 'tag');
	}
}

