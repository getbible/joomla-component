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
namespace TrueChristianBible\Component\GetBible\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use TrueChristianBible\Component\GetBible\Administrator\Helper\GetbibleHelper;
use TrueChristianBible\Joomla\Utilities\FileHelper;
use TrueChristianBible\Joomla\GetBible\Remote\Version;
use TrueChristianBible\Joomla\Github\Factory as GithubFactory;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible Ajax List Model
 *
 * @since  1.6
 */
class AjaxModel extends ListModel
{
	/**
	 * The component params.
	 *
	 * @var   Registry
	 * @since 3.2.0
	 */
	protected Registry $app_params;

	/**
	 * The application object.
	 *
	 * @var   CMSApplicationInterface  The application instance.
	 * @since 3.2.0
	 */
	protected CMSApplicationInterface $app;

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

		$this->app_params = ComponentHelper::getParams('com_getbible');
		$this->app ??= Factory::getApplication();
	}

	// Used in translation

	/**
	 * Check and if a notice is new (per/user)
	 *
	 * @param string|null    $notice   The current notice
	 *
	 * @return  bool  true if is new
	 * @since   2.0.0
	 */
	public function isNew(?string $notice): bool
	{
		// first get the file path
		$path_filename = FileHelper::getPath('path', 'usernotice', 'md', Factory::getUser()->username, JPATH_ADMINISTRATOR . '/components/com_getbible');

		// check if the file is set
		if (($content = FileHelper::getContent($path_filename, FALSE)) !== FALSE)
		{
			if ($notice == $content)
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Check if a notice has been read (per/user)
	 *
	 * @param string|null    $notice   The current notice
	 *
	 * @return  bool  true if is read
	 * @since   2.0.0
	 */
	public function isRead(?string $notice): bool
	{
		// first get the file path
		$path_filename = FileHelper::getPath('path', 'usernotice', 'md', Factory::getUser()->username, JPATH_ADMINISTRATOR . '/components/com_getbible');

		// set as read if not already set
		if (($content = FileHelper::getContent($path_filename, FALSE)) !== FALSE)
		{
			if ($notice == $content)
			{
				return true;
			}
		}

		return FileHelper::write($path_filename, $notice);
	}

	/**
	 * Get the current version notice.
	 *
	 * Compares the installed version of the component with the latest available
	 * version from the repository tags and returns an appropriate message.
	 *
	 * @param   string|null  $version  Optional version to compare if manifest version not found.
	 *
	 * @return  array  The array with 'notice' or 'error' and optional 'github-error' / 'gitea-error'.
	 * @since   2.3.0
	 * @since   5.1.1 Improved with support for pre-releases and intelligent tag grouping.
	 */
	public function getVersion(?string $version = null): array
	{
		return (new Version(
			'getBible', 'joomla-component',
			'[[[arg2]]]', '[[[arg3]]]'
		))->get($version);
	}

	/**
	 * Get the content of a GitHub wiki page.
	 *
	 * @param   string  $name  The name of the wiki page (default: 'Home').
	 *
	 * @return  array  Associative array with 'page' or 'error' key.
	 * @since   2.3.0
	 */
	public function getWiki(string $name = 'Home'): array
	{
		try {
			$wiki = GithubFactory::_('Github.Repository.Wiki')
				->get('getBible', 'support', $name);

			if (!empty($wiki->content)) {
				return ['page' => base64_decode($wiki->content)];
			}
		} catch (\Throwable $e) {
			return ['error' => $e->getMessage()];
		}

		return ['error' => Text::_('COM_GETBIBLE_THE_WIKI_CAN_ONLY_BE_LOADED_WHEN_YOUR_GETBIBLE_SYSTEM_HAS_INTERNET_CONNECTION')];
	}
}
