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


use Joomla\Http\Response as JoomlaResponse;
use VDM\Joomla\Utilities\JsonHelper;
use VDM\Joomla\Utilities\StringHelper;


/**
 * The GetBible Response
 * 
 * @since 2.0.1
 */
final class Response
{
	/**
	 * Process the response and decode it.
	 *
	 * @param   JoomlaResponse  $response      The response.
	 * @param   integer         $expectedCode  The expected "good" code.
	 * @param   mixed           $default       The default if body not have length
	 *
	 * @return  mixed
	 *
	 * @since   2.0.1
	 * @throws  \DomainException
	 **/
	public function get(JoomlaResponse $response, int  $expectedCode = 200, $default = null)
	{
		// Validate the response code.
		if ($response->code != $expectedCode)
		{
			// Decode the error response and throw an exception.
			$message = $this->error($response);

			// Throw an exception with the GetBible error message and code.
			throw new \DomainException($message, $response->code);
		}

		return $this->getBody($response, $default);
	}

	/**
	 * Return the body from the response
	 *
	 * @param   JoomlaResponse  $response    The response.
	 * @param   mixed           $default     The default if body not have length
	 *
	 * @return  mixed
	 * @since   2.0.1
	 **/
	protected function getBody(JoomlaResponse $response, $default = null)
	{
		$body = $response->body ?? null;
		// check that we have a body
		if (StringHelper::check($body))
		{
			// if it's JSON, decode it
			if (JsonHelper::check($body))
			{
				return json_decode((string) $body);
			}
			
			// if it's XML, convert it to an object
			libxml_use_internal_errors(true);
			$xml = simplexml_load_string($body);
			if ($xml !== false)
			{
				return $xml;
			}

			// if it's neither JSON nor XML, return as is
			return $body;
		}

		return $default;
	}

	/**
	 * Get the error message from the GetBible API response
	 *
	 * @param   JoomlaResponse  $response   The response.
	 *
	 * @return  string
	 * @since   2.0.1
	 **/
	protected function error(JoomlaResponse $response): string
	{
		$body = $response->body ?? null;
		// do we have a json string
		if (JsonHelper::check($body))
		{
			$error = json_decode($body);
		}
		else
		{
			return 'Invalid or empty response body.';
		}

		// check if GetBible returned an error object
		if (isset($error->Error))
		{
			// error object found, extract message and code
			$errorMessage = isset($error->Error->Message) ? $error->Error->Message : 'Unknown error.';
			$errorCode = isset($error->Error->Code) ? $error->Error->Code : 'Unknown error code.';

			// return formatted error message
			return 'Wasabi Error: ' . $errorMessage . ' Code: ' . $errorCode;
		}

		return 'No error information found in response.';
	}
}

