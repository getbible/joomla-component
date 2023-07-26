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
use VDM\Joomla\Openai\Chat;
use VDM\Joomla\Openai\Completions;
use VDM\Joomla\Openai\Models;
use VDM\Joomla\Openai\Moderate;


/**
 * The Openai Api Service
 * 
 * @since 3.2.0
 */
class Openai implements ServiceProviderInterface
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
		$container->alias(Chat::class, 'Openai.Chat')
			->share('Openai.Chat', [$this, 'getChat'], true);

		$container->alias(Completions::class, 'Openai.Completions')
			->share('Openai.Completions', [$this, 'getCompletions'], true);

		$container->alias(Models::class, 'Openai.Models')
			->share('Openai.Models', [$this, 'getModels'], true);

		$container->alias(Moderate::class, 'Openai.Moderate')
			->share('Openai.Moderate', [$this, 'getModerate'], true);
	}

	/**
	 * Get the Chat class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Chat
	 * @since 3.2.0
	 */
	public function getChat(Container $container): Chat
	{
		return new Chat(
			$container->get('Openai.Utilities.Http'),
			$container->get('Openai.Utilities.Uri'),
			$container->get('Openai.Utilities.Response')
		);
	}

	/**
	 * Get the Completions class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Completions
	 * @since 3.2.0
	 */
	public function getCompletions(Container $container): Completions
	{
		return new Completions(
			$container->get('Openai.Utilities.Http'),
			$container->get('Openai.Utilities.Uri'),
			$container->get('Openai.Utilities.Response')
		);
	}

	/**
	 * Get the Models class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Models
	 * @since 3.2.0
	 */
	public function getModels(Container $container): Models
	{
		return new Models(
			$container->get('Openai.Utilities.Http'),
			$container->get('Openai.Utilities.Uri'),
			$container->get('Openai.Utilities.Response')
		);
	}

	/**
	 * Get the Moderate class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Moderate
	 * @since 3.2.0
	 */
	public function getModerate(Container $container): Moderate
	{
		return new Moderate(
			$container->get('Openai.Utilities.Http'),
			$container->get('Openai.Utilities.Uri'),
			$container->get('Openai.Utilities.Response')
		);
	}
}

