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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ModuleHelper;
use VDM\Joomla\GetBible\Factory as GetBibleFactory;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper;

/**
 * Getbible Html View class for the Search
 */
class GetbibleViewSearch extends HtmlView
{
	// Overwriting JView display method
	function display($tpl = null)
	{
		// get combined params of both component and menu
		$this->app = Factory::getApplication();
		$this->params = $this->app->getParams();
		$this->menu = $this->app->getMenu()->getActive();
		// get the user object
		$this->user = Factory::getUser();
		// Initialise variables.
		$this->items = $this->get('Items');
		$this->translations = $this->get('Translations');
		$this->books = $this->get('Books');
		$this->translation = $this->get('Translation');
		// remove from page (in case debug mode is on)
		$this->params->set('openai_token', null);
		$this->params->set('gitea_token', null);
		// set the input object
		$this->input = $this->app->input;
		if ($this->params->get('activate_search') == 1)
		{
			// set the page direction globally
			$this->document->setDirection($this->translation->direction);
			// set the global language declaration
			// $this->document->setLanguage($this->translation->joomla); (soon ;)
			// set the enough verses witch
			$this->enoughVerses = GetBibleFactory::_('GetBible.Watcher')->enoughVerses($this->translation->abbreviation ?? 'kjv');
			// set metadata
			$this->setMetaData();
		}

		// Set the toolbar
		$this->addToolBar();

		// set the document
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
		// set the page title
		$title = Text::sprintf('COM_GETBIBLE_SEARCHING_S_IN_S_S',
			$this->getSearch(),
			$this->translation->translation,
			$this->params->get('page_title', '')
		);
		$this->document->setTitle($title);
		$url =  $this->getCanonicalUrl();
		// set the Generator
		$this->document->setGenerator('getBible! - Open Source Bible App.');

		// set the metadata values
		$description = Text::sprintf('COM_GETBIBLE_SEARCHING_S_IN_S_TARGETING_S_WITH_S_S_IN_S',
			$this->getSearch(),
			$this->translation->translation,
			strtolower($this->getWordsText()),
			strtolower($this->getMatchText()),
			strtolower($this->getCaseText()),
			$this->getTargetText()
		);
		$this->document->setDescription($description);
		$this->document->setMetadata('keywords', Text::sprintf('COM_GETBIBLE_SEARCH_S_S_S_S_S_S_BIBLE_S_S_SCRIPTURE_SEARCH_GETBIBLE',
			$this->getSearch(),
			strtolower($this->getWordsText()),
			strtolower($this->getMatchText()),
			strtolower($this->getCaseText()),
			$this->getTargetText(),
			$this->translation->translation,
			$this->translation->abbreviation,
			$this->translation->language
		));
		$this->document->setMetaData('author', Text::_('COM_GETBIBLE_THE_WORD_OF_GOD'));

		// set canonical URL
		$this->document->addHeadLink($url, 'canonical');

		// OG:Title
		$this->document->setMetadata('og:title', $title, 'property');

		// OG:Description
		$this->document->setMetadata('og:description', $description, 'property');

		// OG:Image
		// $this->document->setMetadata('og:image', 'YOUR_IMAGE_URL_HERE', 'property');

		// OG:URL
		$this->document->setMetadata('og:url', $url, 'property');

		// OG:Type
		$this->document->setMetadata('og:type', 'website', 'property');

		// Twitter Card Type
		$this->document->setMetadata('twitter:card', 'summary');

		// Twitter Title
		$this->document->setMetadata('twitter:title', $title);

		// Twitter Description
		$this->document->setMetadata('twitter:description', $description);

		// Twitter Image
		// $this->document->setMetadata('twitter:image', 'YOUR_IMAGE_URL_HERE');

		// Twitter Site (Your website's Twitter handle)
		// $this->document->setMetadata('twitter:site', '@YourTwitterHandle');

		// Twitter Creator (Author's Twitter handle or your website's Twitter handle)
		// $this->document->setMetadata('twitter:creator', '@AuthorTwitterHandle');
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
	 * Get the search url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getSearchUrl(): string
	{
		if (empty($this->url_search))
		{
			$this->setSearchUrl();
		}
		return $this->url_search ?? '';
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
	 * Get the Options Search Behaviour (text)
	 *
	 * @return  array
	 * @since  2.0.1
	 */
	public function getOptionsText(): array
	{
		if (empty($this->options_text))
		{
			$this->setOptionsText();
		}
		return $this->options_text;
	}

	/**
	 * Get the Words Search Behaviour
	 *
	 * @return  int
	 * @since  2.0.1
	 */
	public function getWords(): int
	{
		if (empty($this->words))
		{
			$this->setWords();
		}
		return $this->words ?? 1;
	}

	/**
	 * Get the Words Search Behaviour
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getWordsText(): string
	{
		if (empty($this->words_text))
		{
			$this->setWordsText();
		}
		return $this->words_text;
	}

	/**
	 * Get the Match Search Behaviour
	 *
	 * @return  int
	 * @since  2.0.1
	 */
	public function getMatch(): int
	{
		if (empty($this->match))
		{
			$this->setMatch();
		}
		return $this->match ?? 1;
	}

	/**
	 * Get the Match Search Behaviour (text)
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getMatchText(): string
	{
		if (empty($this->match_text))
		{
			$this->setMatchText();
		}
		return $this->match_text;
	}

	/**
	 * Get the Case Search Behaviour
	 *
	 * @return  int
	 * @since  2.0.1
	 */
	public function getCase(): int
	{
		if (empty($this->case))
		{
			$this->setCase();
		}
		return $this->case ?? 1;
	}

	/**
	 * Get the Case Search Behaviour (text)
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getCaseText(): string
	{
		if (empty($this->case_text))
		{
			$this->setCaseText();
		}
		return $this->case_text;
	}

	/**
	 * Get the Target Search Behaviour
	 *
	 * @return  int
	 * @since  2.0.1
	 */
	public function getTarget(): int
	{
		if (empty($this->target))
		{
			$this->setTarget();
		}
		return $this->target ?? 1000;
	}

	/**
	 * Get the Target Search Behaviour (text)
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getTargetText(): string
	{
		if (empty($this->target_text))
		{
			$this->setTargetText();
		}
		return $this->target_text;
	}

	/**
	 * Get the Search String
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getSearch(): string
	{
		if (empty($this->search))
		{
			$this->setSearch();
		}
		return $this->search;
	}

	/**
	 * Set the Search String
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setSearch()
	{
		$this->search = $this->input->getString('search') ?? $this->input->getString('s') ?? '';
	}

	/**
	 * Set the Case Search Behaviour
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setTarget()
	{
		$target = $this->input->getInt('target', 1000);
		$this->target = ($target == 4000) ? $this->input->getInt('target_book', $target) : $target;
	}

	/**
	 * Set the Target Search Behaviour (text)
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	public function setTargetText()
	{
		// set the value names
		$target = [
			1000 => Text::_('COM_GETBIBLE_ALL_BOOKS'),
			2000 => Text::_('COM_GETBIBLE_OLD_TESTAMENT'),
			3000 => Text::_('COM_GETBIBLE_NEW_TESTAMENT'),
			4000 => Text::_('COM_GETBIBLE_A_BOOK')
		];

		$this->target_text = $target[$this->getTarget()] ?? Text::_('COM_GETBIBLE_A_BOOK');
	}

	/**
	 * Set the Case Search Behaviour
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setCase()
	{
		$this->case = $this->input->getInt('case', $this->params->get('search_case', 1));
	}

	/**
	 * Set the Case Search Behaviour (text)
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setCaseText()
	{
		// set the value names
		$case = [
			1 => Text::_('COM_GETBIBLE_CASE_INSENSITIVE'),
			2 => Text::_('COM_GETBIBLE_CASE_SENSITIVE')
		];

		$this->case_text = $case[$this->getCase()] ?? Text::_('COM_GETBIBLE_CASE_INSENSITIVE');
	}

	/**
	 * Set the Match Search Behaviour
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setMatch()
	{
		$this->match = $this->input->getInt('match', $this->params->get('search_match', 1));
	}

	/**
	 * Set the Match Search Behaviour (text)
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setMatchText()
	{
		// set the value names
		$match = [
			1 => Text::_('COM_GETBIBLE_EXACT_MATCH'),
			2 => Text::_('COM_GETBIBLE_PARTIAL_MATCH')
		];

		$this->match_text = $match[$this->getMatch()] ?? Text::_('COM_GETBIBLE_EXACT_MATCH');
	}

	/**
	 * Set the Words Search Behaviour
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setWords()
	{
		$this->words = $this->input->getInt('words', $this->params->get('search_word', 1));
	}

	/**
	 * Set the Words Search Behaviour (text)
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setWordsText()
	{
		// set the value names
		$words = [
			1 => Text::_('COM_GETBIBLE_ALL_WORDS'),
			2 => Text::_('COM_GETBIBLE_ANY_WORDS'),
			3 => Text::_('COM_GETBIBLE_EXACT_WORDS')
		];

		$this->words_text = $words[$this->getWords()] ?? Text::_('COM_GETBIBLE_ALL_WORDS');
	}

	/**
	 * Set the Options Search Behaviour (text)
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setOptionsText()
	{
		$this->options_text = [];
		$this->options_text[] = $this->getWordsText();
		$this->options_text[] = $this->getMatchText();
		$this->options_text[] = $this->getCaseText();
		$this->options_text[] = $this->getTargetText();
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
		$uri = JUri::getInstance($decodedUrl);
		$router = JRouter::getInstance('site');

		$this->url_return_value = $encodedUrl;
		$this->url_return = $decodedUrl;
		$this->url_return_query = $router->parse($uri);
	}

	/**
	 * Set the base url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBaseUrl()
	{
		$this->url_base = JUri::base();
	}

	/**
	 * Set the AJAX url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setAjaxUrl()
	{
		$this->url_ajax = $this->getBaseUrl() . 'index.php?option=com_getbible&format=json&raw=true&' . JSession::getFormToken() . '=1&task=ajax.';
	}

	/**
	 * Set the search url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setSearchUrl()
	{
		// set the current search URL
		$this->url_search = JRoute::_('index.php?option=com_getbible&view=search&Itemid=' . $this->params->get('app_menu', 0) .
			'&t=' . $this->translation->abbreviation . $this->getReturnUrlValue() .
			'&words=' . $this->getWords() . '&match=' . $this->getMatch() .
			'&case=' . $this->getCase() . '&target=' . $this->getTarget() . '&search=' . $this->getSearch());
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
			JRoute::_('index.php?option=com_getbible&view=search&Itemid=' . $this->params->get('app_menu', 0) .
			'&t=' . $this->translation->abbreviation . '&words=' . $this->getWords() .
			'&match=' . $this->getMatch() . '&case=' . $this->getCase() .
			'&target=' . $this->getTarget() . '&search=' . $this->getSearch());
	}

	/**
	 * Set the Bible url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBibleUrl()
	{
		$this->url_bible = $this->getReturnUrl() ?? JRoute::_('index.php?option=com_getbible&view=app&Itemid=' . $this->params->get('app_menu', 0) . '&t=' . $this->translation->abbreviation);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{

		// Only load jQuery if needed. (default is true)
		if ($this->params->get('add_jquery_framework', 1) == 1)
		{
			Html::_('jquery.framework');
		}
		// Load the header checker class.
		require_once( JPATH_COMPONENT_SITE.'/helpers/headercheck.php' );
		// Initialize the header checker.
		$HeaderCheck = new getbibleHeaderCheck();

		// always load these files.
		Html::_('stylesheet', "media/com_getbible/datatable/css/datatables.min.css", ['version' => 'auto']);
		Html::_('script', "media/com_getbible/datatable/js/pdfmake.min.js", ['version' => 'auto']);
		Html::_('script', "media/com_getbible/datatable/js/vfs_fonts.js", ['version' => 'auto']);
		Html::_('script', "media/com_getbible/datatable/js/datatables.min.js", ['version' => 'auto']);

		// Add View JavaScript File
		Html::_('script', "components/com_getbible/assets/js/search.js", ['version' => 'auto']);

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
		$search_found_color = $this->params->get('search_found_color', '#4747ff');
		$table_selection_color = $this->params->get('table_selection_color', '#dfdfdf');
		
		$url_search = $this->getSearchUrl();
		$url_ajax = $this->getAjaxUrl();
		$book = $this->getReturnUrlBook();
		$chapter = $this->getReturnUrlChapter();
		// add the document default css file
		Html::_('stylesheet', 'components/com_getbible/assets/css/search.css', ['version' => 'auto']);
		// Set the Custom CSS script to view
		$this->document->addStyleDeclaration("
			.uk-table tr {
				cursor: pointer;
			}
			.uk-table table.dataTable tbody > tr.selected, .uk-table table.dataTable tbody > tr > .selected {
				background-color: $table_selection_color;
			}
			.uk-table tr.selected td {
				background-color: $table_selection_color;
			}
			.getbible-word-found {
				font-weight: bolder;
				color: $search_found_color;
			}
			.direction-rtl {
				direction: rtl;
				text-align: right;
				unicode-bidi: bidi-override;
			}
			.direction-ltr {
				direction: ltr;
				text-align: left;
				unicode-bidi: bidi-override;
			}
		");
		// Set the Custom JS script to view
		$this->document->addScriptDeclaration("
			const urlSearch = '$url_search';
			const urlAjax = '$url_ajax';
			const getAppURL = (book, chapter, verse, translation = 'kjv') => {
				// build search url
				return urlAjax +
					'getAppUrl&translation=' + urlencode(translation) +
					'&book=' + book +
					'&chapter=' + chapter +
					'&verse=' + verse;
			};
			const getSearchURL = (search, words = 1, match = 1, type_case = 1, target = 1000, translation = 'kjv') => {
				// build search url
				return urlAjax +
					'getSearchUrl&translation=' + urlencode(translation) +
					'&words=' + words +
					'&match=' + match +
					'&case=' + type_case +
					'&target=' + target +
					'&search=' + urlencode(search) +
					'&target_book=0' +
					'&book=$book&chapter=$chapter';
			};
		");
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('search');
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
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var, $sorten = false, $length = 40)
	{
		// use the helper htmlEscape method instead.
		return StringHelper::html($var, $this->_charset, $sorten, $length);
	}
}
