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

namespace VDM\Joomla\GetBible\Utilities;


use Joomla\Uri\Uri as JoomlaUri;
use VDM\Joomla\GetBible\Config;


/**
 * The GetBible Uri
 * 
 * @since 2.0.1
 */
final class Uri
{
	/**
	 * The Config class
	 *
	 * @var    Config
	 * @since  2.0.1
	 */
	protected Config $config;

	/**
	 * Constructor
	 *
	* @param   Config      $config     The config class.
	 *
	 * @since   2.0.1
	 **/
	public function __construct(Config $config)
	{
		// set the API config
		$this->config = $config;
	}

	/**
	 * Method to build and return a full request URL for the request.
	 *
	 * @param   string   $path   URL to inflect
	 *
	 * @return  JoomlaUri
	 * @since   2.0.1
	 **/
	public function get(string $path): JoomlaUri
	{
		// Get a new Uri object focusing the api url and given path.
		$uri = new JoomlaUri($this->config->endpoint . $path);

		return $uri;
	}
}

