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
use VDM\Joomla\GetBible\Watcher as Watch;
use VDM\Joomla\GetBible\Watcher\Translation;
use VDM\Joomla\GetBible\Watcher\Book;
use VDM\Joomla\GetBible\Watcher\Chapter;


/**
 * The GetBible Watcher Service
 * 
 * @since 2.0.1
 */
class Watcher implements ServiceProviderInterface
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
		$container->alias(Watch::class, 'GetBible.Watcher')
			->share('GetBible.Watcher', [$this, 'getWatcher'], true);

		$container->alias(Translation::class, 'GetBible.Watcher.Translation')
			->share('GetBible.Watcher.Translation', [$this, 'getTranslation'], true);

		$container->alias(Book::class, 'GetBible.Watcher.Book')
			->share('GetBible.Watcher.Book', [$this, 'getBook'], true);

		$container->alias(Chapter::class, 'GetBible.Watcher.Chapter')
			->share('GetBible.Watcher.Chapter', [$this, 'getChapter'], true);
	}

	/**
	 * Get the Watcher class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Watch
	 * @since 2.0.1
	 */
	public function getWatcher(Container $container): Watch
	{
		return new Watch(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Watcher.Translation'),
			$container->get('GetBible.Watcher.Book'),
			$container->get('GetBible.Watcher.Chapter')
		);
	}

	/**
	 * Get the Translation class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Translation
	 * @since 2.0.1
	 */
	public function getTranslation(Container $container): Translation
	{
		return new Translation(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Api.Translations')
		);
	}

	/**
	 * Get the Book class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Book
	 * @since 2.0.1
	 */
	public function getBook(Container $container): Book
	{
		return new Book(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Api.Books')
		);
	}

	/**
	 * Get the Chapter class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Chapter
	 * @since 2.0.1
	 */
	public function getChapter(Container $container): Chapter
	{
		return new Chapter(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Api.Chapters'),
			$container->get('GetBible.Api.Verses')
		);
	}
}

