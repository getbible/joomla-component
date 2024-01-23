<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.net>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/
namespace TrueChristianChurch\Component\Getbible\Site\Service;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

/**
 * Routing class from com_getbible
 *
 * @since  3.10
 */
class Router extends RouterView
{
	/**
	 * Flag to remove IDs
	 *
	 * @var  boolean
	 */
	protected $noIDs = false;

	/**
	 * The category factory
	 *
	 * @var   CategoryFactoryInterface
	 *
	 * @since  4.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var  array
	 *
	 * @since  4.0.0
	 */
	private $categoryCache = [];

	/**
	 * The db
	 *
	 * @var  DatabaseInterface
	 *
	 * @since  4.0.0
	 */
	private $db;

	/**
	 * Getbible Component router constructor
	 *
	 * @param   SiteApplication           $app               The application object
	 * @param   AbstractMenu              $menu              The menu object to work with
	 * @param   CategoryFactoryInterface  $categoryFactory   The category object
	 * @param   DatabaseInterface         $db                The database object
	 */
	public function __construct(
		SiteApplication $app,
		AbstractMenu $menu,
		CategoryFactoryInterface $categoryFactory,
		DatabaseInterface $db)
	{
		$this->categoryFactory = $categoryFactory;
		$this->db              = $db;###ROUTER_BUILD_VIEWS_CONF###

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}###ROUTER_BUILD_VIEWS_SEGMENT_ID###
}
