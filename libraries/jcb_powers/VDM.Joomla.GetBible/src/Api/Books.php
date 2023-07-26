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
 * The GetBible Books
 * 
 * @since 2.0.1
 */
final class Books extends Api
{
	/**
	 * Get the books in a translation
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  object|null    The response object or null if an error occurs.
	 * @since   2.0.1
	 */
	public function get(string $translation = 'kjv'): ?object
	{
		return $this->response->get(
			$this->http->get(
				$this->uri->get($translation . '.json')
			)
		);
	}

	/**
	 * List the books in a translation
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  object|null    The response object or null if an error occurs.
	 * @since   2.0.1
	 */
	public function list(string $translation = 'kjv'): ?object
	{
		return $this->response->get(
			$this->http->get(
				$this->uri->get($translation . '/books.json')
			)
		);
	}

	/**
	 * List the books checksums in a translation
	 *
	 * @param   string  $translation  The translation.
	 *
	 * @return  object|null    The response object or null if an error occurs.
	 * @since   2.0.1
	 */
	public function checksum(string $translation = 'kjv'): ?object
	{
		return $this->response->get(
			$this->http->get(
				$this->uri->get($translation . '/checksum.json')
			)
		);
	}

	/**
	 * Get the  book's checksums in a translation
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 *
	 * @return  string|null    The response checksums or null if an error occurs.
	 * @since   2.0.1
	 */
	public function sha(string $translation, int $book): ?string
	{
		return trim(
			$this->response->get(
				$this->http->get(
					$this->uri->get($translation . '/' . $book . '.sha')
				)
			)
		);
	}
}

