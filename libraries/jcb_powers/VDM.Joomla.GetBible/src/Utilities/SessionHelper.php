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


use Joomla\CMS\Factory as JoomlaFactory;
use Joomla\CMS\Session\Session;
use VDM\Joomla\Utilities\GuidHelper;


/**
 * The GetBible Session Helper
 *   - Just for easy set and get
 * 
 * @since 2.0.1
 */
final class SessionHelper
{
	/**
	 * The Session
	 *
	 * @var    Session
	 * @since 3.2.0
	 */
	protected Session $session;

	/**
	 * Constructor
	 *
	 * @param Session|null   $session   The Joomla session.
	 *
	 * @since 3.2.0
	 */
	public function __construct(?Session $session = null)
	{
		$this->session = $session ?: JoomlaFactory::getSession();
	}

	/**
	 * Get data from the session store
	 *
	 * @param   string  $name     Name of a variable
	 * @param   mixed   $default  Default value of a variable if not set
	 *
	 * @return  mixed  Value of a variable
	 * @since 2.1.0
	 */
	public function get(string $name, $default = null)
	{
		return $this->session->get($name, $default);
	}

	/**
	 * Set data into the session store.
	 *
	 * @param   string  $name   Name of a variable.
	 * @param   mixed   $value  Value of a variable.
	 *
	 * @return  mixed  Old value of a variable.
	 * @since 2.1.0
	 */
	public function set($name, $value = null)
	{
		return $this->session->set($name, $value);
	}
}

