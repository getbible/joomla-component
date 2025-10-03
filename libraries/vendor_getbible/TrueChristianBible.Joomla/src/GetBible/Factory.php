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

namespace TrueChristianBible\Joomla\GetBible;


use Joomla\DI\Container;
use TrueChristianBible\Joomla\GetBible\Service\Api;
use TrueChristianBible\Joomla\GetBible\Service\Utilities;
use TrueChristianBible\Joomla\GetBible\Service\Watcher;
use TrueChristianBible\Joomla\GetBible\Service\App;
use TrueChristianBible\Joomla\GetBible\Service\Model;
use TrueChristianBible\Joomla\GetBible\Service\Database;
use TrueChristianBible\Joomla\Interfaces\FactoryInterface;
use TrueChristianBible\Joomla\Abstraction\Factory as ExtendingFactory;


/**
 * GetBible Factory
 * 
 * @since 2.0.1
 */
abstract class Factory extends ExtendingFactory implements FactoryInterface
{
	/**
	 * Package Container
	 *
	 * @var   Container|null
	 * @since 5.0.3
	 **/
	protected static ?Container $container = null;

	/**
	 * Create a container object
	 *
	 * @return  Container
	 * @since  2.0.1
	 */
	protected static function createContainer(): Container
	{
		return (new Container())
			->registerServiceProvider(new Utilities())
			->registerServiceProvider(new Api())
			->registerServiceProvider(new Watcher())
			->registerServiceProvider(new App())
			->registerServiceProvider(new Model())
			->registerServiceProvider(new Database());
	}
}

