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
use VDM\Joomla\GetBible\Api\Translations;
use VDM\Joomla\GetBible\Abstraction\Watcher;


/**
 * The GetBible Translation Watcher
 * 
 * @since 2.0.1
 */
final class Translation extends Watcher
{
	/**
	 * The Translations class
	 *
	 * @var    Translations
	 * @since  2.0.1
	 */
	protected Translations $translations;

	/**
	 * Constructor
	 *
	 * @param Load           $load            The load object.
	 * @param Insert         $insert          The insert object.
	 * @param Update         $update          The update object.
	 * @param Translations   $translations    The translations API object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update,
		Translations $translations)
	{
		// load the parent constructor
		parent::__construct($load, $insert, $update);

		$this->translations = $translations;

		// set the table
		$this->table = 'translation';
	}

	/**
	 * Update translations details
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	public function translations(): bool
	{
		return $this->update();
	}

	/**
	 * Sync the target being watched
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	public function sync(string $translation): bool
	{
		// load the target if not found
		if ($this->load($translation))
		{
			if ($this->isNew() || $this->hold())
			{
				return true;
			}

			try
			{
				// get API hash value
				$hash = $this->translations->sha($translation);
			}
			catch (\Exception $e)
			{
				return false;
			}

			// confirm hash has not changed
			if (hash_equals($hash, $this->target->sha))
			{
				return $this->bump();
			}

			if ($this->update())
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Load Translation
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  bool   True if translation found
	 * @since   2.0.1
	 */
	private function load(string $translation): bool
	{
		// check local value
		if (($this->target = $this->load->item(['abbreviation' => $translation], $this->table)) !== null)
		{
			return true;
		}

		try
		{
			// get all the translations
			$translations = $this->translations->list();
		}
		catch (\Exception $e)
		{
			return false;
		}

		// check return data
		if (!isset($translations->{$translation}) || !isset($translations->{$translation}->sha))
		{
			return false;
		}

		// add them to the database
		$this->insert->items((array) $translations, 'translation');

		if (($this->target = $this->load->item(['abbreviation' => $translation], $this->table)) !== null)
		{
			$this->fresh = true;
		}

		return $this->fresh;
	}

	/**
	 * Trigger the update of all translations
	 *
	 * @return  bool    True if update was a success
	 * @since   2.0.1
	 */
	private function update(): bool
	{
		try
		{
			// get translations from the API
			if (($translations = $this->translations->list()) === null)
			{
				return false;
			}
		}
		catch (\Exception $e)
		{
			return false;
		}

		// get the local published translations
		$local_translations = $this->load->items(['published' => 1], $this->table);

		$update = [];
		$insert = [];
		$match = ['key' => 'abbreviation', 'value' => ''];

		// dynamic update all translations
		foreach ($translations as $translation)
		{
			// check if the verse exist
			$match['value'] = $translation->abbreviation ?? null;
			if ($local_translations !== null && ($object = $this->getTarget($match, $local_translations)) !== null)
			{
				$translation->id = $object->id;
				$translation->created = $this->today;
				$update[] = $translation;
			}
			else
			{
				$insert[] = $translation;
			}
		}

		// check if we have values to insert
		$inserted = false;
		if ($insert !== [])
		{
			$inserted = $this->insert->items($insert, $this->table);
		}

		// update the local values
		if ($update !== [] && $this->update->items($update, 'id', $this->table))
		{
			return true;
		}

		return $inserted;
	}
}

