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

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Utilities\ArrayHelper;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use Joomla\Registry\Registry;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Getbible List Model
 */
class GetbibleModel extends ListModel
{
	public function getIcons()
	{
		// load user for access menus
		$user = Factory::getApplication()->getIdentity();
		// reset icon array
		$icons  = [];
		// view groups array
		$viewGroups = array(
			'main' => array('png.linkers', 'png.notes', 'png.tagged_verses', 'png.prompts', 'png.open_ai_responses', 'png.tags', 'png.translations', 'png.books', 'png.chapters', 'png.verses')
		);
		// view access array
		$viewAccess = [
			'linker.create' => 'linker.create',
			'linkers.access' => 'linker.access',
			'linker.access' => 'linker.access',
			'linkers.submenu' => 'linker.submenu',
			'linkers.dashboard_list' => 'linker.dashboard_list',
			'note.create' => 'note.create',
			'notes.access' => 'note.access',
			'note.access' => 'note.access',
			'notes.submenu' => 'note.submenu',
			'notes.dashboard_list' => 'note.dashboard_list',
			'tagged_verse.create' => 'tagged_verse.create',
			'tagged_verses.access' => 'tagged_verse.access',
			'tagged_verse.access' => 'tagged_verse.access',
			'tagged_verses.submenu' => 'tagged_verse.submenu',
			'tagged_verses.dashboard_list' => 'tagged_verse.dashboard_list',
			'prompt.create' => 'prompt.create',
			'prompts.access' => 'prompt.access',
			'prompt.access' => 'prompt.access',
			'prompts.submenu' => 'prompt.submenu',
			'prompts.dashboard_list' => 'prompt.dashboard_list',
			'open_ai_response.create' => 'open_ai_response.create',
			'open_ai_responses.access' => 'open_ai_response.access',
			'open_ai_response.access' => 'open_ai_response.access',
			'open_ai_responses.submenu' => 'open_ai_response.submenu',
			'open_ai_responses.dashboard_list' => 'open_ai_response.dashboard_list',
			'open_ai_message.create' => 'open_ai_message.create',
			'open_ai_messages.access' => 'open_ai_message.access',
			'open_ai_message.access' => 'open_ai_message.access',
			'password.create' => 'password.create',
			'passwords.access' => 'password.access',
			'password.access' => 'password.access',
			'tag.create' => 'tag.create',
			'tags.access' => 'tag.access',
			'tag.access' => 'tag.access',
			'tags.submenu' => 'tag.submenu',
			'tags.dashboard_list' => 'tag.dashboard_list',
			'translation.create' => 'translation.create',
			'translations.access' => 'translation.access',
			'translation.access' => 'translation.access',
			'translations.submenu' => 'translation.submenu',
			'translations.dashboard_list' => 'translation.dashboard_list',
			'book.create' => 'book.create',
			'books.access' => 'book.access',
			'book.access' => 'book.access',
			'books.submenu' => 'book.submenu',
			'books.dashboard_list' => 'book.dashboard_list',
			'chapter.create' => 'chapter.create',
			'chapters.access' => 'chapter.access',
			'chapter.access' => 'chapter.access',
			'chapters.submenu' => 'chapter.submenu',
			'chapters.dashboard_list' => 'chapter.dashboard_list',
			'verse.create' => 'verse.create',
			'verses.access' => 'verse.access',
			'verse.access' => 'verse.access',
			'verses.submenu' => 'verse.submenu',
			'verses.dashboard_list' => 'verse.dashboard_list',
		];
		// loop over the $views
		foreach($viewGroups as $group => $views)
		{
			$i = 0;
			if (UtilitiesArrayHelper::check($views))
			{
				foreach($views as $view)
				{
					$add = false;
					// external views (links)
					if (strpos($view,'||') !== false)
					{
						$dwd = explode('||', $view);
						if (count($dwd) == 3)
						{
							list($type, $name, $url) = $dwd;
							$viewName = $name;
							$alt      = $name;
							$url      = $url;
							$image    = $name . '.' . $type;
							$name     = 'COM_GETBIBLE_DASHBOARD_' . StringHelper::safe($name,'U');
						}
					}
					// internal views
					elseif (strpos($view,'.') !== false)
					{
						$dwd = explode('.', $view);
						if (count($dwd) == 3)
						{
							list($type, $name, $action) = $dwd;
						}
						elseif (count($dwd) == 2)
						{
							list($type, $name) = $dwd;
							$action = false;
						}
						if ($action)
						{
							$viewName = $name;
							switch($action)
							{
								case 'add':
									$url   = 'index.php?option=com_getbible&view=' . $name . '&layout=edit';
									$image = $name . '_' . $action.  '.' . $type;
									$alt   = $name . '&nbsp;' . $action;
									$name  = 'COM_GETBIBLE_DASHBOARD_'.StringHelper::safe($name,'U').'_ADD';
									$add   = true;
								break;
								default:
									// check for new convention (more stable)
									if (strpos($action, '_qpo0O0oqp_') !== false)
									{
										list($action, $extension) = (array) explode('_qpo0O0oqp_', $action);
										$extension = str_replace('_po0O0oq_', '.', $extension);
									}
									else
									{
										$extension = 'com_getbible.' . $name;
									}
									$url   = 'index.php?option=com_categories&view=categories&extension=' . $extension;
									$image = $name . '_' . $action . '.' . $type;
									$alt   = $viewName . '&nbsp;' . $action;
									$name  = 'COM_GETBIBLE_DASHBOARD_' . StringHelper::safe($name,'U') . '_' . StringHelper::safe($action,'U');
								break;
							}
						}
						else
						{
							$viewName = $name;
							$alt      = $name;
							$url      = 'index.php?option=com_getbible&view=' . $name;
							$image    = $name . '.' . $type;
							$name     = 'COM_GETBIBLE_DASHBOARD_' . StringHelper::safe($name,'U');
							$hover    = false;
						}
					}
					else
					{
						$viewName = $view;
						$alt      = $view;
						$url      = 'index.php?option=com_getbible&view=' . $view;
						$image    = $view . '.png';
						$name     = ucwords($view).'<br /><br />';
						$hover    = false;
					}
					// first make sure the view access is set
					if (UtilitiesArrayHelper::check($viewAccess))
					{
						// setup some defaults
						$dashboard_add = false;
						$dashboard_list = false;
						$accessTo = '';
						$accessAdd = '';
						// access checking start
						$accessCreate = (isset($viewAccess[$viewName.'.create'])) ? StringHelper::check($viewAccess[$viewName.'.create']):false;
						$accessAccess = (isset($viewAccess[$viewName.'.access'])) ? StringHelper::check($viewAccess[$viewName.'.access']):false;
						// set main controllers
						$accessDashboard_add = (isset($viewAccess[$viewName.'.dashboard_add'])) ? StringHelper::check($viewAccess[$viewName.'.dashboard_add']):false;
						$accessDashboard_list = (isset($viewAccess[$viewName.'.dashboard_list'])) ? StringHelper::check($viewAccess[$viewName.'.dashboard_list']):false;
						// check for adding access
						if ($add && $accessCreate)
						{
							$accessAdd = $viewAccess[$viewName.'.create'];
						}
						elseif ($add)
						{
							$accessAdd = 'core.create';
						}
						// check if access to view is set
						if ($accessAccess)
						{
							$accessTo = $viewAccess[$viewName.'.access'];
						}
						// set main access controllers
						if ($accessDashboard_add)
						{
							$dashboard_add    = $user->authorise($viewAccess[$viewName.'.dashboard_add'], 'com_getbible');
						}
						if ($accessDashboard_list)
						{
							$dashboard_list = $user->authorise($viewAccess[$viewName.'.dashboard_list'], 'com_getbible');
						}
						if (StringHelper::check($accessAdd) && StringHelper::check($accessTo))
						{
							// check access
							if($user->authorise($accessAdd, 'com_getbible') && $user->authorise($accessTo, 'com_getbible') && $dashboard_add)
							{
								$icons[$group][$i]        = new \StdClass;
								$icons[$group][$i]->url   = $url;
								$icons[$group][$i]->name  = $name;
								$icons[$group][$i]->image = $image;
								$icons[$group][$i]->alt   = $alt;
							}
						}
						elseif (StringHelper::check($accessTo))
						{
							// check access
							if($user->authorise($accessTo, 'com_getbible') && $dashboard_list)
							{
								$icons[$group][$i]        = new \StdClass;
								$icons[$group][$i]->url   = $url;
								$icons[$group][$i]->name  = $name;
								$icons[$group][$i]->image = $image;
								$icons[$group][$i]->alt   = $alt;
							}
						}
						elseif (StringHelper::check($accessAdd))
						{
							// check access
							if($user->authorise($accessAdd, 'com_getbible') && $dashboard_add)
							{
								$icons[$group][$i]        = new \StdClass;
								$icons[$group][$i]->url   = $url;
								$icons[$group][$i]->name  = $name;
								$icons[$group][$i]->image = $image;
								$icons[$group][$i]->alt   = $alt;
							}
						}
						else
						{
							$icons[$group][$i]        = new \StdClass;
							$icons[$group][$i]->url   = $url;
							$icons[$group][$i]->name  = $name;
							$icons[$group][$i]->image = $image;
							$icons[$group][$i]->alt   = $alt;
						}
					}
					else
					{
						$icons[$group][$i]        = new \StdClass;
						$icons[$group][$i]->url   = $url;
						$icons[$group][$i]->name  = $name;
						$icons[$group][$i]->image = $image;
						$icons[$group][$i]->alt   = $alt;
					}
					$i++;
				}
			}
			else
			{
					$icons[$group][$i] = false;
			}
		}
		return $icons;
	}


