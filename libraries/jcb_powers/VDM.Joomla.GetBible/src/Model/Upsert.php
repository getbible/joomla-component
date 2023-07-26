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

namespace VDM\Joomla\GetBible\Model;


use VDM\Joomla\Abstraction\BaseConfig as Config;
use VDM\Joomla\GetBible\Table;
use VDM\Joomla\Utilities\ObjectHelper;
use VDM\Joomla\Interfaces\ModelInterface;
use VDM\Joomla\Abstraction\Model;


/**
 * The GetBible Model for both Update and Insert
 * 
 * @since 2.0.1
 */
final class Upsert extends Model implements ModelInterface
{
	/**
	 * GetBible Config
	 *
	 * @var    Config
	 * @since 2.0.1
	 */
	protected Config $config;

	/**
	 * Constructor
	 *
	 * @param Config       $config           The getBible config object.
	 * @param Table         $table            The getBible table object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Config $config, Table $table)
	{
		parent::__construct($table);

		$this->config = $config;
	}

	/**
	 * Model the value
	 *          Example: $this->value(value, 'field_key', 'table_name');
	 *
	 * @param   mixed           $value    The value to model
	 * @param   string          $field    The field key
	 * @param   string|null     $table    The table
	 *
	 * @return  mixed
	 * @since 2.0.1
	 */
	public function value($value, string $field, ?string $table = null)
	{
		// set the table name
		if (empty($table))
		{
			$table = $this->getTable();
		}

		// check if this is a valid table
		if (($store = $this->table->get($table, $field, 'store')) !== null)
		{
			// Model Distribution History
			if ($table === 'translation' && $field === 'distribution_history')
			{
				$value = $this->modelDistributionHistory($value);
			}

			// open the value based on the store method
			switch($store)
			{
				case 'json':
					$value = json_encode($value,  JSON_FORCE_OBJECT);
				break;
			}
		}

		return $value;
	}

	/**
	 * Validate before the value is modelled
	 *
	 * @param   mixed         $value   The field value
	 * @param   string|null   $field     The field key
	 * @param   string|null   $table   The table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	protected function validateBefore(&$value, ?string $field = null, ?string $table = null): bool
	{
		// add all values
		return true;
	}

	/**
	 * Validate after the value is modelled
	 *
	 * @param   mixed         $value   The field value
	 * @param   string|null   $field     The field key
	 * @param   string|null   $table   The table
	 *
	 * @return  bool
	 * @since 2.0.1
	 */
	protected function validateAfter(&$value, ?string $field = null, ?string $table = null): bool
	{
		// add all values
		return true;
	}

	/**
	 * Get the current active table
	 *
	 * @return  string
	 * @since 2.0.1
	 */
	protected function getTable(): string
	{
		return $this->config->table_name;
	}

	/**
	 * Model Distribution History
	 *
	 * @param   mixed           $value    The value to model
	 *
	 * @return  mixed
	 * @since 2.0.1
	 */
	public function modelDistributionHistory($value)
	{
		if (ObjectHelper::check($value))
		{
			$n = 0;
			$bucket = [];
			foreach ($value as $version => $description)
			{
				$bucket["distribution_history$n"] = ['version' => $version, 'description' => $description];
				$n++;
			}
			return $bucket;
		}
		return '';
	}
}

