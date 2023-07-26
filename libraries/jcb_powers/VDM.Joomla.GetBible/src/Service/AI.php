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
use VDM\Joomla\GetBible\AI as GetBible;
use VDM\Joomla\GetBible\AI\Engineer;


/**
 * The GetBible AI Service
 * 
 * @since 3.2.0
 */
class AI implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 * @since 3.2.0
	 */
	public function register(Container $container)
	{
		$container->alias(GetBible::class, 'GetBible.AI')
			->share('GetBible.AI', [$this, 'getAI'], true);

		$container->alias(Engineer::class, 'GetBible.AI.Engineer')
			->share('GetBible.AI.Engineer', [$this, 'getEngineer'], true);
	}

	/**
	 * Get the GetBible AI class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  GetBible
	 * @since 3.2.0
	 */
	public function getAI(Container $container): GetBible
	{
		return new GetBible(
			$container->get('GetBible.Config'),
			$container->get('GetBible.Response'),
			$container->get('GetBible.AI.Engineer')
		);
	}

	/**
	 * Get the Engineer AI class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Engineer
	 * @since 3.2.0
	 */
	public function getEngineer(Container $container): Engineer
	{
		return new Engineer(
			$container->get('GetBible.Scripture'),
			$container->get('GetBible.Prompt'),
			$container->get('GetBible.Placeholders'),
			$container->get('Openai.Chat'),
			$container->get('GetBible.Insert')
		);
	}
}

