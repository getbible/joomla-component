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

namespace VDM\Joomla\GetBible\Abstraction;


use Joomla\CMS\Date\Date;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Database\Insert;
use VDM\Joomla\GetBible\Database\Update;


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
	 * The target
	 *
	 * @var    object|null
	 * @since  2.0.1
	 */
	protected ?object $target;

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

		// just in-case we set a date
		$this->today = (new Date())->toSql();
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
	 * Get local targeted object
	 *
	 * @param   array    $match    The [key, value].
	 * @param   array    $local    The local values.
	 *
	 * @return  object|null   The found value
	 * @since   2.0.1
	 */
	protected function getTarget(array $match, array &$local): ?object
	{
		foreach ($local as $_value)
		{
			if ($_value->{$match['key']} === $match['value'])
			{
				return $_value;
			}
		}

		return null;
	}
}

