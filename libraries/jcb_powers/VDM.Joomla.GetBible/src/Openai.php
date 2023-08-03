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
use VDM\Joomla\GetBible\Service\Openai as Api;
use VDM\Joomla\Openai\Service\Utilities;
use VDM\Joomla\GetBible\Service\Data;
use VDM\Joomla\GetBible\Service\AI;
use VDM\Joomla\GetBible\Service\Model;
use VDM\Joomla\GetBible\Service\Database;
use VDM\Joomla\Interfaces\FactoryInterface;


/**
 * Openai Factory
 * 
 * @since 3.2.0
 */
abstract class Openai implements FactoryInterface
{
	/**
	 * Global Package Container
	 *
	 * @var     Container
	 * @since 3.2.0
	 **/
	protected static $container = null;

	/**
	 * Get any class from the package container
	 *
	 * @param   string  $key  The container class key
	 *
	 * @return  Mixed
	 * @since 3.2.0
	 */
	public static function _($key)
	{
		return self::getContainer()->get($key);
	}

	/**
	 * Get the global package container
	 *
	 * @return  Container
	 * @since 3.2.0
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
	 * @since 3.2.0
	 */
	protected static function createContainer(): Container
	{
		return (new Container())
			->registerServiceProvider(new Api())
			->registerServiceProvider(new Utilities())
			->registerServiceProvider(new Data())
			->registerServiceProvider(new AI())
			->registerServiceProvider(new Model())
			->registerServiceProvider(new Database());
	}
}
