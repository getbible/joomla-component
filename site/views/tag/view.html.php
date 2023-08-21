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
use Joomla\CMS\Helper\ModuleHelper as JModuleHelper;
use VDM\Joomla\GetBible\Factory;

use Joomla\CMS\MVC\View\HtmlView;

/**
 * Getbible Html View class for the Tag
 */
class GetbibleViewTag extends HtmlView
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		// get combined params of both component and menu
		$this->app = JFactory::getApplication();
		$this->params = $this->app->getParams();
		$this->menu = $this->app->getMenu()->getActive();
		// get the user object
		$this->user = JFactory::getUser();
		// Initialise variables.
		$this->items = $this->get('Items');
		$this->translation = $this->get('Translation');
		$this->tags = $this->get('Tags');
		$this->linkertags = $this->get('LinkerTags');
		$this->tag = $this->get('Tag');
		$this->linkertagged = $this->get('LinkerTagged');
		// remove from page (in case debug mode is on)
		$this->params->set('openai_token', null);
		$this->params->set('gitea_token', null);
		// set the input object
		$this->input = $this->app->input;
		// should we not have tags at this point we should not load the tag feature
		if (empty($this->tags))
		{
			$this->params->set('activate_tags', null);
		}
		else
		{
			$this->mergeTags();
		}
		// check if we have some tagged verses
		if (!empty($this->items) || !empty($this->linkertagged))
		{
			// set the page direction globally
			$this->document->setDirection($this->translation->direction);
			// set the global language declaration
			// $this->document->setLanguage($this->translation->joomla); (soon ;)
			// set the linker
			$this->linker = $this->getLinker();
			// merge the system and linker
			$this->mergeTaggedVerses();
			// see if we have any tagged verses left
			if (!empty($this->items))
			{
				// sort the tagged verses in to paragraphs
				$this->items = Factory::_('GetBible.Tagged.Paragraphs')->get($this->items, $this->translation->abbreviation ?? 'kjv');
				// set sorting books option
				$this->setBooks();
			}
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
			throw new Exception(implode(PHP_EOL, $errors), 500);
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
		$title = JText::sprintf('COM_GETBIBLE_TAG_S_IN_S_S',
			$this->tag->name,
			$this->translation->translation,
			$this->params->get('page_title', '')
		);
		$this->document->setTitle($title);
		$url =  $this->getCanonicalUrl();
		// set the Generator
		$this->document->setGenerator('getBible! - Open Source Bible App.');

		// set the metadata values
		$description = JText::sprintf('COM_GETBIBLE_TAG_S_S',
			$this->tag->name, 
			$this->tag->description,
		);
		$this->document->setDescription($description);
		$this->document->setMetadata('keywords', JText::sprintf('COM_GETBIBLE_TAG_S_S_BIBLE_S_S_SCRIPTURE_TAG_GETBIBLE',
			$this->tag->name,
			$this->translation->translation,
			$this->translation->abbreviation,
			$this->translation->language
		));
		$this->document->setMetaData('author', JText::_('COM_GETBIBLE_THE_WORD_OF_GOD'));

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
		return $this->url_canonical ?? $this->getTagUrl();
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
	 * Get the tag url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getTagUrl(): string
	{
		if (empty($this->url_tag))
		{
			$this->setTagUrl();
		}
		return $this->url_tag ?? $this->getBaseUrl();
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
			return '&return=' . $this->url_return_value;
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
	 * Set the return URL if it's provided and internal.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setReturnUrl(): void
	{
		$encodedUrl = $this->input->get('return', null, 'base64');

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
	 * Set the tag url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setTagUrl()
	{
		// set the current tag URL 
		$this->url_tag = JRoute::_('index.php?option=com_getbible&view=tag&Itemid=' .
			$this->params->get('app_menu', 0) . $this->getReturnUrlValue() .
			'&guid=' . $this->tag->guid . '&t=' . $this->translation->abbreviation);
	}

	/**
	 * Set the canonical url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setCanonicalUrl()
	{
		// set the current tag URL
		$this->url_canonical = trim($this->getBaseUrl(), '/') .
			JRoute::_('index.php?option=com_getbible&view=tag&Itemid=' .
			$this->params->get('app_menu', 0) .
			'&guid=' . $this->tag->guid .
			'&t=' . $this->translation->abbreviation);
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
	 * Merge system and linker tags
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function mergeTags(): void
	{
		$mergeTags = [];

		// If $this->tags is an array and is not empty, add its elements to $mergeTags
		if (is_array($this->tags) && $this->tags !== [])
		{
			foreach ($this->tags as $tag)
			{
				// set the tag url
				$tag->url = JRoute::_('index.php?option=com_getbible&view=tag&Itemid=' . $this->params->get('app_menu', 0) . $this->getReturnUrlValue() . '&guid=' . $tag->guid . '&t=' . $this->translation->abbreviation);
				// Use the 'verse' attribute as the key
				$mergeTags[$tag->id] = $tag;
			}
		}

		// If $this->linkertags is an array and is not empty, add or replace its elements in $mergeTags
		if (is_array($this->linkertags) && $this->linkertags !== [])
		{
			foreach ($this->linkertags as $tag)
			{
				if ($tag->published != 1)
				{
					// we remove the tag if not published
					unset($mergeTags[$tag->id]);
					continue;
				}
				// set the tag url
				$tag->url = JRoute::_('index.php?option=com_getbible&view=tag&Itemid=' . $this->params->get('app_menu', 0) . $this->getReturnUrlValue() . '&guid=' . $tag->guid . '&t=' . $this->translation->abbreviation);
				// If the verse already exists in $mergeTags, this will replace it
				// If it doesn't exist, this will add it
				$mergeTags[$tag->id] = $tag;
			}
		}

		// update the notes array if we have values
		if ($mergeTags !== [])
		{
			usort($mergeTags, function($a, $b) {
				return strcmp($a->name, $b->name);
			});
			// Reset the keys to be numeric and start from 0
			$this->tags = array_values($mergeTags);
		}
	}

	/**
	 * Merge system and linker tagged verses
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function mergeTaggedVerses(): void
	{
		$mergeTags = [];

		// If this->items is an array and is not empty, add its elements to $mergeTags
		foreach ($this->items as $tag)
		{
			// we build the key
			$key = $tag->book_nr . '-' . $tag->chapter . '-' . $tag->verse . '_' . $tag->tag;
			// Use the 'verse' attribute as the key
			$mergeTags[$key] = $tag;
		}

		// If $this->linkertagged is an array and is not empty, add or replace its elements in $mergeTags
		if (is_array($this->linkertagged) && $this->linkertagged !== [])
		{
			foreach ($this->linkertagged as $tag)
			{
				// we build the key
				$key = $tag->book_nr . '-' . $tag->chapter . '-' . $tag->verse . '_' . $tag->tag;
				if ($tag->published != 1)
				{
					// we remove the tag if not published
					unset($mergeTags[$key]);
					continue;
				}
				// If the verse already exists in $mergeTags, this will replace it
				// If it doesn't exist, this will add it
				$mergeTags[$key] = $tag;
			}
		}

		// update the notes array if we have values
		if ($mergeTags !== [])
		{
			// Reset the keys to be numeric and start from 0
			$this->items = array_values($mergeTags);
		}
	}

	/**
	 * Set the books
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBooks(): void
	{
		$this->books = [];
		if (!empty($this->items))
		{
			foreach ($this->items as $item)
			{
				$this->books[$item['data_book']] = $item['book'];
			}
		}
	}

	/**
	 * Get the Linker Details
	 *
	 * @return  array  The linker array.
	 * @since  2.0.1
	 */
	protected function getLinker(): array
	{
		return Factory::_('GetBible.Linker')->activeDetails();
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{

		// Only load jQuery if needed. (default is true)
		if ($this->params->get('add_jquery_framework', 1) == 1)
		{
			JHtml::_('jquery.framework');
		}
		// Load the header checker class.
		require_once( JPATH_COMPONENT_SITE.'/helpers/headercheck.php' );
		// Initialize the header checker.
		$HeaderCheck = new getbibleHeaderCheck;

		// always load these files.
		JHtml::_('stylesheet', "media/com_getbible/nouislider/css/nouislider.min.css", ['version' => 'auto']);
		JHtml::_('script', "media/com_getbible/nouislider/js/nouislider.min.js", ['version' => 'auto']);

		// Load uikit options.
		$uikit = $this->params->get('uikit_load');
		// Set script size.
		$size = $this->params->get('uikit_min');
		// The uikit css.
		if ((!$HeaderCheck->css_loaded('uikit.min') || $uikit == 1) && $uikit != 2 && $uikit != 3)
		{
			JHtml::_('stylesheet', 'media/com_getbible/uikit-v3/css/uikit'.$size.'.css', ['version' => 'auto']);
		}
		// The uikit js.
		if ((!$HeaderCheck->js_loaded('uikit.min') || $uikit == 1) && $uikit != 2 && $uikit != 3)
		{
			JHtml::_('script', 'media/com_getbible/uikit-v3/js/uikit'.$size.'.js', ['version' => 'auto']);
			JHtml::_('script', 'media/com_getbible/uikit-v3/js/uikit-icons'.$size.'.js', ['version' => 'auto']);
		}
		// add the document default css file
		JHtml::_('stylesheet', 'components/com_getbible/assets/css/tag.css', ['version' => 'auto']);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		
		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('tag');
		if (GetbibleHelper::checkString($this->help_url))
		{
			JToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}
		// now initiate the toolbar
		$this->toolbar = JToolbar::getInstance();
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
			$modules = JModuleHelper::getModules($position);
			if (GetbibleHelper::checkArray($modules, true))
			{
				// set the place holder
				$this->setModules[$position] = array();
				foreach($modules as $module)
				{
					$this->setModules[$position][] = JModuleHelper::renderModule($module);
				}
				$found = true;
			}
		}
		// check if modules were found
		if ($found && isset($this->setModules[$position]) && GetbibleHelper::checkArray($this->setModules[$position]))
		{
			// set class
			if (GetbibleHelper::checkString($class))
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
		return GetbibleHelper::htmlEscape($var, $this->_charset, $sorten, $length);
	}
}
