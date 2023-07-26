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
use VDM\Joomla\GetBible\Utilities\Uri;
use VDM\Joomla\GetBible\Utilities\Response;
use VDM\Joomla\GetBible\Utilities\Http;
use VDM\Joomla\GetBible\Utilities\StringHelper;
use VDM\Joomla\GetBible\Utilities\SessionHelper;


/**
 * The GetBible Utilities Service
 * 
 * @since 3.2.0
 */
class Utilities implements ServiceProviderInterface
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
		$container->alias(Uri::class, 'GetBible.Utilities.Uri')
			->share('GetBible.Utilities.Uri', [$this, 'getUri'], true);

		$container->alias(Response::class, 'GetBible.Utilities.Response')
			->share('GetBible.Utilities.Response', [$this, 'getResponse'], true);

		$container->alias(Http::class, 'GetBible.Utilities.Http')
			->share('GetBible.Utilities.Http', [$this, 'getHttp'], true);

		$container->alias(StringHelper::class, 'GetBible.Utilities.String')
			->share('GetBible.Utilities.String', [$this, 'getString'], true);

		$container->alias(SessionHelper::class, 'GetBible.Utilities.Session')
			->share('GetBible.Utilities.Session', [$this, 'getSession'], true);
	}

	/**
	 * Get the Uri class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Uri
	 * @since 3.2.0
	 */
	public function getUri(Container $container): Uri
	{
		return new Uri(
			$container->get('GetBible.Config')
		);
	}

	/**
	 * Get the Response class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Response
	 * @since 3.2.0
	 */
	public function getResponse(Container $container): Response
	{
		return new Response();
	}

	/**
	 * Get the Http class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Http
	 * @since 3.2.0
	 */
	public function getHttp(Container $container): Http
	{
		return new Http();
	}

	/**
	 * Get the String Helper class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  StringHelper
	 * @since 3.2.0
	 */
	public function getString(Container $container): StringHelper
	{
		return new StringHelper();
	}

	/**
	 * Get the Session Helper class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  SessionHelper
	 * @since 3.2.0
	 */
	public function getSession(Container $container): SessionHelper
	{
		return new SessionHelper();
	}
}

