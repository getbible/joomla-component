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
namespace TrueChristianChurch\Component\Getbible\Site\Helper;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Categories\Categories;
use VDM\Joomla\Utilities\ArrayHelper;

/**
 * Getbible Component Route Helper
 *
 * @static
 * @since       1.5
 */
abstract class RouteHelper
{
	protected static $lookup;

	/**
	 * @param int The route of the App
	 */
	public static function getAppRoute($id = 0, $catid = 0)
	{
		if ($id > 0)
		{
			// Initialize the needel array.
			$needles = array(
				'app'  => array((int) $id)
			);
			// Create the link
			$link = 'index.php?option=com_getbible&view=app&id='. $id;
		}
		else
		{
			// Initialize the needel array.
			$needles = array(
				'app'  => array()
			);
			// Create the link but don't add the id.
			$link = 'index.php?option=com_getbible&view=app';
		}
		if ($catid > 1)
		{
			$categories = Categories::getInstance('getbible.app');
			$category = $categories->get($catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles, 'app'))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param int The route of the Tag
	 */
	public static function getTagRoute($id = 0, $catid = 0)
	{
		if ($id > 0)
		{
			// Initialize the needel array.
			$needles = array(
				'tag'  => array((int) $id)
			);
			// Create the link
			$link = 'index.php?option=com_getbible&view=tag&id='. $id;
		}
		else
		{
			// Initialize the needel array.
			$needles = array(
				'tag'  => array()
			);
			// Create the link but don't add the id.
			$link = 'index.php?option=com_getbible&view=tag';
		}
		if ($catid > 1)
		{
			$categories = Categories::getInstance('getbible.tag');
			$category = $categories->get($catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param int The route of the Search
	 */
	public static function getSearchRoute($id = 0, $catid = 0)
	{
		if ($id > 0)
		{
			// Initialize the needel array.
			$needles = array(
				'search'  => array((int) $id)
			);
			// Create the link
			$link = 'index.php?option=com_getbible&view=search&id='. $id;
		}
		else
		{
			// Initialize the needel array.
			$needles = array(
				'search'  => array()
			);
			// Create the link but don't add the id.
			$link = 'index.php?option=com_getbible&view=search';
		}
		if ($catid > 1)
		{
			$categories = Categories::getInstance('getbible.search');
			$category = $categories->get($catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param int The route of the Openai
	 */
	public static function getOpenaiRoute($id = 0, $catid = 0)
	{
		if ($id > 0)
		{
			// Initialize the needel array.
			$needles = array(
				'openai'  => array((int) $id)
			);
			// Create the link
			$link = 'index.php?option=com_getbible&view=openai&id='. $id;
		}
		else
		{
			// Initialize the needel array.
			$needles = array(
				'openai'  => array()
			);
			// Create the link but don't add the id.
			$link = 'index.php?option=com_getbible&view=openai';
		}
		if ($catid > 1)
		{
			$categories = Categories::getInstance('getbible.openai');
			$category = $categories->get($catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * @param int The route of the Api
	 */
	public static function getApiRoute($id = 0, $catid = 0)
	{
		if ($id > 0)
		{
			// Initialize the needel array.
			$needles = array(
				'api'  => array((int) $id)
			);
			// Create the link
			$link = 'index.php?option=com_getbible&view=api&id='. $id;
		}
		else
		{
			// Initialize the needel array.
			$needles = array(
				'api'  => array()
			);
			// Create the link but don't add the id.
			$link = 'index.php?option=com_getbible&view=api';
		}
		if ($catid > 1)
		{
			$categories = Categories::getInstance('getbible.api');
			$category = $categories->get($catid);
			if ($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	protected static function _findItem($needles = null,$type = null)
	{
		$app      = Factory::getApplication();
		$menus    = $app->getMenu('site');
		$language = isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = [];

			$component  = ComponentHelper::getComponent('com_getbible');

			$attributes = array('component_id');
			$values     = array($component->id);

			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[]     = array($needles['language'], '*');
			}

			$items = $menus->getItems($attributes, $values);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = [];
					}

					if (isset($item->query['id']))
					{
						/**
						 * Here it will become a bit tricky
						 * language != * can override existing entries
						 * language == * cannot override existing entries
						 */
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
						{
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
					else
					{
						self::$lookup[$language][$view][0] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					if (ArrayHelper::check($ids))
					{
						foreach ($ids as $id)
						{
							if (isset(self::$lookup[$language][$view][(int) $id]))
							{
								return self::$lookup[$language][$view][(int) $id];
							}
						}
					}
					elseif (isset(self::$lookup[$language][$view][0]))
					{
						return self::$lookup[$language][$view][0];
					}
				}
			}
		}

		if ($type)
		{
			// Check if the global menu item has been set.
			$params = ComponentHelper::getParams('com_getbible');
			if ($item = $params->get($type.'_menu', 0))
			{
				return $item;
			}
		}

		// Check if the active menuitem matches the requested language
		$active = $menus->getActive();

		if ($active
			&& $active->component == 'com_getbible'
			&& ($language == '*' || in_array($active->language, array('*', $language)) || !Multilanguage::isEnabled()))
		{
			return $active->id;
		}

		// If not found, return language specific home link
		$default = $menus->getDefault($language);

		return !empty($default->id) ? $default->id : null;
	}
}
