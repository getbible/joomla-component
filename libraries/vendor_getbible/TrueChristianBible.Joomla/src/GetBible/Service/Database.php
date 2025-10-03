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

namespace TrueChristianBible\Joomla\GetBible\Service;


use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use TrueChristianBible\Joomla\Database\Insert as BaseInsert;
use TrueChristianBible\Joomla\Database\Update as BaseUpdate;
use TrueChristianBible\Joomla\Database\Load as BaseLoad;
use TrueChristianBible\Joomla\Database\Delete as BaseDelete;
use TrueChristianBible\Joomla\GetBible\Database\Insert;
use TrueChristianBible\Joomla\GetBible\Database\Load;
use TrueChristianBible\Joomla\GetBible\Database\Update;


/**
 * The GetBible Database Service
 * 
 * @since 2.0.1
 */
class Database implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 * @since 2.0.1
	 */
	public function register(Container $container)
	{
		$container->alias(BaseInsert::class, 'Insert')
			->share('Insert', [$this, 'getBaseInsert'], true);

		$container->alias(BaseUpdate::class, 'Update')
			->share('Update', [$this, 'getBaseUpdate'], true);

		$container->alias(BaseLoad::class, 'Load')
			->share('Load', [$this, 'getBaseLoad'], true);

		$container->alias(BaseDelete::class, 'Delete')
			->share('Delete', [$this, 'getBaseDelete'], true);

		$container->alias(Insert::class, 'GetBible.Insert')
			->share('GetBible.Insert', [$this, 'getInsert'], true);

		$container->alias(Update::class, 'GetBible.Update')
			->share('GetBible.Update', [$this, 'getUpdate'], true);

		$container->alias(Load::class, 'GetBible.Load')
			->share('GetBible.Load', [$this, 'getLoad'], true);
	}

	/**
	 * Get the BaseUpdate class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  BaseUpdate
	 * @since 2.0.1
	 */
	public function getBaseUpdate(Container $container): BaseUpdate
	{
		return new BaseUpdate();
	}

	/**
	 * Get the BaseInsert class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  BaseInsert
	 * @since 2.0.1
	 */
	public function getBaseInsert(Container $container): BaseInsert
	{
		return new BaseInsert();
	}

	/**
	 * Get the BaseLoad class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  BaseLoad
	 * @since 2.0.1
	 */
	public function getBaseLoad(Container $container): BaseLoad
	{
		return new BaseLoad();
	}

	/**
	 * Get the BaseDelete class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  BaseDelete
	 * @since 5.0.15
	 */
	public function getBaseDelete(Container $container): BaseDelete
	{
		return new BaseDelete();
	}

	/**
	 * Get the Insert class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Insert
	 * @since 2.0.1
	 */
	public function getInsert(Container $container): Insert
	{
		return new Insert(
			$container->get('GetBible.Model.Upsert'),
			$container->get('Insert')
		);
	}

	/**
	 * Get the Update class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Update
	 * @since 2.0.1
	 */
	public function getUpdate(Container $container): Update
	{
		return new Update(
			$container->get('GetBible.Model.Upsert'),
			$container->get('Update')
		);
	}

	/**
	 * Get the Table class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Load
	 * @since 2.0.1
	 */
	public function getLoad(Container $container): Load
	{
		return new Load(
			$container->get('GetBible.Table'),
			$container->get('GetBible.Model.Load'),
			$container->get('Load')
		);
	}
}

