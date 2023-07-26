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

namespace VDM\Joomla\GetBible\Service;


use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use VDM\Joomla\GetBible\Model\Upsert;
use VDM\Joomla\GetBible\Model\Load;


/**
 * The GetBible Model Service
 * 
 * @since 2.0.1
 */
class Model implements ServiceProviderInterface
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
		$container->alias(Upsert::class, 'GetBible.Model.Upsert')
			->share('GetBible.Model.Upsert', [$this, 'getUpsert'], true);

		$container->alias(Load::class, 'GetBible.Model.Load')
			->share('GetBible.Model.Load', [$this, 'getLoad'], true);
	}

	/**
	 * Get the Upsert class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Upsert
	 * @since 2.0.1
	 */
	public function getUpsert(Container $container): Upsert
	{
		return new Upsert(
			$container->get('GetBible.Config'),
			$container->get('GetBible.Table')
		);
	}

	/**
	 * Get the Load class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Load
	 * @since 2.0.1
	 */
	public function getLoad(Container $container): Load
	{
		return new Load(
			$container->get('GetBible.Config'),
			$container->get('GetBible.Table')
		);
	}
}

