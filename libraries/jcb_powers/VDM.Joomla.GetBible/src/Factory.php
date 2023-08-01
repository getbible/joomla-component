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

namespace VDM\Joomla\GetBible;


use Joomla\DI\Container;
use VDM\Joomla\GetBible\Service\Api;
use VDM\Joomla\GetBible\Service\Utilities;
use VDM\Joomla\GetBible\Service\Watcher;
use VDM\Joomla\GetBible\Service\App;
use VDM\Joomla\GetBible\Service\Model;
use VDM\Joomla\GetBible\Service\Database;
use VDM\Joomla\Interfaces\FactoryInterface;


/**
 * GetBible Factory
 * 
 * @since 2.0.1
 */
abstract class Factory implements FactoryInterface
{
	/**
	 * Global Package Container
	 *
	 * @var     Container
	 * @since 2.0.1
	 **/
	protected static $container = null;

	/**
	 * Get any class from the package container
	 *
	 * @param   string  $key  The container class key
	 *
	 * @return  Mixed
	 * @since   2.0.1
	 */
	public static function _($key)
	{
		return self::getContainer()->get($key);
	}

	/**
	 * Get the global package container
	 *
	 * @return  Container
	 * @since   2.0.1
	 */
	public static function getContainer(): Container
	{
		if (!self::$container)
		{
			self::$container = self::createContainer();
		}

		return self::$container;
	}

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

