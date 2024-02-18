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
namespace TrueChristianChurch\Component\Getbible\Site\View\Openai;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Document\Document;
use TrueChristianChurch\Component\Getbible\Site\Helper\HeaderCheck;
use TrueChristianChurch\Component\Getbible\Site\Helper\GetbibleHelper;
use TrueChristianChurch\Component\Getbible\Site\Helper\RouteHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible Html View class for the Openai
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 * @since  1.6
	 */
	public function display($tpl = null)
	{
		// get combined params of both component and menu
		$this->app ??= Factory::getApplication();
		$this->params = $this->app->getParams();
		$this->menu = $this->app->getMenu()->getActive();
		// get the user object
		$this->user ??= $this->app->getIdentity();
		// Initialise variables.
		$this->item = $this->get('Item');
		$this->translation = $this->get('Translation');
		// remove from page (in case debug mode is on)
		$this->params->set('openai_token', null);
		$this->params->set('gitea_token', null);
		// set meta
		$this->setMetaData();

		// Set the toolbar
		$this->addToolBar();

		// Set the html view document stuff
		$this->_prepareDocument();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode(PHP_EOL, $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	 * Set the page metadata
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setMetaData()
	{
		if (empty($this->item))
		{
			return;
		}

		// set the page title
		$title = Text::sprintf('COM_GETBIBLE_OPEN_AI_S_IN_S_S',
			$this->getSelectedWord(),
			$this->translation->translation,
			$this->params->get('page_title', '')
		);
		$this->getDocument()->setTitle($title);
		$url =  $this->getCanonicalUrl();
		// set the Generator
		$this->getDocument()->setGenerator('getBible! - Open AI - Open Source Bible App.');

		// set the metadata values
		$description = Text::sprintf('COM_GETBIBLE_OPEN_AI_RESPOND_TO_PROMPT_ABOUT_S_IN_S',
			$this->getSelectedWord(),
			$this->translation->translation
		);
		$this->getDocument()->setDescription($description);
		$this->getDocument()->setMetadata('keywords', Text::sprintf('COM_GETBIBLE_OPEN_AI_S_S_BIBLE_S_S_SCRIPTURE_RESEARCH_GETBIBLE',
			$this->getSelectedWord(),
			$this->translation->translation,
			$this->translation->abbreviation,
			$this->translation->language
		));
		$this->getDocument()->setMetaData('author', Text::_('COM_GETBIBLE_OPEN_AI'));

		// set canonical URL
		$this->getDocument()->addHeadLink($url, 'canonical');

		// OG:Title
		$this->getDocument()->setMetadata('og:title', $title, 'property');

		// OG:Description
		$this->getDocument()->setMetadata('og:description', $description, 'property');

		// OG:Image
		// $this->getDocument()->setMetadata('og:image', 'YOUR_IMAGE_URL_HERE', 'property');

		// OG:URL
		$this->getDocument()->setMetadata('og:url', $url, 'property');

		// OG:Type
		$this->getDocument()->setMetadata('og:type', 'website', 'property');

		// Twitter Card Type
		$this->getDocument()->setMetadata('twitter:card', 'summary');

		// Twitter Title
		$this->getDocument()->setMetadata('twitter:title', $title);

		// Twitter Description
		$this->getDocument()->setMetadata('twitter:description', $description);

		// Twitter Image
		// $this->getDocument()->setMetadata('twitter:image', 'YOUR_IMAGE_URL_HERE');

		// Twitter Site (Your website's Twitter handle)
		// $this->getDocument()->setMetadata('twitter:site', '@YourTwitterHandle');

		// Twitter Creator (Author's Twitter handle or your website's Twitter handle)
		// $this->getDocument()->setMetadata('twitter:creator', '@AuthorTwitterHandle');
	}

	/**
	 * Get the canonical url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getCanonicalUrl(): string
	{
		if (empty($this->url_canonical))
		{
			$this->setCanonicalUrl();
		}
		return $this->url_canonical ?? $this->getBaseUrl();
	}

	/**
	 * Get the Bible url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getBibleUrl(): string
	{
		if (empty($this->url_bible))
		{
			$this->setBibleUrl();
		}
		return $this->url_bible;
	}

	/**
	 * Get the base url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getBaseUrl(): string
	{
		if (empty($this->url_base))
		{
			$this->setBaseUrl();
		}
		return $this->url_base ?? '';
	}

	/**
	 * Get the AI url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getAiUrl(): string
	{
		if (empty($this->url_ai))
		{
			$this->setAiUrl();
		}
		return $this->url_ai ?? '';
	}

	/**
	 * Get the AJAX url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getAjaxUrl(): string
	{
		if (empty($this->url_ajax))
		{
			$this->setAjaxUrl();
		}
		return $this->url_ajax ?? '';
	}

	/**
	 * Get the return url value
	 *
	 * @return  string|null
	 * @since  2.0.1
	 */
	public function getReturnUrl(): ?string
	{
		if (empty($this->url_return))
		{
			$this->setReturnUrl();
		}

		return $this->url_return ?? null;
	}

	/**
	 * Get the return url value
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getReturnUrlValue(): string
	{
		if (empty($this->url_return_value))
		{
			$this->setReturnUrl();
		}

		if (!empty($this->url_return_value))
		{
			return '&bibleurl=' . $this->url_return_value;
		}

		return '';
	}

	/**
	 * Get the return url book value
	 *
	 * @return  int
	 * @since  2.0.1
	 */
	public function getReturnUrlBook(): int
	{
		if (empty($this->url_return_query))
		{
			$this->setReturnUrl();
		}

		return (int) $this->url_return_query['book'] ?? 0;
	}

	/**
	 * Get the return url chapter value
	 *
	 * @return  int
	 * @since  2.0.1
	 */
	public function getReturnUrlChapter(): int
	{
		if (empty($this->url_return_query))
		{
			$this->setReturnUrl();
		}

		return (int) $this->url_return_query['chapter'] ?? 0;
	}

	/**
	 * Get the last prompt
	 *
	 * @return  object|null
	 * @since  2.0.1
	 */
	public function getPrompt(): ?object
	{
		if (empty($this->prompt))
		{
			$this->setPrompt();
		}
		return $this->prompt ?? null;
	}

	/**
	 * Get the selected word
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getSelectedWord(): string
	{
		if (empty($this->selected_word))
		{
			$this->setSelectedWord();
		}
		return $this->selected_word ?? '';
	}

	/**
	 * Get the word number/s
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getWord(): string
	{
		if (empty($this->word))
		{
			$this->setWord();
		}
		return $this->word ?? '';
	}

	/**
	 * Get the verse number/s
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getVerse(): string
	{
		if (empty($this->verse))
		{
			$this->setVerse();
		}
		return $this->verse ?? '';
	}

	/**
	 * Get the chapter number/s
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getChapter(): string
	{
		if (empty($this->chapter))
		{
			$this->setChapter();
		}
		return $this->chapter ?? '';
	}

	/**
	 * Get the book number/s
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getBook(): string
	{
		if (empty($this->book))
		{
			$this->setBook();
		}
		return $this->book ?? '';
	}

	/**
	 * Get the prompt guid
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getGuid(): string
	{
		if (empty($this->prompt_guid))
		{
			$this->setGuid();
		}
		return $this->prompt_guid ?? '';
	}

	/**
	 * Set the return URL if it's provided and internal.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setReturnUrl(): void
	{
		$encodedUrl = $this->input->get('bibleurl', null, 'base64');

		if ($encodedUrl === null)
		{
			return;
		}

		$decodedUrl = base64_decode($encodedUrl);
		$uri = Uri::getInstance($decodedUrl);
		$router = Router::getInstance('site');

		$this->url_return_value = $encodedUrl;
		$this->url_return = $decodedUrl;
		$this->url_return_query = $router->parse($uri);
	}

	/**
	 * Set the prompt guid
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setGuid()
	{
		$prompt = $this->getPrompt();

		$this->prompt_guid = $prompt->prompt ?? '';
	}

	/**
	 * Set the book number/s
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBook()
	{
		$prompt = $this->getPrompt();

		$this->book = $prompt->book ?? '';
	}

	/**
	 * Set the chapter number/s
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setChapter()
	{
		$prompt = $this->getPrompt();

		$this->chapter = $prompt->chapter ?? '';
	}

	/**
	 * Set the verse number/s
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setVerse()
	{
		$prompt = $this->getPrompt();

		$this->verse = $prompt->verse ?? '';
	}

	/**
	 * Set the word number/s
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setWord()
	{
		$prompt = $this->getPrompt();

		$this->word = $prompt->word ?? '';
	}

	/**
	 * Set the selected word
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	public function setSelectedWord()
	{
		$prompt = $this->getPrompt();

		$this->selected_word = $prompt->selected_word ?? '';
	}

	/**
	 * Set the last prompt
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setPrompt()
	{
		// Check if the 'item' property or its first element is empty.
		if (empty($this->item) || empty($this->item[0]))
		{
			return;
		}

		// Set the last item from the 'item' array as the prompt.
		$this->prompt = end($this->item);

		// Reset the internal pointer of the array to ensure consistent behaviour elsewhere.
		reset($this->item);
	}

	/**
	 * Set the base url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBaseUrl()
	{
		$this->url_base = Uri::base();
	}

	/**
	 * Set the AJAX url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setAjaxUrl()
	{
		$this->url_ajax = $this->getBaseUrl() . 'index.php?option=com_getbible&format=json&raw=true&' . Session::getFormToken() . '=1&task=ajax.';
	}

	/**
	 * Set the Bible url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBibleUrl()
	{
		$this->url_bible = $this->getReturnUrl() ?? Route::_('index.php?option=com_getbible&view=app&Itemid=' . $this->params->get('app_menu', 0) . '&t=' . $this->translation->abbreviation);
	}

	/**
	 * Set the AI url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setAiUrl()
	{
		// set the current search URL
		$this->url_ai = Route::_('index.php?option=com_getbible&view=openai&t=' . $this->translation->abbreviation .
			'&Itemid=' . $this->params->get('app_menu', 0) .
			$this->getReturnUrlValue() .
			'&guid=' . $this->getGuid() .
			'&book=' . $this->getBook() .
			'&chapter=' . $this->getChapter() .
			'&verse=' . $this->getVerse() .
			'&words=' . $this->getWord());
	}

	/**
	 * Set the canonical url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setCanonicalUrl()
	{
		// set the current search URL
		$this->url_canonical = trim($this->getBaseUrl(), '/') .
			Route::_('index.php?option=com_getbible&view=openai&Itemid=' . $this->params->get('app_menu', 0) .
			'&t=' . $this->translation->abbreviation .
			'&guid=' . $this->getGuid() .
			'&book=' . $this->getBook() .
			'&chapter=' . $this->getChapter() .
			'&verse=' . $this->getVerse() .
			'&words=' . $this->getWord());
	}

	/**
	 * Prepare some document related stuff.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function _prepareDocument(): void
	{

		// Only load jQuery if needed. (default is true)
		if ($this->params->get('add_jquery_framework', 1) == 1)
		{
			Html::_('jquery.framework');
		}
		// Load the header checker class.
		// Initialize the header checker.
		$HeaderCheck = new HeaderCheck();

		// Add View JavaScript File
		Html::_('script', "components/com_getbible/assets/js/openai.js", ['version' => 'auto']);

		// Load uikit options.
		$uikit = $this->params->get('uikit_load');
		// Set script size.
		$size = $this->params->get('uikit_min');
		// The uikit css.
		if ((!$HeaderCheck->css_loaded('uikit.min') || $uikit == 1) && $uikit != 2 && $uikit != 3)
		{
			Html::_('stylesheet', 'media/com_getbible/uikit-v3/css/uikit'.$size.'.css', ['version' => 'auto']);
		}
		// The uikit js.
		if ((!$HeaderCheck->js_loaded('uikit.min') || $uikit == 1) && $uikit != 2 && $uikit != 3)
		{
			Html::_('script', 'media/com_getbible/uikit-v3/js/uikit'.$size.'.js', ['version' => 'auto']);
			Html::_('script', 'media/com_getbible/uikit-v3/js/uikit-icons'.$size.'.js', ['version' => 'auto']);
		}
		// add the document default css file
		Html::_('stylesheet', 'components/com_getbible/assets/css/openai.css', ['version' => 'auto']);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('openai');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}
		// now initiate the toolbar
		$this->toolbar = Toolbar::getInstance();
	}

	/**
	 * Get the modules published in a position
	 */
	public function getModules($position, $seperator = '', $class = '')
	{
		// set default
		$found = false;
		// check if we aleady have these modules loaded
		if (isset($this->setModules[$position]))
		{
			$found = true;
		}
		else
		{
			// this is where you want to load your module position
			$modules = ModuleHelper::getModules($position);
			if (ArrayHelper::check($modules, true))
			{
				// set the place holder
				$this->setModules[$position] = [];
				foreach($modules as $module)
				{
					$this->setModules[$position][] = ModuleHelper::renderModule($module);
				}
				$found = true;
			}
		}
		// check if modules were found
		if ($found && isset($this->setModules[$position]) && ArrayHelper::check($this->setModules[$position]))
		{
			// set class
			if (StringHelper::check($class))
			{
				$class = ' class="'.$class.'" ';
			}
			// set seperating return values
			switch($seperator)
			{
				case 'none':
					return implode('', $this->setModules[$position]);
					break;
				case 'div':
					return '<div'.$class.'>'.implode('</div><div'.$class.'>', $this->setModules[$position]).'</div>';
					break;
				case 'list':
					return '<ul'.$class.'><li>'.implode('</li><li>', $this->setModules[$position]).'</li></ul>';
					break;
				case 'array':
				case 'Array':
					return $this->setModules[$position];
					break;
				default:
					return implode('<br />', $this->setModules[$position]);
					break;
			}
		}
		return false;
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var     The output to escape.
	 * @param   bool   $shorten The switch to shorten.
	 * @param   int    $length  The shorting length.
	 *
	 * @return  mixed  The escaped value.
	 * @since   1.6
	 */
	public function escape($var, bool $shorten = false, int $length = 40)
	{
		if (!is_string($var))
		{
			return $var;
		}

		return StringHelper::html($var, $this->_charset ?? 'UTF-8', $shorten, $length);
	}
}
