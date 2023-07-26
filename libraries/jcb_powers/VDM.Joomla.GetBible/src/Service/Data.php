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
use VDM\Joomla\GetBible\Openai\Config;
use VDM\Joomla\GetBible\Table;
use VDM\Joomla\GetBible\Utilities\StringHelper;
use VDM\Joomla\GetBible\Data\Scripture;
use VDM\Joomla\GetBible\Data\Translation;
use VDM\Joomla\GetBible\Data\Book;
use VDM\Joomla\GetBible\Data\Chapter;
use VDM\Joomla\GetBible\Data\Verse;
use VDM\Joomla\GetBible\Data\Word;
use VDM\Joomla\GetBible\Data\Prompt;
use VDM\Joomla\GetBible\Data\Placeholders;
use VDM\Joomla\GetBible\Data\Response;


/**
 * The GetBible Data Service
 * 
 * @since 3.2.0
 */
class Data implements ServiceProviderInterface
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
		$container->alias(Config::class, 'GetBible.Config')
			->share('GetBible.Config', [$this, 'getConfig'], true);

		$container->alias(Table::class, 'GetBible.Table')
			->share('GetBible.Table', [$this, 'getTable'], true);

		$container->alias(StringHelper::class, 'GetBible.Utilities.String')
			->share('GetBible.Utilities.String', [$this, 'getString'], true);

		$container->alias(Prompt::class, 'GetBible.Prompt')
			->share('GetBible.Prompt', [$this, 'getPrompt'], true);

		$container->alias(Scripture::class, 'GetBible.Scripture')
			->share('GetBible.Scripture', [$this, 'getScripture'], true);

		$container->alias(Translation::class, 'GetBible.Translation')
			->share('GetBible.Translation', [$this, 'getTranslation'], true);

		$container->alias(Book::class, 'GetBible.Book')
			->share('GetBible.Book', [$this, 'getBook'], true);

		$container->alias(Chapter::class, 'GetBible.Chapter')
			->share('GetBible.Chapter', [$this, 'getChapter'], true);

		$container->alias(Verse::class, 'GetBible.Verse')
			->share('GetBible.Verse', [$this, 'getVerse'], true);

		$container->alias(Word::class, 'GetBible.Word')
			->share('GetBible.Word', [$this, 'getWord'], true);

		$container->alias(Placeholders::class, 'GetBible.Placeholders')
			->share('GetBible.Placeholders', [$this, 'getPlaceholders'], true);

		$container->alias(Response::class, 'GetBible.Response')
			->share('GetBible.Response', [$this, 'getResponse'], true);
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
	 * Get the Prompt class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Prompt
	 * @since 3.2.0
	 */
	public function getPrompt(Container $container): Prompt
	{
		return new Prompt(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Config')
		);
	}

	/**
	 * Get the Scripture class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Scripture
	 * @since 3.2.0
	 */
	public function getScripture(Container $container): Scripture
	{
		return new Scripture(
			$container->get('GetBible.Translation'),
			$container->get('GetBible.Book'),
			$container->get('GetBible.Chapter'),
			$container->get('GetBible.Verse'),
			$container->get('GetBible.Word'),
		);
	}

	/**
	 * Get the Translation class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Translation
	 * @since 3.2.0
	 */
	public function getTranslation(Container $container): Translation
	{
		return new Translation(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Config')
		);
	}

	/**
	 * Get the Book class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Book
	 * @since 3.2.0
	 */
	public function getBook(Container $container): Book
	{
		return new Book(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Config')
		);
	}

	/**
	 * Get the Chapter class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Chapter
	 * @since 3.2.0
	 */
	public function getChapter(Container $container): Chapter
	{
		return new Chapter(
			$container->get('GetBible.Load'),
			$container->get('GetBible.Config')
		);
	}

	/**
	 * Get the Verse class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Verse
	 * @since 3.2.0
	 */
	public function getVerse(Container $container): Verse
	{
		return new Verse(
			$container->get('GetBible.Chapter'),
			$container->get('GetBible.Config'),
			$container->get('GetBible.Prompt'),
			$container->get('GetBible.Utilities.String')
		);
	}

	/**
	 * Get the Word class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Word
	 * @since 3.2.0
	 */
	public function getWord(Container $container): Word
	{
		return new Word(
			$container->get('GetBible.Verse'),
			$container->get('GetBible.Config'),
			$container->get('GetBible.Prompt')
		);
	}

	/**
	 * Get the Placeholders class
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Placeholders
	 * @since 3.2.0
	 */
	public function getPlaceholders(Container $container): Placeholders
	{
		return new Placeholders(
			$container->get('GetBible.Scripture'),
			$container->get('GetBible.Prompt')
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
		return new Response(
			$container->get('GetBible.Scripture'),
			$container->get('GetBible.Prompt'),
			$container->get('GetBible.Load'),
			$container->get('GetBible.Config')
		);
	}
}

