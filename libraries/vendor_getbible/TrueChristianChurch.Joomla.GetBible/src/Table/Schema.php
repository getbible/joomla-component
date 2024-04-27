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

namespace TrueChristianChurch\Joomla\GetBible\Table;


use TrueChristianChurch\Joomla\GetBible\Table;
use TrueChristianChurch\Joomla\Interfaces\SchemaInterface;
use TrueChristianChurch\Joomla\Abstraction\Schema as ExtendingSchema;


/**
 * GetBible Tables Schema
 * 
 * @since 3.0.8
 */
final class Schema extends ExtendingSchema implements SchemaInterface
{
	/**
	 * Constructor.
	 *
	 * @param Table   $table   The Table Class.
	 *
	 * @since 3.0.8
	 */
	public function __construct(?Table $table = null)
	{
		$table ??= new Table;

		parent::__construct($table);
	}

	/**
	 * Get the targeted component code
	 *
	 * @return  string
	 * @since 3.0.8
	 */
	protected function getCode(): string
	{
		return 'getbible';
	}
}

