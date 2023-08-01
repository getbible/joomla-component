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
use VDM\Joomla\GetBible\Config;
use VDM\Joomla\GetBible\Table;
use VDM\Joomla\GetBible\DailyScripture;
use VDM\Joomla\GetBible\Search;
use VDM\Joomla\GetBible\Loader;
use VDM\Joomla\GetBible\Linker;
use VDM\Joomla\GetBible\Note;
use VDM\Joomla\GetBible\Tag;
use VDM\Joomla\GetBible\Tagged;
use VDM\Joomla\GetBible\Tagged\Paragraphs;


/**
 * The GetBible App Service
 * 
 * @since 2.0.1
 */
class App implements ServiceProviderInterface
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
		$container->alias(Config::class, 'GetBible.Config')
			->share('GetBible.Config', [$this, 'getConfig'], true);

		$container->alias(Table::class, 'GetBible.Table')
			->share('GetBible.Table', [$this, 'getTable'], true);

		$container->alias(DailyScripture::class, 'DailyScripture')
			->share('DailyScripture', [$this, 'getDailyScripture'], true);

		$container->alias(Search::class, 'GetBible.Search')
			->share('GetBible.Search', [$this, 'getSearch'], true);

		$container->alias(Loader::class, 'GetBible.Loader')
			->share('GetBible.Loader', [$this, 'getLoader'], true);

		$container->alias(Linker::class, 'GetBible.Linker')
			->share('GetBible.Linker', [$this, 'getLinker'], true);

		$container->alias(Note::class, 'GetBible.Note')
			->share('GetBible.Note', [$this, 'getNote'], true);

		$container->alias(Tag::class, 'GetBible.Tag')
			->share('GetBible.Tag', [$this, 'getTag'], true);

		$container->alias(Tagged::class, 'GetBible.Tagged')
			->share('GetBible.Tagged', [$this, 'getTagged'], true);

		$container->alias(Paragraphs::class, 'GetBible.Tagged.Paragraphs')
			->share('GetBible.Tagged.Paragraphs', [$this, 'getTaggedParagraphs'], true);
	}

	/**
	 * Get the Config class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Config
	 * @since 2.0.1
	 */
	public function getConfig(Container $container): Config
	{
		return new Config();
	}

	/**
	 * Get the Table class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Table
	 * @since 2.0.1
	 */
	public function getTable(Container $container): Table
	{
		return new Table();
	}

	/**
	 * Get the Daily Scripture class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  DailyScripture
	 * @since 2.0.1
	 */
	public function getDailyScripture(Container $container): DailyScripture
	{
		return new DailyScripture(
			$container->get('GetBible.Config'),
			$container->get('GetBible.Utilities.Http'),
			$container->get('GetBible.Load')
		);
	}

	/**
	 * Get the Search class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Search
	 * @since 2.0.1
	 */
	public function getSearch(Container $container): Search
	{
		return new Search();
	}

	/**
	 * Get the Loader class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Loader
	 * @since 2.0.1
	 */
	public function getLoader(Container $container): Loader
	{
		return new Loader(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Watcher')
		);
	}

	/**
	 * Get the Linker class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Linker
	 * @since 2.0.1
	 */
	public function getLinker(Container $container): Linker
	{
		return new Linker(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Utilities.Session')
		);
	}

	/**
	 * Get the Note class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Note
	 * @since 2.0.1
	 */
	public function getNote(Container $container): Note
	{
		return new Note(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Linker')
		);
	}

	/**
	 * Get the Tag class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Tag
	 * @since 2.0.1
	 */
	public function getTag(Container $container): Tag
	{
		return new Tag(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Linker')
		);
	}

	/**
	 * Get the Tagged class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Tagged
	 * @since 2.0.1
	 */
	public function getTagged(Container $container): Tagged
	{
		return new Tagged(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Insert'),
			$container->get('GetBible.Update'),
			$container->get('GetBible.Linker')
		);
	}

	/**
	 * Get the Paragraphs class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Paragraphs
	 * @since 2.0.1
	 */
	public function getTaggedParagraphs(Container $container): Paragraphs
	{
		return new Paragraphs();
	}
}

