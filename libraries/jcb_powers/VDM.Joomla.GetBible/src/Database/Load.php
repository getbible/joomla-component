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


use VDM\Joomla\GetBible\Table;
use VDM\Joomla\GetBible\Model\Load as Model;
use VDM\Joomla\Database\Load as Database;


/**
 * GetBible Database Load
 * 
 * @since 2.0.1
 */
final class Load
{
	/**
	 * Search Table
	 *
	 * @var    Table
	 * @since 2.0.1
	 */
	protected Table $table;

	/**
	 * Model Load
	 *
	 * @var    Model
	 * @since 2.0.1
	 */
	protected Model $model;

	/**
	 * Database Load
	 *
	 * @var    Database
	 * @since 2.0.1
	 */
	protected Database $load;

	/**
	 * Constructor
	 *
	 * @param Table       $table     The core table object.
	 * @param Model       $model     The model object.
	 * @param Database    $load      The database object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Table $table, Model $model, Database $load)
	{
		$this->table = $table;
		$this->model = $model;
		$this->load = $load;
	}

	/**
	 * Get a value from a given table
	 *          Example: $this->value(
	 *                        [
	 *                           'abbreviation' => 'kjv',
	 *                           'book_nr' => 62,
	 *                           'chapter' => 3,
	 *                           'verse' => 16
	 *                        ], 'value_key', 'table_name'
	 *                    );
	 *
	 * @param   array      $keys      The item keys
	 * @param   string     $field     The field key
	 * @param   string     $table     The table
	 *
	 * @return  mixed
	 * @since 2.0.1
	 */
	public function value(array $keys, string $field, string $table)
	{
		// check if this is a valid table
		if ($this->table->exist($table, $field))
		{
			return $this->model->value(
				$this->load->value(
					["a.{$field}" => $field],
					['a' => $table],
					$this->prefix($keys)
				),
				$field,
				$table
			);
		}

		return null;
	}

	/**
	 * Get the max value based on a filtered result from a given table
	 *          Example: $this->max(
	 *                        [
	 *                           'abbreviation' => 'kjv',
	 *                           'book_nr' => 62,
	 *                           'chapter' => 3,
	 *                           'verse' => 16
	 *                        ], 'value_key', 'table_name'
	 *                    );
	 *
	 * @param   array      $filter    The filter keys
	 * @param   string     $field     The field key
	 * @param   string     $table     The table
	 *
	 * @return  int|null
	 * @since 2.0.1
	 */
	public function max(array $filter, string $field, string $table): ?int
	{
		// check if this is a valid table
		if ($this->table->exist($table, $field))
		{
			return $this->load->max(
				$field,
				['a' => $table],
				$this->prefix($filter)
			);
		}

		return null;
	}

	/**
	 * Count the number of items based on filter result from a given table
	 *          Example: $this->count(
	 *                        [
	 *                           'abbreviation' => 'kjv',
	 *                           'book_nr' => 62,
	 *                           'chapter' => 3,
	 *                           'verse' => 16
	 *                        ], 'table_name'
	 *                    );
	 *
	 * @param   array      $filter    The filter keys
	 * @param   string     $table     The table
	 *
	 * @return  int|null
	 * @since 2.0.1
	 */
	public function count(array $filter, string $table): ?int
	{
		// check if this is a valid table
		if ($this->table->exist($table))
		{
			return $this->load->count(
				['a' => $table],
				$this->prefix($filter)
			);
		}

		return null;
	}

	/**
	 * Get values from a given table
	 *          Example: $this->item(
	 *                        [
	 *                           'abbriviation' => 'kjv',
	 *                           'book_nr' => 62,
	 *                           'chapter' => 3,
	 *                           'verse' => 16
	 *                        ], 'table_name'
	 *                    );
	 *
	 * @param   array    $keys      The item keys
	 * @param   string   $table     The table
	 *
	 * @return  object|null
	 * @since 2.0.1
	 */
	public function item(array $keys, string $table): ?object
	{
		// check if this is a valid table
		if ($this->table->exist($table))
		{
			return $this->model->item(
				$this->load->item(
					['all' => 'a.*'],
					['a' => $table],
					$this->prefix($keys)
				),
				$table
			);
		}

		return null;
	}

	/**
	 * Get values from a given table
	 *          Example: $this->items(
	 *                        [
	 *                           'abbriviation' => [
	 *                              'operator' => 'IN',
	 *                              'value' => ['kjv', 'aov']
	 *                           ],
	 *                           'book_nr' => 62,
	 *                           'chapter' => 3,
	 *                           'verse' =>  [
	 *                              'operator' => 'IN',
	 *                              'value' => [16, 17, 18]
	 *                           ]
	 *                        ], 'table_name'
	 *                    );
	 *          Example: $this->items($ids, 'table_name');
	 *
	 * @param   array    $keys    The item keys
	 * @param   string   $table   The table
	 *
	 * @return  array|null
	 * @since 2.0.1
	 */
	public function items(array $keys, string $table): ?array
	{
		// check if this is a valid table
		if ($this->table->exist($table))
		{
			return $this->model->items(
				$this->load->items(
					['all' => 'a.*'], ['a' => $table], $this->prefix($keys)
				),
				$table
			);
		}

		return null;
	}

	/**
	 * Add prefix to the keys
	 *
	 * @param   array    $keys The query keys
	 *
	 * @return  array
	 * @since 2.0.1
	 */
	private function prefix(array &$keys): array
	{
		// update the key values
		$bucket = [];
		foreach ($keys as $k => $v)
		{
			$bucket['a.' . $k] = $v;
		}
		return $bucket;
	}
}

