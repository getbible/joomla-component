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
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\User\User;
use Joomla\Utilities\ArrayHelper;
use Joomla\Input\Input;
use TrueChristianBible\Component\GetBible\Administrator\Helper\GetbibleHelper;
use Joomla\Registry\Registry;
use TrueChristianBible\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use TrueChristianBible\Joomla\Utilities\StringHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible List Model
 *
 * @since  1.6
 */
class GetbibleModel extends ListModel
{
	/**
	 * Represents the current user object.
	 *
	 * @var   User  The user object representing the current user.
	 * @since 3.2.0
	 */
	protected User $user;

	/**
	 * View groups of this component
	 *
	 * @var   array<string, string>
	 * @since 5.1.1
	 */
	protected array $viewGroups = [
		'main' => ['png.linkers', 'png.notes', 'png.tagged_verses', 'png.prompts', 'png.open_ai_responses', 'png.tags', 'png.translations', 'png.books', 'png.chapters', 'png.verses'],
	];

	/**
	 * View access array.
	 *
	 * @var   array<string, string>
	 * @since 5.1.1
	 */
	protected array $viewAccess = [
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

	/**
	 * The styles array.
	 *
	 * @var    array
	 * @since  4.3
	 */
	protected array $styles = [
		'administrator/components/com_getbible/assets/css/admin.css',
		'administrator/components/com_getbible/assets/css/dashboard.css'
 	];

	/**
	 * The scripts array.
	 *
	 * @var    array
	 * @since  4.3
	 */
	protected array $scripts = [
		'administrator/components/com_getbible/assets/js/admin.js'
 	];

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

		$this->user ??= $this->getCurrentUser();
	}

