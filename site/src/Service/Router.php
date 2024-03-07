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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Router class for the Get Bible Component
 *
 * @since  3.10
 */
class Router extends RouterView
{
	/**
	 * Flag to remove IDs
	 *
	 * @var    boolean
	 * @since  4.0.0
	 */
	protected $noIDs = false;

	/**
	 * The category factory
	 *
	 * @var    CategoryFactoryInterface
	 * @since  4.0.0
	 */
	private $categoryFactory;

	/**
	 * The category cache
	 *
	 * @var    array
	 * @since  4.0.0
	 */
	private $categoryCache = [];

	/**
	 * The db
	 *
	 * @var    DatabaseInterface
	 * @since  4.0.0
	 */
	private $db;

	/**
	 * The component params
	 *
	 * @var    Registry
	 * @since  4.0.0
	 */
	private $params;

	/**
	 * Getbible Component router constructor
	 *
	 * @param   SiteApplication           $app               The application object
	 * @param   AbstractMenu              $menu              The menu object to work with
	 * @param   CategoryFactoryInterface  $categoryFactory   The category object
	 * @param   DatabaseInterface         $db                The database object
	 *
	 * @since   4.0.0
	 */
	public function __construct(
		SiteApplication $app,
		AbstractMenu $menu,
		CategoryFactoryInterface $categoryFactory,
		DatabaseInterface $db)
	{
		$this->categoryFactory = $categoryFactory;
		$this->db              = $db;
		$this->params          = ComponentHelper::getParams('com_getbible');
		$this->noIDs           = (bool) $this->params->get('sef_ids', false);

		// Add the (app:view) router configuration
		$app = new RouterViewConfiguration('app');
		$this->registerView($app);

		// Add the (tag:view) router configuration
		$tag = new RouterViewConfiguration('tag');
		$this->registerView($tag);

		// Add the (search:view) router configuration
		$search = new RouterViewConfiguration('search');
		$this->registerView($search);

		// Add the (openai:view) router configuration
		$openai = new RouterViewConfiguration('openai');
		$this->registerView($openai);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}
}
