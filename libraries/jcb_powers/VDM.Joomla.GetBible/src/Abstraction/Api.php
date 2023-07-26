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

namespace VDM\Joomla\GetBible\Abstraction;


use VDM\Joomla\GetBible\Utilities\Http;
use VDM\Joomla\GetBible\Utilities\Uri;
use VDM\Joomla\GetBible\Utilities\Response;


/**
 * The GetBible Api
 * 
 * @since 2.0.1
 */
abstract class Api
{
	/**
	 * The Http class
	 *
	 * @var    Http
	 * @since  2.0.1
	 */
	protected Http $http;

	/**
	 * The Uri class
	 *
	 * @var    Uri
	 * @since  2.0.1
	 */
	protected Uri $uri;

	/**
	 * The Response class
	 *
	 * @var    Response
	 * @since  2.0.1
	 */
	protected Response $response;

	/**
	 * Constructor.
	 *
	 * @param   Http        $http       The http class.
	 * @param   Uri         $uri        The uri class.
	 * @param   Response    $response   The response class.
	 *
	 * @since   2.0.1
	 **/
	public function __construct(Http $http, Uri $uri, Response $response)
	{
		$this->http = $http;
		$this->uri = $uri;
		$this->response = $response;
	}
}

