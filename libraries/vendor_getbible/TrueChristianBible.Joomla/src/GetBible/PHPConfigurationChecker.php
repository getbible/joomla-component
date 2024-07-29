<?php
/**
 * @package    Joomla.Component.Builder
 *
 * @created    4th September, 2022
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        Joomla Component Builder <https://git.vdm.dev/joomla/Component-Builder>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace TrueChristianBible\Joomla\GetBible;


use TrueChristianBible\Joomla\Interfaces\PHPConfigurationCheckerInterface;
use TrueChristianBible\Joomla\Abstraction\PHPConfigurationChecker as ExtendingPHPConfigurationChecker;


/**
 * Getbible PHP Configuration Checker
 * 
 * @since 5.02
 */
final class PHPConfigurationChecker extends ExtendingPHPConfigurationChecker implements PHPConfigurationCheckerInterface
{
	/**
	 * The upload max filesize value
	 *
	 * @var    string
	 * @since  5.0.2
	 **/
	protected  string $upload_max_filesize = '64M';

	/**
	 * The post max size value
	 *
	 * @var    string
	 * @since  5.0.2
	 **/
	protected  string $post_max_size = '128M';

	/**
	 * The max execution time value
	 *
	 * @var    int
	 * @since  5.0.2
	 **/
	protected  int $max_execution_time = 60;

	/**
	 * The max input vars value
	 *
	 * @var    int
	 * @since  5.0.2
	 **/
	protected  int $max_input_vars = 5000;

	/**
	 * The max input time value
	 *
	 * @var    int
	 * @since  5.0.2
	 **/
	protected  int $max_input_time = 60;

	/**
	 * The memory limit value
	 *
	 * @var    string
	 * @since  5.0.2
	 **/
	protected  string $memory_limit = '256M';

	/**
	 * Constructor.
	 *
	 * @since  5.0.2
	 */
	public function __construct($app = null)
	{
		parent::__construct($app);

		// set the required PHP Configures
		$this->set('environment.name', 'Getbible environment');
		$this->set('environment.wiki_url', 'git.vdm.dev/getBible/support/wiki/PHP-Settings');
	}
}

