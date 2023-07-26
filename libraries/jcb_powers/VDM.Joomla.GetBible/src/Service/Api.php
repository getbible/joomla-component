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
use VDM\Joomla\GetBible\Api\Translations;
use VDM\Joomla\GetBible\Api\Books;
use VDM\Joomla\GetBible\Api\Chapters;
use VDM\Joomla\GetBible\Api\Verses;


/**
 * The GetBible Api Service
 * 
 * @since 2.0.1
 */
class Api implements ServiceProviderInterface
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
		$container->alias(Translations::class, 'GetBible.Api.Translations')
			->share('GetBible.Api.Translations', [$this, 'getTranslations'], true);

		$container->alias(Books::class, 'GetBible.Api.Books')
			->share('GetBible.Api.Books', [$this, 'getBooks'], true);

		$container->alias(Chapters::class, 'GetBible.Api.Chapters')
			->share('GetBible.Api.Chapters', [$this, 'getChapters'], true);

		$container->alias(Verses::class, 'GetBible.Api.Verses')
			->share('GetBible.Api.Verses', [$this, 'getVerses'], true);
	}

	/**
	 * Get the Translations class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Translations
	 * @since 3.2.0
	 */
	public function getTranslations(Container $container): Translations
	{
		return new Translations(
			$container->get('GetBible.Utilities.Http'),
			$container->get('GetBible.Utilities.Uri'),
			$container->get('GetBible.Utilities.Response')
		);
	}

	/**
	 * Get the Books class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Books
	 * @since 3.2.0
	 */
	public function getBooks(Container $container): Books
	{
		return new Books(
			$container->get('GetBible.Utilities.Http'),
			$container->get('GetBible.Utilities.Uri'),
			$container->get('GetBible.Utilities.Response')
		);
	}

	/**
	 * Get the Chapters class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Chapters
	 * @since 3.2.0
	 */
	public function getChapters(Container $container): Chapters
	{
		return new Chapters(
			$container->get('GetBible.Utilities.Http'),
			$container->get('GetBible.Utilities.Uri'),
			$container->get('GetBible.Utilities.Response')
		);
	}

	/**
	 * Get the Verses class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Verses
	 * @since 3.2.0
	 */
	public function getVerses(Container $container): Verses
	{
		return new Verses(
			$container->get('GetBible.Utilities.Http'),
			$container->get('GetBible.Utilities.Uri'),
			$container->get('GetBible.Utilities.Response')
		);
	}
}

