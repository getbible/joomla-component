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
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Helper\TagsHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\RouteHelper;
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\GetBible\Openai;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\JsonHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible Openai Item Model
 *
 * @since  1.6
 */
class OpenaiModel extends ItemModel
{
	/**
	 * Model context string.
	 *
	 * @var     string
	 * @since   1.6
	 */
	protected $_context = 'com_getbible.openai';

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
	 * A custom property for UI Kit components.
	 *
	 * @var   array|null  Property for storing UI Kit component-related data or objects.
	 * @since 3.2.0
	 */
	protected ?array $uikitComp;

	/**
	 * @var     object item
	 * @since   1.6
	 */
	protected $item;

	/**
	 * Constructor
	 *
	 * @param   array                 $config   An array of configuration options (name, state, dbo, table_path, ignore_request).
	 * @param   ?MVCFactoryInterface  $factory  The factory.
	 *
	 * @since   3.0
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function populateState()
	{
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
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{

		$pk = (!empty($pk)) ? $pk : (int) $this->getState('openai.id');

		$this->translation = $this->input->getString('t') ?? $this->input->getString('translation', Helper::getParams('com_getbible')->get('default_translation', 'kjv'));
		$this->guid = $pk =  $this->input->getString('guid');
		$this->book = $this->input->getInt('book', 0);
		$this->chapter = $this->input->getInt('chapter', 0);
		$this->verse = $this->input->getString('verse', '');

		// only continue if openai is activated
		if (Helper::getParams('com_getbible')->get('enable_open_ai') != 1)
		{
			$app = Factory::getApplication();
			// If no data is found redirect to default page and show warning.
			$app->enqueueMessage('The Open AI feature has not been activated. Please contact the system administrator of this website to resolve this.', 'error');
			$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
			return false;
		}
		// validate that we have a valid prompt and we have a book, chapter and verse
		elseif (empty($this->book) || empty($this->chapter) || empty($this->verse) || empty($this->guid) || ($abbreviation = GuidHelper::item($this->guid, 'prompt', 'a.abbreviation', 'getbible')) === null)
		{
			$app = Factory::getApplication();
			// If no data is found redirect to default page and show warning.
			$app->enqueueMessage('There has been an error!', 'error');
			$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
			return false;
		}
		// validate that we have the correct translation
		elseif ($abbreviation !== 'all' && $abbreviation !== $this->translation)
		{
			$app = Factory::getApplication();
			// If no data is found redirect to default page and show warning.
			$app->enqueueMessage('There has been an error: mismatch!', 'error');
			$app->redirect(JRoute::_('index.php?option=com_getbible&view=app'));
			return false;
		}

		if ($this->_item === null)
		{
			$this->_item = [];
		}

		if (!isset($this->_item[$pk]))
		{
			try
			{
				// Get a db connection.
				$db = $this->getDatabase();

				// Create a new query object.
				$query = $db->getQuery(true);

				// Get data
				try
				{
					$data = Openai::_('GetBible.AI')->get();
				}
				catch (\DomainException $e)
				{
					// If no data is found redirect to default page and show warning.
					$this->app->enqueueMessage($e->getMessage(), 'error');
					$this->app->redirect(Route::_('index.php?option=com_getbible&view=app'));
					return false;
				}
				catch (\InvalidArgumentException $e)
				{
					// If no data is found redirect to default page and show warning.
					$this->app->enqueueMessage($e->getMessage(), 'error');
					$this->app->redirect(Route::_('index.php?option=com_getbible&view=app'));
					return false;
				}
				catch (\Exception $e)
				{
					// If no data is found redirect to default page and show warning.
					$this->app->enqueueMessage($e->getMessage(), 'error');
					$this->app->redirect(Route::_('index.php?option=com_getbible&view=app'));
					return false;
				}

				if (empty($data))
				{
					$app = Factory::getApplication();
					// If no data is found redirect to default page and show warning.
					$app->enqueueMessage(Text::_('COM_GETBIBLE_NOT_FOUND_OR_ACCESS_DENIED'), 'warning');
					$app->redirect(Route::_('index.php?option=com_getbible&view=app'));
					return false;
				}

				// set data object to item.
				$this->_item[$pk] = $data;
			}
			catch (\Exception $e)
			{
				if ($e->getCode() == 404)
				{
					// Need to go thru the error handler to allow Redirect to work.
					throw $e;
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
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.openai.distribution_about', &$_distribution_about, &$params, 0));
		// Make sure the content prepare plugins fire on distribution_license
		$_distribution_license = new \stdClass();
		$_distribution_license->text =& $data->distribution_license; // value must be in text
		// Since all values are now in text (Joomla Limitation), we also add the field name (distribution_license) to context
		$this->_dispatcher->triggerEvent("onContentPrepare", array('com_getbible.openai.distribution_license', &$_distribution_license, &$params, 0));

		// return data object.
		return $data;
	}
}
