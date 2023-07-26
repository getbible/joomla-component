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


use Joomla\Registry\Registry;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Watcher;


/**
 * The GetBible Loader of Scripture in Content
 * 
 * @since 2.0.1
 */
final class Loader
{
	/**
	 * The Load class
	 *
	 * @var    Load
	 * @since  2.0.1
	 */
	protected Load $load;

	/**
	 * The Watcher class
	 *
	 * @var    Watcher
	 * @since  2.0.1
	 */
	protected Watcher $watcher;

	/**
	 * The Plugin Params class
	 *
	 * @var    Registry
	 * @since  2.0.1
	 */
	protected Registry $plugin;

	/**
	 * Constructor
	 *
	 * @param Load           $load            The load object.
	 * @param Watcher      $watcher      The watcher object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Load $load,
		Watcher $watcher)
	{
		$this->load = $load;
		$this->watcher = $watcher;
	}

	/**
	 * The setting of Scripture into Content starts here
	 *
	 * @param   object      &$row     The article object.  Note $article->text is also available
	 * @param   Registry  $plugin  The plugin params
	 *
	 * @return  void
	 * @since   2.0.1
	 */
	public function set(&$row, Registry $plugin): bool
	{
		$this->plugin = $plugin;

		return;
	}
}