	public function getWiki()
	{
		// the call URL
		$call_url = Uri::base() . 'index.php?option=com_getbible&task=ajax.getWiki&format=json&raw=true&' . Session::getFormToken() . '=1&name=Home';
		$document = Factory::getDocument();
		$document->addScriptDeclaration('
		function getWikiPage(){
			fetch("' . $call_url . '").then((response) => {
				if (response.ok) {
					return response.json();
				}
			}).then((result) => {
				if (typeof result.page !== "undefined") {
					document.getElementById("wiki-md").innerHTML = marked.parse(result.page);
				} else if (typeof result.error !== "undefined") {
					document.getElementById("wiki-md-error").innerHTML = result.error
				}
			});
		}
		setTimeout(getWikiPage, 1000);');

		return '<div id="wiki-md"><small>'.Text::_('COM_GETBIBLE_THE_WIKI_IS_LOADING').'.<span class="loading-dots">.</span></small></div><div id="wiki-md-error" style="color: red"></div>';
	}
	

	public function getNoticeboard()
	{
		// get the document to load the scripts
		$document = Factory::getDocument();
		Html::_('script', "media/com_getbible/js/marked.js", ['version' => 'auto']);
		$document->addScriptDeclaration('
		var token = "' . Session::getFormToken() . '";
		var noticeboard = "https://vdm.bz/getbible-noticeboard-md";
		document.addEventListener("DOMContentLoaded", function() {
			fetch(noticeboard)
			.then(response => {
				if (!response.ok) {
					throw new Error("Network response was not ok");
				}
				return response.text();
			})
			.then(board => {
				if (board.length > 5) {
					document.getElementById("noticeboard-md").innerHTML = marked.parse(board);
					getIS(1, board)
					.then(result => {
						if (result) {
							document.querySelectorAll("#cpanel_tabTabs a").forEach(link => {
								if (link.href.includes("#vast_development_method") || link.href.includes("#notice_board")) {
									var textVDM = link.textContent;
									link.innerHTML = "<span class=\"label label-important vdm-new-notice\">1</span> " + textVDM;
									link.id = "vdm-new-notice";
									document.getElementById("vdm-new-notice").addEventListener("click", () => {
										getIS(2, board)
										.then(result => {
											if (result) {
												document.querySelectorAll(".vdm-new-notice").forEach(element => {
													element.style.opacity = 0;
												});
											}
										});
									});
								}
							});
						}
					});
				} else {
					document.getElementById("noticeboard-md").innerHTML = "'.Text::_('COM_GETBIBLE_ALL_IS_GOOD_PLEASE_CHECK_AGAIN_LATER').'.";
				}
			})
			.catch(error => {
				console.error("There was an error!", error);
				document.getElementById("noticeboard-md").innerHTML = "'.Text::_('COM_GETBIBLE_ALL_IS_GOOD_PLEASE_CHECK_AGAIN_LATER').'.";
			});
		});

		// to check is READ/NEW
		function getIS(type, notice) {
			let getUrl = "";
			if (type === 1) {
				getUrl = "index.php?option=com_getbible&task=ajax.isNew&format=json&raw=true";
			} else if (type === 2) {
				getUrl = "index.php?option=com_getbible&task=ajax.isRead&format=json&raw=true";
			}
			let request = new URLSearchParams();
			if (token.length > 0 && notice.length) {
				request.append(token, "1");
				request.append("notice", notice);
			}
			return fetch(getUrl, {
				method: "POST",
				headers: {
					"Content-Type": "application/x-www-form-urlencoded;charset=UTF-8"
				},
				body: request
			}).then(response => response.json());
		}
		
document.addEventListener("DOMContentLoaded", function() {
	document.querySelectorAll(".loading-dots").forEach(function(loading_dots) {
		let x = 0;
		let intervalId = setInterval(function() {
			if (!loading_dots.classList.contains("loading-dots")) {
				clearInterval(intervalId);
				return;
			}
			let dots = ".".repeat(x % 8);
			loading_dots.textContent = dots;
			x++;
		}, 500);
	});
});');

		return '<div id="noticeboard-md">'.Text::_('COM_GETBIBLE_THE_NOTICE_BOARD_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}

	public function getReadme()
	{
		$document = Factory::getDocument();
		$document->addScriptDeclaration('
		var getreadme = "'. Uri::root() . 'administrator/components/com_getbible/README.txt";
		document.addEventListener("DOMContentLoaded", function () {
			fetch(getreadme)
			.then(response => {
				if (!response.ok) {
				    throw new Error("Network response was not ok");
				}
				return response.text();
			})
			.then(readme => {
				document.getElementById("readme-md").innerHTML = marked.parse(readme);
			})
			.catch(error => {
				console.error("There has been a problem with your fetch operation:", error);
				document.getElementById("readme-md").innerHTML = "'.Text::_('COM_GETBIBLE_PLEASE_CHECK_AGAIN_LATER').'.";
			});
		});');

		return '<div id="readme-md"><small>'.Text::_('COM_GETBIBLE_THE_README_IS_LOADING').'.<span class="loading-dots">.</span></small></div>';
	}

	/**
	 * get Current Version Bay adding JavaScript to the Page
	 *
	 * @return  void
	 * @since   2.3.0
	 */
	public function getVersion()
	{
		// the call URL
		$call_url = Uri::base() . 'index.php?option=com_getbible&task=ajax.getVersion&format=json&raw=true&' . Session::getFormToken() . '=1&version=1';
		$document = Factory::getDocument();
		$document->addScriptDeclaration('
		function getComponentVersionStatus() {
			fetch("' . $call_url . '").then((response) => {
				if (response.ok) {
					return response.json();
				}
			}).then((result) => {
				if (typeof result.notice !== "undefined") {
					document.getElementById("component-update-notice").innerHTML = result.notice;
				} else if (typeof result.error !== "undefined") {
					document.getElementById("component-update-notice").innerHTML = result.error;
				}
			});
		}
		setTimeout(getComponentVersionStatus, 800);');
	}

}
