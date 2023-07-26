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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\GetBible\Factory;

/**
 * Getbible List Model for Tag
 */
class GetbibleModelTag extends ListModel
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
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->initSet = true; 
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tagged_verse as a
		$query->select($db->quoteName(
			array('a.id','a.book_nr','a.chapter','a.verse','a.access','a.linker','a.guid'),
			array('id','book_nr','chapter','verse','access','linker','guid')));
		$query->from($db->quoteName('#__getbible_tagged_verse', 'a'));

		// Get from #__getbible_verse as v
		$query->select($db->quoteName(
			array('v.text'),
			array('text')));
		$query->join('LEFT', ($db->quoteName('#__getbible_verse', 'v')) . ' ON (' . $db->quoteName('a.verse') . ' = ' . $db->quoteName('v.verse') . ')');

		// Get from #__getbible_book as b
		$query->select($db->quoteName(
			array('b.name'),
			array('name')));
		$query->join('LEFT', ($db->quoteName('#__getbible_book', 'b')) . ' ON (' . $db->quoteName('v.book_nr') . ' = ' . $db->quoteName('b.nr') . ')');

		// Get from #__getbible_tag as t
		$query->select($db->quoteName(
			array('t.guid'),
			array('tag')));
		$query->join('LEFT', ($db->quoteName('#__getbible_tag', 't')) . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('t.guid') . ')');
		// Check if $this->tag is a string or numeric value.
		$checkValue = $this->tag;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('a.tag = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.tag = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('v.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('v.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('b.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('b.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.published is 1
		$query->where('a.published = 1');
		// Get where a.access is 1
		$query->where('a.access = 1');
		// Get where v.published is 1
		$query->where('v.published = 1');
		// Get where t.published is 1
		$query->where('t.published = 1');
		// Get where v.book_nr is a.book_nr
		$query->where('v.book_nr = a.book_nr');
		// Get where v.chapter is a.chapter
		$query->where('v.chapter = a.chapter');
		// Get where v.verse is a.verse
		$query->where('v.verse = a.verse');
		$query->order('a.book_nr ASC');
		$query->order('a.chapter ASC');
		$query->order('a.verse ASC');

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
		$user = JFactory::getUser();


		$this->input ??= JFactory::getApplication()->input;

		$this->translation = $this->input->getString('t') ?? $this->input->getString('version') ?? $this->input->getString('translation', 'kjv') ;
		$this->tag = $this->input->getString('guid') ?? '';

		if (!GuidHelper::valid($this->tag))
		{
			return false;
		}
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);

		// Insure all item fields are adapted where needed.
		if (GetbibleHelper::checkArray($items))
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JFactory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && GetbibleHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on text
				$_text = new stdClass();
				$_text->text =& $item->text; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (text) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.text', &$_text, &$params, 0));
			}
		}

		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getTranslation()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.distribution_abbreviation','a.distribution_versification','a.distribution_version','a.distribution_version_date','a.distribution_lcsh','a.encoding','a.sha','a.language','a.lang','a.distribution_sourcetype','a.distribution_source','a.distribution_license','a.distribution_about','a.distribution_history','a.translation','a.abbreviation','a.direction'),
			array('distribution_abbreviation','distribution_versification','distribution_version','distribution_version_date','distribution_lcsh','encoding','sha','language','lang','distribution_sourcetype','distribution_source','distribution_license','distribution_about','distribution_history','translation','abbreviation','direction')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('a.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a stdClass object.
		$data = $db->loadObject();

		if (empty($data))
		{
			return false;
		}
	// Load the JEvent Dispatcher
	JPluginHelper::importPlugin('content');
	$this->_dispatcher = JFactory::getApplication();
		// Check if we can decode distribution_history
		if (GetbibleHelper::checkJson($data->distribution_history))
		{
			// Decode distribution_history
			$data->distribution_history = json_decode($data->distribution_history, true);
		}
		// Check if item has params, or pass whole item.
		$params = (isset($data->params) && GetbibleHelper::checkJson($data->params)) ? json_decode($data->params) : $data;
		// Make sure the content prepare plugins fire on distribution_about
		$_distribution_about = new stdClass();
		$_distribution_about->text =& $data->distribution_about; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_about) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new stdClass();
		$_distribution_license->text =& $data->distribution_license; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_license) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.distribution_license', &$_distribution_license, &$params, 0));

		// return data object.
		return $data;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getTags()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.description','a.guid'),
			array('id','name','description','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Get where a.access is 1
		$query->where('a.access = 1');
		// Get where a.published is 1
		$query->where('a.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (GetbibleHelper::checkArray($items))
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JFactory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && GetbibleHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.description', &$_description, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getLinkerTags()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.linker','a.name','a.description','a.published','a.guid'),
			array('id','linker','name','description','published','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Check if Factory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = Factory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('a.linker = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.linker = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 0
		$query->where('a.access = 0');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (GetbibleHelper::checkArray($items))
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JFactory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && GetbibleHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.description', &$_description, &$params, 0));
			}
		}
		// return items
		return $items;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getTag()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.description','a.guid'),
			array('id','name','description','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Check if $this->tag is a string or numeric value.
		$checkValue = $this->tag;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('a.guid = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.guid = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.published is 1
		$query->where('a.published = 1');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		// Load the results as a stdClass object.
		$data = $db->loadObject();

		if (empty($data))
		{
			return false;
		}
	// Load the JEvent Dispatcher
	JPluginHelper::importPlugin('content');
	$this->_dispatcher = JFactory::getApplication();
		// Check if item has params, or pass whole item.
		$params = (isset($data->params) && GetbibleHelper::checkJson($data->params)) ? json_decode($data->params) : $data;
		// Make sure the content prepare plugins fire on description
		$_description = new stdClass();
		$_description->text =& $data->description; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.description', &$_description, &$params, 0));

		// return data object.
		return $data;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  An array of objects on success, false on failure.
	 *
	 */
	public function getLinkerTagged()
	{

		if (!isset($this->initSet) || !$this->initSet)
		{
			$this->user = JFactory::getUser();
			$this->userId = $this->user->get('id');
			$this->guest = $this->user->get('guest');
			$this->groups = $this->user->get('groups');
			$this->authorisedGroups = $this->user->getAuthorisedGroups();
			$this->levels = $this->user->getAuthorisedViewLevels();
			$this->initSet = true;
		}

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tagged_verse as a
		$query->select($db->quoteName(
			array('a.id','a.book_nr','a.chapter','a.verse','a.access','a.published','a.linker','a.guid'),
			array('id','book_nr','chapter','verse','access','published','linker','guid')));
		$query->from($db->quoteName('#__getbible_tagged_verse', 'a'));

		// Get from #__getbible_verse as v
		$query->select($db->quoteName(
			array('v.text'),
			array('text')));
		$query->join('LEFT', ($db->quoteName('#__getbible_verse', 'v')) . ' ON (' . $db->quoteName('a.verse') . ' = ' . $db->quoteName('v.verse') . ')');

		// Get from #__getbible_book as b
		$query->select($db->quoteName(
			array('b.name'),
			array('name')));
		$query->join('LEFT', ($db->quoteName('#__getbible_book', 'b')) . ' ON (' . $db->quoteName('v.book_nr') . ' = ' . $db->quoteName('b.nr') . ')');

		// Get from #__getbible_tag as t
		$query->select($db->quoteName(
			array('t.guid'),
			array('tag')));
		$query->join('LEFT', ($db->quoteName('#__getbible_tag', 't')) . ' ON (' . $db->quoteName('a.tag') . ' = ' . $db->quoteName('t.guid') . ')');
		// Check if Factory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = Factory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('a.linker = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.linker = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->tag is a string or numeric value.
		$checkValue = $this->tag;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('a.tag = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('a.tag = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('v.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('v.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && GetbibleHelper::checkString($checkValue))
		{
			$query->where('b.abbreviation = ' . $db->quote($checkValue));
		}
		elseif (is_numeric($checkValue))
		{
			$query->where('b.abbreviation = ' . $checkValue);
		}
		else
		{
			return false;
		}
		// Get where a.access is 0
		$query->where('a.access = 0');
		// Get where v.published is 1
		$query->where('v.published = 1');
		// Get where t.published is 1
		$query->where('t.published = 1');
		// Get where v.book_nr is a.book_nr
		$query->where('v.book_nr = a.book_nr');
		// Get where v.chapter is a.chapter
		$query->where('v.chapter = a.chapter');
		// Get where v.verse is a.verse
		$query->where('v.verse = a.verse');
		$query->order('a.book_nr ASC');
		$query->order('a.chapter ASC');
		$query->order('a.verse ASC');
		$query->group('a.book_nr');
		$query->group('a.chapter');
		$query->group('a.verse');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (GetbibleHelper::checkArray($items))
		{
			// Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JFactory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && GetbibleHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on text
				$_text = new stdClass();
				$_text->text =& $item->text; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (text) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.text', &$_text, &$params, 0));
			}
		}
		// return items
		return $items;
	}
}
