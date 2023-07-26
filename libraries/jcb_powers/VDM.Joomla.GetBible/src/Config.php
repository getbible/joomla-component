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


use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Abstraction\BaseConfig;


/**
 * GetBible Configurations
 * 
 * @since 2.0.1
 */
class Config extends BaseConfig
{
	/**
	 * get Bible API url
	 *
	 * @return  string  The API Endpoint
	 * @since 2.0.1
	 */
	protected function getEndpoint(): ?string
	{
		return $this->schema . '://' . $this->domain . '/' . $this->version . '/';
	}

	/**
	 * get Bible API Schema
	 *
	 * @return  string  The Get Bible Schema
	 * @since 2.0.1
	 */
	protected function getSchema(): ?string
	{
		return 'https';
	}

	/**
	 * get Bible API domain
	 *
	 * @return  string  The Get Bible Domain
	 * @since 2.0.1
	 */
	protected function getDomain(): ?string
	{
		return 'api.getbible.net';
	}

	/**
	 * get Bible version
	 *
	 * @return  string  The Get Bible Version
	 * @since 2.0.1
	 */
	protected function getVersion(): ?string
	{
		return 'v2';
	}

	/**
	 * get Daily Scripture URL
	 *
	 * @return  string  The Get Daily Scripture URL
	 * @since 2.0.1
	 */
	protected function getDailyscriptureurl(): ?string
	{
		return 'https://raw.githubusercontent.com/trueChristian/daily-scripture/master/README.today';
	}
}

