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

namespace TrueChristianBible\Joomla\GetBible\Abstraction;


use Joomla\CMS\Date\Date;
use TrueChristianBible\Joomla\GetBible\Database\Load;
use TrueChristianBible\Joomla\GetBible\Database\Insert;
use TrueChristianBible\Joomla\GetBible\Database\Update;


/**
 * The GetBible Watcher
 * 
 * @since 2.0.1
 */
abstract class Watcher
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
	 * The fresh load switch
	 *
	 * @var    bool
	 * @since  2.0.1
	 */
	protected bool $fresh = false;

	/**
	 * The force update
	 *
	 * @var    bool
	 * @since  2.0.1
	 */
	protected bool $force = false;

	/**
	 * The target
	 *
	 * @var    object|null
	 * @since  2.0.1
	 */
	protected ?object $target;

	/**
	 * The today's date
	 *
	 * @var    string
	 * @since  2.0.1
	 */
	protected string $today;

	/**
	 * The two months in the past date
	 *
	 * @var    string
	 * @since  2.0.1
	 */
	protected string $past;

	/**
	 * The target table
	 *
	 * @var    string
	 * @since  2.0.1
	 */
	protected string $table;

	/**
	 * Constructor
	 *
	 * @param Load           $load            The load object.
	 * @param Insert         $insert          The insert object.
	 * @param Update         $update          The update object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Insert $insert,
		Update $update)
	{
		$this->load = $load;
		$this->insert = $insert;
		$this->update = $update;

		// just in-case we set some dates
		$this->today = (new Date())->toSql();
		$this->past = (new Date())->modify('-2 months')->toSql();

	}

	/**
	 * The switch to force a local update
	 *
	 * @return  void
	 * @since   2.0.1
	 */
	public function forceUpdate(): void
	{
		$this->force = true;
	}

	/**
	 * The see if new target is newly installed
	 *
	 * @return  bool  true if is new
	 * @since   2.0.1
	 */
	public function isNew(): bool
	{
		return $this->fresh;
	}

	/**
	 * Check if its time to match the API hash
	 *
	 * @return  bool    false if its time to check for an update
	 * @since   2.0.1
	 */
	protected function hold(): bool
	{
		// if we are being forced
		if ($this->force)
		{
			return false; // we no longer hold, but force an update
		}

		// Create DateTime objects from the strings
		try {
			$today = new \DateTime($this->today);
			$created = new \DateTime($this->target->created);
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
	protected function bump(): bool
	{
		$update = [];
		$update['id'] = $this->target->id;
		$update['created'] = $this->today;

		// update the local verse
		return $this->update->row($update, 'id', $this->table);
	}

	/**
	 * Get local targeted object.
	 *
	 * This method attempts to find a matching object in the local set,
	 * ensuring comparisons are type-consistent and null-safe. Numeric strings are
	 * compared as integers or floats; non-numeric values are compared strictly as strings.
	 *
	 * Defensive checks:
	 * - If the match array does not contain 'key' or 'value', return null.
	 * - If the local object does not have the specified property, skip it.
	 *
	 * @param   array  $match  The [key, value] pair to match against.
	 * @param   array  $local  The local values (array of objects).
	 *
	 * @return  object|null  The found object or null if not found.
	 * @since   2.0.1
	 */
	protected function getTarget(array $match, array $local): ?object
	{
		// Ensure the match array has the required entries
		if (!isset($match['key'], $match['value']))
		{
			return null;
		}

		$key   = $match['key'];
		$value = $match['value'];

		foreach ($local as $_value)
		{
			// Ensure $_value is an object and has the property
			if (!is_object($_value) || !property_exists($_value, $key))
			{
				continue;
			}

			$localVal = $_value->{$key};

			// Handle numeric comparisons
			if (is_numeric($value) && is_numeric($localVal))
			{
				$valueStr   = (string) $value;
				$localStr   = (string) $localVal;
				$isFloatCmp = str_contains($valueStr, '.') || str_contains($localStr, '.');

				if ($isFloatCmp)
				{
					if ((float) $localVal === (float) $value)
					{
						return $_value;
					}
				}
				else
				{
					if ((int) $localVal === (int) $value)
					{
						return $_value;
					}
				}
			}
			else
			{
				// Strict string comparison
				if ((string) $localVal === (string) $value)
				{
					return $_value;
				}
			}
		}

		return null;
	}
}

