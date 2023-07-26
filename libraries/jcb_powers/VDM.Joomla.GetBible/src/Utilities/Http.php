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


use Joomla\CMS\Http\Http as JoomlaHttp;
use Joomla\Registry\Registry;


/**
 * The GetBible Http
 * 
 * @since 2.0.1
 */
final class Http extends JoomlaHttp
{
	/**
	 * Constructor.
	 *
	 * @since   2.0.1
	 * @throws  \InvalidArgumentException
	 **/
	public function __construct()
	{
		// setup config
		$config = [
			'userAgent' => 'JoomlaGetBible/2.0',
			'headers' => [
				'Content-Type' => 'application/json'
			]
		];

		$options = new Registry($config);

		// run parent constructor
		parent::__construct($options);
	}
}

