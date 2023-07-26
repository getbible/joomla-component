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

namespace VDM\Joomla\GetBible\Api;


use VDM\Joomla\GetBible\Abstraction\Api;


/**
 * The GetBible Translations
 * 
 * @since 2.0.1
 */
final class Translations extends Api
{
	/**
	 * List the translations
	 *
	 * @return  object|null    The response object or null if an error occurs.
	 * @since   2.0.1
	 */
	public function list(): ?object
	{
		return $this->response->get(
			$this->http->get(
				$this->uri->get('translations.json')
			)
		);
	}

	/**
	 * List the translations checksums
	 *
	 * @return  object|null    The response object or null if an error occurs.
	 * @since   2.0.1
	 */
	public function checksum(): ?object
	{
		return $this->response->get(
			$this->http->get(
				$this->uri->get('checksum.json')
			)
		);
	}

	/**
	 * Get the translation's checksums 
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  string|null    The response checksum or null if an error occurs.
	 * @since   2.0.1
	 */
	public function sha(string $translation = 'kjv'): ?string
	{
		return trim(
			$this->response->get(
				$this->http->get(
					$this->uri->get($translation . '.sha')
				)
			)
		);
	}
}

