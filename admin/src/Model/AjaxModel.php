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
namespace TrueChristianChurch\Component\Getbible\Administrator\Model;

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
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\FileHelper;
use VDM\Joomla\Gitea\Factory as GiteaFactory;

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
		$path_filename = FileHelper::getPath('path', 'usernotice', 'md', Factory::getUser()->username, JPATH_COMPONENT_ADMINISTRATOR);

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
		$path_filename = FileHelper::getPath('path', 'usernotice', 'md', Factory::getUser()->username, JPATH_COMPONENT_ADMINISTRATOR);

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
	 * get Current Version
	 *
	 * @param   string|null  $message  The error messages if any.
	 *
	 * @return  array  The array of the notice or error message
	 * @since   2.3.0
	 */
	public function getVersion($version = null)
	{
		try
		{
			// get the repository tags
			$tags = GiteaFactory::_('Gitea.Repository.Tags')->list('getBible', 'joomla-component');
		}
		catch (DomainException $e)
		{
			return $this->getTokenForVersion($e->getMessage());
		}
		catch (InvalidArgumentException $e)
		{
			return $this->getTokenForVersion($e->getMessage());
		}
		catch (Exception $e)
		{
			return $this->getTokenForVersion($e->getMessage());
		}
		// do we have tags returned
		if (isset($tags[0]) && isset($tags[0]->name))
		{
			// get the version
			$manifest = GetbibleHelper::manifest();
			$local_version = (string) $manifest->version;
			$current_version = trim($tags[0]->name, 'vV');

			// now check if this version is out dated
			if ($current_version === $local_version)
			{
				return ['notice' => '<small><span style="color:green;"><span class="icon-shield"></span>&nbsp;' . Text::_('COM_GETBIBLE_UP_TO_DATE') . '</span></small>'];
			}
			else
			{
				// check if this is beta version
				$current_array = array_map(function ($v) { return (int) $v; }, (array) explode('.', $current_version));
				$local_array = array_map(function ($v) { return (int) $v; }, (array) explode('.', $local_version));
				if (($local_array[0] > $current_array[0]) || 
					($local_array[0] == $current_array[0] && $local_array[1] > $current_array[1]) || 
					($local_array[0] == $current_array[0] && $local_array[1] == $current_array[1] && $local_array[2] > $current_array[2]))
				{
					return ['notice' => '<small><span style="color:#F7B033;"><span class="icon-wrench"></span>&nbsp;' . Text::_('COM_GETBIBLE_PRE_RELEASE') . '</span></small>'];
				}
				else
				{
					// download link of the latest version
					$download = "https://git.vdm.dev/api/v1/repos/getBible/joomla-component/archive/" . $tags[0]->name . ".zip";

					return ['notice' => '<small><span style="color:red;"><span class="icon-warning-circle"></span>&nbsp;' . Text::_('COM_GETBIBLE_OUT_OF_DATE') . '!</span> <a style="color:green;"  href="' .
						$download . '" title="' . Text::_('COM_GETBIBLE_YOU_CAN_DIRECTLY_DOWNLOAD_THE_LATEST_UPDATE_OR_USE_THE_JOOMLA_UPDATE_AREA') . '">' . Text::_('COM_GETBIBLE_DOWNLOAD_UPDATE') . '!</a></small>'];
				}
			}
		}

		return $this->getTokenForVersion();
	}

	/**
	 * Instructions to get Token for version
	 *
	 * @param   string|null  $message  The error messages if any.
	 *
	 * @return  array  The array of the error message
	 * @since   2.3.0
	 */
	protected function getTokenForVersion(?string $message = null): array
	{
		// the URL
		$url = 'https://git.vdm.dev/user/settings/applications';

		// create link
		$a = '<small><a style="color:#F7B033;" href="' . $url . '" title="';
		$a_ = '">';
		$_a = '</a></small>';

		if ($message)
		{
			return ['error' => $a . $message . $a_ . Text::_('COM_GETBIBLE_GET_TOKEN') . $_a];
		}

		return ['error' =>  $a . Text::_('COM_GETBIBLE_GET_TOKEN_FROM_VDM_TO_GET_UPDATE_NOTICE_AND_ADD_IT_TO_YOUR_GLOBAL_OPTIONS') . $a_ . Text::_('COM_GETBIBLE_GET_TOKEN') . $_a];
	}

	/**
	 * get Wiki Page
	 *
	 * @param   string|null  $message  The error messages if any.
	 *
	 * @return  array  The array of the page or error message
	 * @since   2.3.0
	 */
	public function getWiki(string $name = 'Home'): array
	{
		try
		{
			// get the gitea wiki page im markdown
			$wiki = GiteaFactory::_('Gitea.Repository.Wiki')->get('getBible', 'support', $name);

			// now render the page in HTML
			$page = $wiki->content ?? null;
		}
		catch (\DomainException $e)
		{
			return $this->getTokenForWiki($e->getMessage());
		}
		catch (\InvalidArgumentException $e)
		{
			return $this->getTokenForWiki($e->getMessage());
		}
		catch (\Exception $e)
		{
			return $this->getTokenForWiki($e->getMessage());
		}

		// get the html
		if (isset($page))
		{
			return ['page' => $page];
		}

		return $this->getTokenForWiki();
	}

	/**
	 * Instructions to get Token for wiki
	 *
	 * @param   string|null  $message  The error messages if any.
	 *
	 * @return  array  The array of the error message
	 * @since   2.3.0
	 */
	protected function getTokenForWiki(?string $message = null): array
	{
		if ($message)
		{
			return ['error' => $message];
		}

		return ['error' => Text::_('COM_GETBIBLE_THE_WIKI_CAN_ONLY_BE_LOADED_WHEN_YOUR_JCB_SYSTEM_HAS_INTERNET_CONNECTION')];
	}
}
