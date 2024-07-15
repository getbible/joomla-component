<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    4th September, 2022
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TrueChristianBible\Joomla\GetBible\Table;


use TrueChristianBible\Joomla\GetBible\Table;
use TrueChristianBible\Joomla\GetBible\Table\Schema;
use TrueChristianBible\Joomla\Interfaces\SchemaCheckerInterface;
use TrueChristianBible\Joomla\Abstraction\SchemaChecker as ExtendingSchemaChecker;


/**
 * Getbible Tables Schema Checker
 * 
 * @since 3.2.2
 */
final class SchemaChecker extends ExtendingSchemaChecker implements SchemaCheckerInterface
{
	/**
	 * Get the targeted component code
	 *
	 * @return  string
	 * @since 3.2.2
	 */
	protected function getCode(): string
	{
		return 'getbible';
	}

	/**
	 * Get the targeted component power path
	 *
	 * @return  string
	 * @since 3.2.2
	 */
	protected function getPowerPath(): string
	{
		return 'src/Helper/PowerloaderHelper.php';
	}

	/**
	 * Get the fully qualified name of the schema class.
	 *
	 * @return string
	 * @since 3.2.2
	 */
	protected function getSchemaClass(): string
	{
		return Schema::class;
	}

	/**
	 * Get the fully qualified name of the table class.
	 *
	 * @return string
	 * @since 3.2.2
	 */
	protected function getTableClass(): string
	{
		return Table::class;
	}
}

