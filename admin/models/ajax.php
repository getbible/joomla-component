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
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use VDM\Joomla\Utilities\FileHelper;
use VDM\Joomla\Gitea\Factory;

/**
 * Getbible Ajax List Model
 */
class GetbibleModelAjax extends ListModel
{
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();		
		// get params
		$this->app_params	= JComponentHelper::getParams('com_getbible');
		
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
		$path_filename = FileHelper::getPath('path', 'usernotice', 'md', JFactory::getUser()->username, JPATH_COMPONENT_ADMINISTRATOR);

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
		$path_filename = FileHelper::getPath('path', 'usernotice', 'md', JFactory::getUser()->username, JPATH_COMPONENT_ADMINISTRATOR);

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
		// get the token if set
		$token = $this->app_params->get('gitea_token', false);

		// only add if token is set
		if ($token)
		{
			try
			{
				// load the API details
				Factory::_('Gitea.Repository.Tags')->load_('https://git.vdm.dev', $token);

				// get the repository tags
				$tags = Factory::_('Gitea.Repository.Tags')->list('getBible', 'joomla-component');
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
					return ['notice' => '<small><span style="color:green;"><span class="icon-shield"></span>' . Text::_('COM_GETBIBLE_UP_TO_DATE') . '</span></small>'];
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
						return ['notice' => '<small><span style="color:#F7B033;"><span class="icon-wrench"></span>' . Text::_('COM_GETBIBLE_BETA_RELEASE') . '</span></small>'];
					}
					else
					{
						// download link of the latest version
						$download = "https://git.vdm.dev/api/v1/repos/joomla/[[[gitea_package_name]]]/archive/" . $tags[0]->name . ".zip?access_token=" . $token;

						return ['notice' => '<small><span style="color:red;"><span class="icon-warning-circle"></span>' . Text::_('COM_GETBIBLE_OUT_OF_DATE') . '!</span> <a style="color:green;"  href="' .
							$download . '" title="' . Text::_('COM_GETBIBLE_YOU_CAN_DIRECTLY_DOWNLOAD_THE_LATEST_UPDATE_OR_USE_THE_JOOMLA_UPDATE_AREA') . '">' . Text::_('COM_GETBIBLE_DOWNLOAD_UPDATE') . '!</a></small>'];
					}
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
			// load the API details
			// Factory::_('Gitea.Repository.Wiki')->load_('https://git.vdm.dev', '');

			// get the gitea wiki page im markdown
			$wiki = Factory::_('Gitea.Repository.Wiki')->get('getBible', 'joomla-component', $name);

			// now render the page in HTML
			$page = Factory::_('Gitea.Miscellaneous.Markdown')->render($wiki->content, true);
		}
		catch (DomainException $e)
		{
			return $this->getTokenForWiki($e->getMessage());
		}
		catch (InvalidArgumentException $e)
		{
			return $this->getTokenForWiki($e->getMessage());
		}
		catch (Exception $e)
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
