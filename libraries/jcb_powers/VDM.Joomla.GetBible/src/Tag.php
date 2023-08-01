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
	 * Create a tag
	 *
	 * @param   string         $name           The tag name being created
	 * @param   string|null    $description    The tag description being created
	 *
	 * @return  array|null   Array of the tag values on success
	 * @since 2.0.1
	 **/
	public function create(string $name, ?string $description): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSEBR_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// get tag if it exist
		$name = trim($name);
		if (($tag = $this->get($linker, $name)) !== null)
		{
			// publish if not published
			if ($tag->published != 1 && !$this->update->value(1, 'published', $tag->id, 'id', 'tag'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_TAG_ALREADY_EXIST_BUT_COULD_NOT_BE_REACTIVATED')
				];
			}

			// update the description if it does not match
			$description = $description ?? '';
			$description = trim($description);
			if ($tag->description !== $description && $this->update->value($description, 'description', $tag->id, 'id', 'tag'))
			{
				$tag->description = $description;
			}

			$tag->published = 1;
			$tag->success = Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_REACTIVATED');
			return (array) $tag;
		}
		// create a new tag
		elseif (strlen($name) >= 2 && $this->createTag($linker, $name, $description)
			&& ($tag = $this->get($linker, $name)) !== null)
		{
			$tag->success = Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_CREATED');
			return (array) $tag;
		}

		return null;
	}

	/**
	 * Update a tag
	 *
	 * @param   string         $tag            The tag GUID value
	 * @param   string         $name           The tag name being created
	 * @param   string|null    $description    The tag description being created
	 *
	 * @return  array|null   Array of the tag values on success
	 * @since 2.0.1
	 **/
	public function update(string $tag, string $name, ?string $description): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSEBR_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// get tag if it exist
		$name = trim($name);
		$tag = trim($tag);
		if (($_tag = $this->load->item(['linker' => $linker, 'guid' => $tag], 'tag')) !== null)
		{
			// publish if not published
			if ($_tag->published != 1 && !$this->update->value(1, 'published', $_tag->id, 'id', 'tag'))
			{
				return [
					'error' => Text::_('COM_GETBIBLE_TAG_FOUND_BUT_COULD_NOT_BE_REACTIVATED')
				];
			}

			// update the description if it does not match
			$description = $description ?? '';
			$description = trim($description);
			if ($_tag->description !== $description && $this->update->value($description, 'description', $_tag->id, 'id', 'tag'))
			{
				$_tag->description = $description;
			}

			// update the name if it does not match
			if (strlen($name) >= 2 && $_tag->name !== $name && $this->update->value($name, 'name', $_tag->id, 'id', 'tag'))
			{
				$_tag->name = $name;
			}

			$_tag->published = 1;
			$_tag->success = Text::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_UPDATED');
			return (array) $_tag;
		}
		//elseif (($_tag = $this->load->item(['guid' => $tag], 'tag')) !== null)
		//{
		// we may need to add this	
		//}

		return [
			'error' => Text::_("COM_GETBIBLE_THIS_TAG_DOESNT_BELONG_TO_YOU_THUS_YOU_CANNOT_EDIT_IT")
		];
	}

	/**
	 * Delete a tag
	 *
	 * @param   string     $tag        The tagged verse GUID value
	 *
	 * @return  array|null   Array of the message on success
	 * @since 2.0.1
	 **/
	public function delete(string $tag): ?array
	{
		// make sure the linker has access
		if (($linker = $this->linker->get()) === null)
		{
			return [
				'error' => Text::_("COM_GETBIBLE_WITHOUT_SELECTING_THE_CORRECT_FAVOURITE_VERSEBR_YOU_CANT_PERFORM_THE_INITIAL_ACTION"),
				'access_required' => true
			];
		}

		// make sure the linker has access to delete this tag
		if (($id = $this->load->value(['guid' => $tag, 'linker' => $linker], 'id', 'tag')) !== null && $id > 0
			&& $this->update->value(-2, 'published', $id, 'id', 'tag'))
		{
			return [
				'success' => Text::_('COM_GETBIBLE_TAG_SUCCESSFULLY_DELETED')
			];
		}

		return [
			'error' => Text::_("COM_GETBIBLE_THIS_TAG_DOESNT_BELONG_TO_YOU_THUS_YOU_CANNOT_DELETE_IT")
		];
	}

	/**
	 * Get a tag
	 *
	 * @param   string         $linker         The linker GUID value
	 * @param   string         $name           The tag name
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
	 * @param   string         $linker         The linker GUID value
	 * @param   string         $name           The tag name
	 * @param   string|null    $description    The tag description being created
	 *
	 * @return  bool   True on success
	 * @since 2.0.1
	 **/
	private function createTag(
		string $linker,
		string $name,
		?string $description
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
			'description' => $description ?? '',
			'guid' => $guid
		], 'tag');
	}
}

