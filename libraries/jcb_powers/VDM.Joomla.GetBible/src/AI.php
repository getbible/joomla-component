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


use VDM\Joomla\GetBible\Openai\Config;
use VDM\Joomla\GetBible\Data\Response;
use VDM\Joomla\GetBible\AI\Engineer;


/**
 * The GetBible AI
 * 
 * @since 2.0.1
 */
final class AI
{
	/**
	 * The Config class
	 *
	 * @var    Config
	 * @since  2.0.1
	 */
	protected Config $config;

	/**
	 * The Response class
	 *
	 * @var    Response
	 * @since  2.0.1
	 */
	protected Response $response;

	/**
	 * The Engineer class
	 *
	 * @var    Engineer
	 * @since  2.0.1
	 */
	protected Engineer $engineer;

	/**
	 * Constructor
	 *
	 * @param Config        $config       The response object.
	 * @param Response      $response     The response object.
	 * @param Engineer      $engineer     The engineer object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Config $config, Response $response, Engineer $engineer)
	{
		$this->config = $config;
		$this->response = $response;
		$this->engineer = $engineer;
	}

	/**
	 * Get the Open AI response
	 *
	 * @return  array|null   Array of response messages
	 * @since   2.0.1
	 */
	public function get(): ?array
	{
		// If Open AI isn't enabled in the config, we return null right away.
		if (!$this->config->get('enable_open_ai'))
		{
			return null;
		}

		// If we have enough responses already, return them.
		$response = $this->response->get();
		if ($this->response->isEnough())
		{
			return $response;
		}

		// If there is no existing response and engineer has any response, return it.
		if (empty($response))
		{
			return $this->engineer->get();
		}

		// If there are existing responses, we append the first response from the engineer (if any).
		$_response = $this->engineer->get();
		if (!empty($_response))
		{
			$response[] = $_response[0];
		}

		return $response;
	}
}

