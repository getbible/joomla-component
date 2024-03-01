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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\RouteHelper;
use Joomla\CMS\Helper\TagsHelper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\JsonHelper;
use VDM\Joomla\GetBible\Factory as GetBibleFactory;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible List Model for Tag
 *
 * @since  1.6
 */
class TagModel extends ListModel
{
	/**
	 * Represents the current user object.
	 *
	 * @var   User  The user object representing the current user.
	 * @since 3.2.0
	 */
	protected User $user;

	/**
	 * The unique identifier of the current user.
	 *
	 * @var   int|null  The ID of the current user.
	 * @since 3.2.0
	 */
	protected ?int $userId;

	/**
	 * Flag indicating whether the current user is a guest.
	 *
	 * @var   int  1 if the user is a guest, 0 otherwise.
	 * @since 3.2.0
	 */
	protected int $guest;

	/**
	 * An array of groups that the current user belongs to.
	 *
	 * @var   array|null  An array of user group IDs.
	 * @since 3.2.0
	 */
	protected ?array $groups;

	/**
	 * An array of view access levels for the current user.
	 *
	 * @var   array|null  An array of access level IDs.
	 * @since 3.2.0
	 */
	protected ?array $levels;

	/**
	 * The application object.
	 *
	 * @var   CMSApplicationInterface  The application instance.
	 * @since 3.2.0
	 */
	protected CMSApplicationInterface $app;

	/**
	 * The input object, providing access to the request data.
	 *
	 * @var   Input  The input object.
	 * @since 3.2.0
	 */
	protected Input $input;

	/**
	 * The styles array.
	 *
	 * @var    array
	 * @since  4.3
	 */
	protected array $styles = [
		'components/com_getbible/assets/css/site.css',
		'components/com_getbible/assets/css/tag.css'
 	];

	/**
	 * The scripts array.
	 *
	 * @var    array
	 * @since  4.3
	 */
	protected array $scripts = [
		'components/com_getbible/assets/js/site.js'
 	];

	/**
	 * A custom property for UIKit components. (not used unless you load v2)
	 */
	protected $uikitComp;

	/**
	 * Constructor
	 *
	 * @param   array                 $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   ?MVCFactoryInterface  $factory  The factory.
	 *
	 * @since   1.6
	 * @throws  \Exception
	 */
	public function __construct($config = [], MVCFactoryInterface $factory = null)
	{
		parent::__construct($config, $factory);

		$this->app ??= Factory::getApplication();
		$this->input ??= $this->app->getInput();

		// Set the current user for authorisation checks (for those calling this model directly)
		$this->user ??= $this->getCurrentUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();

		// will be removed
		$this->initSet = true;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return   string  An SQL query
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Make sure all records load, since no pagination allowed.
		$this->setState('list.limit', 0);
		// Get a db connection.
		$db = $this->getDatabase();

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
		if (isset($checkValue) && StringHelper::check($checkValue))
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
		if (isset($checkValue) && StringHelper::check($checkValue))
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
		// Get where b.abbreviation is v.abbreviation
		$query->where('b.abbreviation = v.abbreviation');
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
	 * @since   1.6
	 */
	public function getItems()
	{
		$user = $this->user;


		$this->input ??= Factory::getApplication()->input;

		$this->translation = $this->input->getString('t') ?? $this->input->getString('translation', Helper::getParams('com_getbible')->get('default_translation', 'kjv')) ;
		$this->tag = $this->input->getString('guid') ?? '';

		if (!GuidHelper::valid($this->tag))
		{
			return false;
		}
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on text
				$_text = new \stdClass();
				$_text->text =& $item->text; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (text) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.text', &$_text, &$params, 0));
			}
		}

