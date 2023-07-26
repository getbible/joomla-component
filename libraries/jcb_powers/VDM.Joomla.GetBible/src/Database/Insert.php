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

namespace VDM\Joomla\GetBible\Database;


use VDM\Joomla\GetBible\Model\Upsert as Model;
use VDM\Joomla\Database\Insert as Database;


/**
 * GetBible Database Insert
 * 
 * @since 2.0.1
 */
final class Insert
{
	/**
	 * Model
	 *
	 * @var    Model
	 * @since 2.0.1
	 */
	protected Model $model;

	/**
	 * Database
	 *
	 * @var    Database
	 * @since 2.0.1
	 */
	protected Database $database;

	/**
	 * Constructor
	 *
	 * @param Model       $model       The set model object.
	 * @param Database    $database    The insert database object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Model $model, Database $database)
	{
		$this->model = $model;
		$this->database = $database;
	}

	/**
	 * Insert a value to a given table
	 *          Example: $this->value(Value, 'value_key', 'table_name');
	 *
	 * @param   mixed     $value    The field value
	 * @param   string    $field    The field key
	 * @param   string    $table    Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function value($value, string $field, string $table): bool
	{
		// build the array
		$item = [];
		$item[$field] = $value;

		// Insert the column of this table
		return $this->row($item, $table);
	}

	/**
	 * Insert single row with multiple values to a given table
	 *          Example: $this->item(Array, 'table_name');
	 *
	 * @param   array    $item   The item to save
	 * @param   string   $table  Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function row(array $item, string $table): bool
	{
		// check if array could be modelled
		if (($item = $this->model->row($item, $table)) !== null)
		{
			// Insert the column of this table
			return $this->database->row($item, $table);
		}
		return false;
	}

	/**
	 * Insert multiple rows to a given table
	 *          Example: $this->items(Array, 'table_name');
	 *
	 * @param   array|null   $items  The items updated in database (array of arrays)
	 * @param   string       $table  Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function rows(?array $items, string $table): bool
	{
		// check if arrays could be modelled
		if (($items = $this->model->rows($items, $table)) !== null)
		{
			// Insert the column of this table
			return $this->database->rows($items, $table);
		}
		return false;
	}

	/**
	 * Insert single item with multiple values to a given table
	 *          Example: $this->item(Object, 'table_name');
	 *
	 * @param   object    $item   The item to save
	 * @param   string    $table  Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function item(object $item, string $table): bool
	{
		// check if object could be modelled
		if (($item = $this->model->item($item, $table)) !== null)
		{
			// Insert the column of this table.
			return $this->database->item($item, $table);
		}
		return false;
	}

	/**
	 * Insert multiple items to a given table
	 *          Example: $this->items(Array, 'table_name');
	 *
	 * @param   array|null   $items  The items updated in database (array of objects)
	 * @param   string       $table  Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function items(?array $items, string $table): bool
	{
		// check if object could be modelled
		if (($items = $this->model->items($items, $table)) !== null)
		{
			// Insert the column of this table.
			return $this->database->items($items, $table);
		}
		return false;
	}
}

