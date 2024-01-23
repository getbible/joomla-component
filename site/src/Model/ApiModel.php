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
namespace TrueChristianChurch\Component\Getbible\Site\Model;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;

/**
 * Getbible List Model for Api
 */
class ApiModel extends ListModel
{
	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user = $this->getCurrentUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->app = Factory::getApplication();
		$this->input = $this->app->input;
		$this->initSet = true; 
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = $this->getDatabase();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.id','a.abbr','a.language','a.name'),
			array('id','abbr','language','name')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		// Get where a.published is 1
		$query->where('a.published = 1');
		$query->order('a.language ASC');
		$query->order('a.name ASC');

		// return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$user = Factory::getApplication()->getIdentity();
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
			}
		}

		// return items
		return $items;
	}
}