	/**
	 * Get dashboard icons, grouped by view sections.
	 *
	 * @return array<string, array<int, \stdClass|false>>
	 * @since  5.1.1
	 */
	public function getIcons(): array
	{
		$icons = [];

		foreach ($this->viewGroups as $group => $views)
		{
			if (!UtilitiesArrayHelper::check($views))
			{
				$icons[$group][] = false;
				continue;
			}

			foreach ($views as $view)
			{
				$icon = $this->buildIconObject($view);
				if ($icon !== null)
				{
					$icons[$group][] = $icon;
				}
			}
		}

		return $icons;
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
	 * Build a single dashboard icon if access is granted.
	 *
	 * @param string $view The view string to parse.
	 *
	 * @return \stdClass|null  The icon object or null if access denied.
	 * @since  5.1.1
	 */
	protected function buildIconObject(string $view): ?\stdClass
	{
		$parsed = $this->parseViewDefinition($view);
		if (!$parsed)
		{
			return null;
		}

		[
			'type' => $type,
			'name' => $name,
			'url' => $url,
			'image' => $image,
			'alt' => $alt,
			'viewName' => $viewName,
			'add' => $add,
		] = $parsed;

		if (!$this->hasAccessToView($viewName, $add))
		{
			return null;
		}

		return $this->createIconObject($url, $name, $image, $alt);
	}

	/**
	 * Parse a view string into structured components.
	 *
	 * @param string $view  The view definition string.
	 *
	 * @return array<string, mixed>|null  Parsed values or null on failure.
	 * @since  5.1.1
	 */
	protected function parseViewDefinition(string $view): ?array
	{
		$add = false;

		if (strpos($view, '||') !== false)
		{
			$parts = explode('||', $view);
			if (count($parts) === 3)
			{
				[$type, $name, $url] = $parts;
				return [
					'type' => $type,
					'name' => 'COM_GETBIBLE_DASHBOARD_' . StringHelper::safe($name, 'U'),
					'url' => $url,
					'image' => "{$name}.{$type}",
					'alt' => $name,
					'viewName' => $name,
					'add' => false,
				];
			}
		}

		if (strpos($view, '.') !== false)
		{
			$parts = explode('.', $view);
			$type = $parts[0] ?? '';
			$name = $parts[1] ?? '';
			$action = $parts[2] ?? null;
			$viewName = $name;

			if ($action)
			{
				if ($action === 'add')
				{
					$url = "index.php?option=com_getbible&view={$name}&layout=edit";
					$image = "{$name}_{$action}.{$type}";
					$alt = "{$name}&nbsp;{$action}";
					$name = 'COM_GETBIBLE_DASHBOARD_' .
							StringHelper::safe($name, 'U') . '_ADD';
					$add = true;
				}
				else
				{
					if (strpos($action, '_qpo0O0oqp_') !== false)
					{
						[$action, $ext] = explode('_qpo0O0oqp_', $action);
						$extension = str_replace('_po0O0oq_', '.', $ext);
					}
					else
					{
						$extension = "com_getbible.{$name}";
					}
					$url = "index.php?option=com_categories&view=categories&extension={$extension}";
					$image = "{$name}_{$action}.{$type}";
					$alt = "{$name}&nbsp;{$action}";
					$name = 'COM_GETBIBLE_DASHBOARD_' .
							StringHelper::safe($name, 'U') . '_' .
							StringHelper::safe($action, 'U');
				}
			}
			else
			{
				$url = "index.php?option=com_getbible&view={$name}";
				$image = "{$name}.{$type}";
				$alt = $name;
				$name = 'COM_GETBIBLE_DASHBOARD_' .
						StringHelper::safe($name, 'U');
			}

			return compact('type', 'name', 'url', 'image', 'alt', 'viewName', 'add');
		}

		return [
			'type' => 'png',
			'name' => ucwords($view) . '<br /><br />',
			'url' => "index.php?option=com_getbible&view={$view}",
			'image' => "{$view}.png",
			'alt' => $view,
			'viewName' => $view,
			'add' => false,
		];
	}

	/**
	 * Determine if the user has access to view or create the item.
	 *
	 * @param string $viewName The base name of the view.
	 * @param bool $add If this is an add-action.
	 *
	 * @return bool
	 * @since  5.1.1
	 */
	protected function hasAccessToView(string $viewName, bool $add): bool
	{
		$viewAccess = $this->viewAccess;
		$accessAdd = $add && isset($viewAccess["{$viewName}.create"])
			? $viewAccess["{$viewName}.create"]
			: ($add ? 'core.create' : '');

		$accessTo = $viewAccess["{$viewName}.access"] ?? '';

		$dashboardAdd = isset($viewAccess["{$viewName}.dashboard_add"]) &&
					$this->user->authorise($viewAccess["{$viewName}.dashboard_add"], 'com_getbible');

		$dashboardList = isset($viewAccess["{$viewName}.dashboard_list"]) &&
					$this->user->authorise($viewAccess["{$viewName}.dashboard_list"], 'com_getbible');

		if ($add && StringHelper::check($accessAdd))
		{
			return $this->user->authorise($accessAdd, 'com_getbible') && $dashboardAdd;
		}

		if (StringHelper::check($accessTo))
		{
			return $this->user->authorise($accessTo, 'com_getbible') && $dashboardList;
		}

		return !$accessTo && !$accessAdd;
	}

	/**
	 * Create a \stdClass icon object.
	 *
	 * @param string $url Icon URL.
	 * @param string $name Language string or label.
	 * @param string $image Image filename.
	 * @param string $alt Alt text.
	 *
	 * @return \stdClass
	 * @since  5.1.1
	 */
	protected function createIconObject(string $url, string $name, string $image, string $alt): \stdClass
	{
		$icon = new \stdClass;
		$icon->url = $url;
		$icon->name = $name;
		$icon->image = $image;
		$icon->alt = $alt;
		return $icon;
	}


	/**
	 * Load and display the wiki page content using an AJAX call to the component endpoint.
	 *
	 * This method injects an inline JavaScript script that asynchronously fetches the wiki page content
	 * via a JSON API endpoint in the component. It uses the `marked` library to render markdown content
	 * and inserts the result into the `wiki-md` container. Errors are displayed in a separate element.
	 *
	 * @return string  HTML markup including a container for the wiki content and an error message area.
	 * @since 3.9.0
	 */
	public function getWiki()
	{
		// call the ajax get wiki endpoint
		$call_url = Uri::base() . 'index.php?option=com_getbible&task=ajax.getWiki&format=json&raw=true&' . Session::getFormToken() . '=1&name=Home';

		/** \Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
		$wa->addInlineScript('
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

	/**
	 * Load and display the component's README file using JavaScript fetch and markdown rendering.
	 *
	 * This method injects an inline script into the document that, once the DOM is fully loaded,
	 * fetches the README.txt file located in the administrator component directory, parses it using
	 * the `marked` JavaScript library, and inserts the HTML into the `readme-md` div.
	 *
	 * Automatically uses WebAssetManager if available (Joomla 4+), and falls back to legacy
	 * `$doc->addScriptDeclaration()` for Joomla 3.
	 *
	 * @return string  HTML markup including a container for the README content and a loading message.
	 * @since 3.9.0
	 */
	public function getReadme(): string
	{
		$app = $this->app ?? Factory::getApplication();

		$callUrl = Uri::root()
			. 'administrator/components/com_getbible/README.txt';

		$errorMessage = Text::_('COM_GETBIBLE_PLEASE_CHECK_AGAIN_LATER');

		/** @var \Joomla\CMS\Document\Document $document */
		$document = $app->getDocument();

		// JavaScript to fetch and render README using `marked`
		$script = <<<JS
document.addEventListener("DOMContentLoaded", function () {
	fetch("{$callUrl}")
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
			document.getElementById("readme-md").innerHTML = "{$errorMessage}";
		});
});
JS;

		// Use WebAssetManager if available (Joomla 4+), otherwise fallback
		if (method_exists($document, 'getWebAssetManager'))
		{
			/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
			$wa = $document->getWebAssetManager();
			$wa->addInlineScript($script);
		}
		else
		{
			$document->addScriptDeclaration($script);
		}

		// Return the README container markup
		return '<div id="readme-md"><small>'
			. Text::_('COM_GETBIBLE_THE_README_IS_LOADING')
			. '.<span class="loading-dots">.</span></small></div>';
	}

	/**
	 * Inject JavaScript that fetches and displays the current component version status.
	 *
	 * This method adds an inline script to the page which asynchronously calls the component's
	 * AJAX endpoint to check the latest version. It updates the `#component-update-notice` element
	 * with the fetched version notice or error message.
	 *
	 * @return void
	 * @since  2.3.0
	 */
	public function getVersion()
	{
		// call the ajax get version endpoint
		$call_url = Uri::base()
			. 'index.php?option=com_getbible&task=ajax.getVersion&format=json&raw=true&'
			. Session::getFormToken() . '=1&version=1.0.0';

		try {
			/** \Joomla\CMS\WebAsset\WebAssetManager $wa */
			$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

			$wa->addInlineScript('
			function getComponentVersionStatus() {
				fetch("' . $call_url . '").then((response) => {
					if (response.ok) {
						return response.json();
					}
				}).then((result) => {
					const target = document.getElementById("component-update-notice");
					if (!target) return;
					if (typeof result.notice !== "undefined") {
						target.innerHTML = result.notice;
					} else if (typeof result.error !== "undefined") {
						target.innerHTML = result.error;
					}
				});
			}
			setTimeout(getComponentVersionStatus, 800);');
		} catch (\Throwable $e) {
			// we do nothing....
		}
	}
}
