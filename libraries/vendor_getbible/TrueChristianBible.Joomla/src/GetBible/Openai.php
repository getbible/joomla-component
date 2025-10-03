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
use TrueChristianBible\Joomla\GetBible\Service\Openai as Api;
use TrueChristianBible\Joomla\Openai\Service\Utilities;
use TrueChristianBible\Joomla\GetBible\Service\Data;
use TrueChristianBible\Joomla\GetBible\Service\AI;
use TrueChristianBible\Joomla\GetBible\Service\Model;
use TrueChristianBible\Joomla\GetBible\Service\Database;
use TrueChristianBible\Joomla\Interfaces\FactoryInterface;
use TrueChristianBible\Joomla\Abstraction\Factory;


/**
 * Openai Factory
 * 
 * @since 3.2.0
 */
abstract class Openai extends Factory implements FactoryInterface
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

