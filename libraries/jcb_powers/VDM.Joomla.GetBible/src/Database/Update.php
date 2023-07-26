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
use VDM\Joomla\Database\Update as Database;


/**
 * The GetBible Database Update
 * 
 * @since 2.0.1
 */
final class Update
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
	 * @param Database    $database    The update database object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Model $model, Database $database)
	{
		$this->model = $model;
		$this->database = $database;
	}

	/**
	 * Update a value to a given table
	 *          Example: $this->value(Value, 'value_key', 'id', 'table_name');
	 *
	 * @param   mixed     $value      The field value
	 * @param   string    $field      The field key
	 * @param   string    $keyValue   The key value
	 * @param   string    $key        The key name
	 * @param   string    $table      Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function value($value, string $field, string $keyValue, string $key, string $table): bool
	{
		// build the array
		$item = [];
		$item[$key] = $keyValue;
		$item[$field] = $value;

		// Update the column of this table using guid as the primary key.
		return $this->row($item, $key, $table);
	}

	/**
	 * Update single row with multiple values to a given table
	 *          Example: $this->item(Array, 'id', 'table_name');
	 *
	 * @param   array    $item     The item to save
	 * @param   string   $key      The key name
	 * @param   string   $table    Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function row(array $item, string $key, string $table): bool
	{
		// check if object could be modelled
		if (($item = $this->model->row($item, $table)) !== null)
		{
			// Update the column of this table using $key as the primary key.
			return $this->database->row($item, $key, $table);
		}
		return false;
	}

	/**
	 * Update multiple rows to a given table
	 *          Example: $this->items(Array, 'id', 'table_name');
	 *
	 * @param   array|null   $items   The items updated in database (array of arrays)
	 * @param   string       $key     The key name
	 * @param   string       $table   Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function rows(?array $items, string $key, string $table): bool
	{
		// check if object could be modelled
		if (($items = $this->model->rows($items, $table)) !== null)
		{
			// Update the column of this table using $key as the primary key.
			return $this->database->rows($items, $key, $table);
		}
		return false;
	}

	/**
	 * Update single item with multiple values to a given table
	 *          Example: $this->item(Object, 'id', 'table_name');
	 *
	 * @param   object    $item   The item to save
	 * @param   string    $key    The key name
	 * @param   string    $table  Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function item(object $item, string $key, string $table): bool
	{
		// check if object could be modelled
		if (($item = $this->model->item($item, $table)) !== null)
		{
			// Update the column of this table using $key as the primary key.
			return $this->database->item($item, $key, $table);
		}
		return false;
	}

	/**
	 * Update multiple items to a given table
	 *          Example: $this->items(Array, 'id', 'table_name');
	 *
	 * @param   array|null   $items   The items updated in database (array of objects)
	 * @param   string       $key     The key name
	 * @param   string       $table   Target table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	public function items(?array $items, string $key, string $table): bool
	{
		// check if object could be modelled
		if (($items = $this->model->items($items, $table)) !== null)
		{
			// Update the column of this table using $key as the primary key.
			return $this->database->items($items, $key, $table);
		}
		return false;
	}
}

