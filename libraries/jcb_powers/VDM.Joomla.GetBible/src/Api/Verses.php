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
 * The GetBible Book Chapter Verses
 * 
 * @since 2.0.1
 */
final class Verses extends Api
{
	/**
	 * Get the verses of chapter in a book in a translation
	 *
	 * @param   string  $translation  The translation.
	 * @param   int     $book         The book number.
	 * @param   int     $chapter      The chapter number.
	 *
	 * @return  object|null    The response object or null if an error occurs.
	 * @since   2.0.1
	 */
	public function get(string $translation, int $book, int $chapter): ?object
	{
		return $this->response->get(
			$this->http->get(
				$this->uri->get($translation . '/' . $book . '/' . $chapter . '.json')
			)
		);
	}
}

