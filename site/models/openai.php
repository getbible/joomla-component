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

use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\GetBible\Openai;

/**
 * Getbible Openai Item Model
 */
class GetbibleModelOpenai extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var        string
	 */
	protected $_context = 'com_getbible.openai';

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
	 * @var object item
	 */
	protected $item;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since   1.6
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		// Get the itme main id
		$id = $this->input->getInt('id', null);
		$this->setState('openai.id', $id);

		// Load the parameters.
		$params = $this->app->getParams();
		$this->setState('params', $params);
		parent::populateState();
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $pk  The id of the article.
	 *
	 * @return  mixed  Menu item data object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->initSet = true;

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('openai.id');

		$this->translation = $this->input->getString('t') ?? $this->input->getString('translation', Helper::getParams('com_getbible')->get('default_translation', 'kjv'));
		$this->guid = $pk =  $this->input->getString('guid');
		$this->book = $this->input->getInt('book', 0);
		$this->chapter = $this->input->getInt('chapter', 0);
		$this->verse = $this->input->getString('verse', '');

		// only continue if openai is activated
		if (Helper::getParams('com_getbible')->get('enable_open_ai') != 1)
		{
			$app = JFactory::getApplication();
			// If no data is found redirect to default page and show warning.
			$app->enqueueMessage('The Open AI feature has not been activated. Please contact the system administrator of this website to resolve this.', 'error');
			$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
			return false;
		}
		// validate that we have a valid prompt and we have a book, chapter and verse
		elseif (empty($this->book) || empty($this->chapter) || empty($this->verse) || empty($this->guid) || ($abbreviation = GuidHelper::item($this->guid, 'prompt', 'a.abbreviation', 'getbible')) === null)
		{
			$app = JFactory::getApplication();
			// If no data is found redirect to default page and show warning.
			$app->enqueueMessage('There has been an error!', 'error');
			$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
			return false;
		}
		// validate that we have the correct translation
		elseif ($abbreviation !== 'all' && $abbreviation !== $this->translation)
		{
			$app = JFactory::getApplication();
			// If no data is found redirect to default page and show warning.
			$app->enqueueMessage('There has been an error: mismatch!', 'error');
			$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
			return false;
		}
		
		if ($this->_item === null)
		{
			$this->_item = array();
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				// Get a db connection.
				$db = JFactory::getDbo();

				// Create a new query object.
				$query = $db->getQuery(true);

				// Get data
				try
				{
					$data = Openai::_('GetBible.AI')->get();
				}
				catch (DomainException $e)
				{
					$app = JFactory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage($e->getMessage(), 'error');
					$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
					return false;
				}
				catch (InvalidArgumentException $e)
				{
					$app = JFactory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage($e->getMessage(), 'error');
					$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
					return false;
				}
				catch (Exception $e)
				{
					$app = JFactory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage($e->getMessage(), 'error');
					$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
					return false;
				}

				if (empty($data))
				{
					$app = JFactory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage(JText::_('COM_GETBIBLE_NOT_FOUND_OR_ACCESS_DENIED'), 'warning');
					$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
					return false;
				}

				// set data object to item.
				$this->_item[$pk] = $data;
			}
			catch (Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseWarning(404, $e->getMessage());
				}
				else
				{
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
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
		if (isset($data->distribution_history) && GetbibleHelper::checkJson($data->distribution_history))
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
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.openai.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new stdClass();
		$_distribution_license->text =& $data->distribution_license; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_license) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.openai.distribution_license', &$_distribution_license, &$params, 0));

		// return data object.
		return $data;
	}
}