		// return items
		return $items;
	}

	/**
	 * Method to get the styles that have to be included on the view
	 *
	 * @return  array    styles files
	 * @since   4.3
	 */
	public function getStyles(): array
	{
		return $this->styles;
	}

	/**
	 * Method to set the styles that have to be included on the view
	 *
	 * @return  void
	 * @since   4.3
	 */
	public function setStyles(string $path): void
	{
		$this->styles[] = $path;
	}

	/**
	 * Method to get the script that have to be included on the view
	 *
	 * @return  array    script files
	 * @since   4.3
	 */
	public function getScripts(): array
	{
		return $this->scripts;
	}

	/**
	 * Method to set the script that have to be included on the view
	 *
	 * @return  void
	 * @since   4.3
	 */
	public function setScript(string $path): void
	{
		$this->scripts[] = $path;
	}

	/**
	 * Custom Method
	 *
	 * @return mixed  item data object on success, false on failure.
	 *
	 */
	public function getTranslation()
	{
		// Get a db connection.
		$db = $this->getDatabase();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_translation as a
		$query->select($db->quoteName(
			array('a.distribution_abbreviation','a.distribution_versification','a.distribution_version','a.distribution_version_date','a.distribution_lcsh','a.encoding','a.sha','a.language','a.lang','a.distribution_sourcetype','a.distribution_source','a.distribution_license','a.distribution_about','a.distribution_history','a.translation','a.abbreviation','a.direction'),
			array('distribution_abbreviation','distribution_versification','distribution_version','distribution_version_date','distribution_lcsh','encoding','sha','language','lang','distribution_sourcetype','distribution_source','distribution_license','distribution_about','distribution_history','translation','abbreviation','direction')));
		$query->from($db->quoteName('#__getbible_translation', 'a'));
		// Check if $this->translation is a string or numeric value.
		$checkValue = $this->translation;
		if (isset($checkValue) && StringHelper::check($checkValue))
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
	PluginHelper::importPlugin('content');
	$this->_dispatcher = Factory::getApplication();
		// Check if we can decode distribution_history
		if (isset($data->distribution_history) && JsonHelper::check($data->distribution_history))
		{
			// Decode distribution_history
			$data->distribution_history = json_decode($data->distribution_history, true);
		}
		// Check if item has params, or pass whole item.
		$params = (isset($data->params) && JsonHelper::check($data->params)) ? json_decode($data->params) : $data;
		// Make sure the content prepare plugins fire on distribution_about
		$_distribution_about = new \stdClass();
		$_distribution_about->text =& $data->distribution_about; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_about) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new \stdClass();
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

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = $this->getDatabase();

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
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new \stdClass();
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

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = $this->getDatabase();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.linker','a.name','a.description','a.published','a.guid'),
			array('id','linker','name','description','published','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Check if GetBibleFactory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = GetBibleFactory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && StringHelper::check($checkValue))
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
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on description
				$_description = new \stdClass();
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
		// Get a db connection.
		$db = $this->getDatabase();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Get from #__getbible_tag as a
		$query->select($db->quoteName(
			array('a.id','a.name','a.description','a.guid'),
			array('id','name','description','guid')));
		$query->from($db->quoteName('#__getbible_tag', 'a'));
		// Check if $this->tag is a string or numeric value.
		$checkValue = $this->tag;
		if (isset($checkValue) && StringHelper::check($checkValue))
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
	PluginHelper::importPlugin('content');
	$this->_dispatcher = Factory::getApplication();
		// Check if item has params, or pass whole item.
		$params = (isset($data->params) && JsonHelper::check($data->params)) ? json_decode($data->params) : $data;
		// Make sure the content prepare plugins fire on description
		$_description = new \stdClass();
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

		// Get the global params
		$globalParams = ComponentHelper::getParams('com_getbible', true);
		// Get a db connection.
		$db = $this->getDatabase();

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
		// Check if GetBibleFactory::_('GetBible.Linker')->active(true) is a string or numeric value.
		$checkValue = GetBibleFactory::_('GetBible.Linker')->active(true);
		if (isset($checkValue) && StringHelper::check($checkValue))
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
		if (isset($checkValue) && StringHelper::check($checkValue))
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
		if (isset($checkValue) && StringHelper::check($checkValue))
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
		if (isset($checkValue) && StringHelper::check($checkValue))
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

		// Reset the query using our newly populated query object.
		$db->setQuery($query);
		$items = $db->loadObjectList();

		if (empty($items))
		{
			return false;
		}

		// Insure all item fields are adapted where needed.
		if (UtilitiesArrayHelper::check($items))
		{
			// Load the JEvent Dispatcher
			PluginHelper::importPlugin('content');
			$this->_dispatcher = Factory::getApplication();
			foreach ($items as $nr => &$item)
			{
				// Always create a slug for sef URL's
				$item->slug = ($item->id ?? '0') . (isset($item->alias) ? ':' . $item->alias : '');
				// Check if item has params, or pass whole item.
				$params = (isset($item->params) && JsonHelper::check($item->params)) ? json_decode($item->params) : $item;
				// Make sure the content prepare plugins fire on text
				$_text = new \stdClass();
				$_text->text =& $item->text; // value must be in text
				// Since all values are now in text (Joomla Limitation), we also add the field name (text) to context
				$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.tag.text', &$_text, &$params, 0));
			}
		}
		// return items
		return $items;
	}
}
