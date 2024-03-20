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
namespace TrueChristianChurch\Component\Getbible\Site\View\App;

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
use VDM\Joomla\GetBible\Factory as GetBibleFactory;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible Html View class for the App
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
		$this->styles = $this->get('Styles');
		$this->scripts = $this->get('Scripts');
		// get the user object
		$this->user ??= $this->app->getIdentity();
		// Initialise variables.
		$this->item = $this->get('Item');
		$this->chapter = $this->get('Chapter');
		$this->translations = $this->get('Translations');
		$this->books = $this->get('Books');
		$this->chapters = $this->get('Chapters');
		$this->next = $this->get('Next');
		$this->previous = $this->get('Previous');
		$this->translation = $this->get('Translation');
		$this->notes = $this->get('Notes');
		$this->linkernotes = $this->get('LinkerNotes');
		$this->tags = $this->get('Tags');
		$this->taggedverses = $this->get('TaggedVerses');
		$this->prompts = $this->get('Prompts');
		$this->linkertaggedverses = $this->get('LinkerTaggedVerses');
		$this->linkertags = $this->get('LinkerTags');
		// remove from page (in case debug mode is on)
		$this->params->set('openai_token', null);
		$this->params->set('gitea_token', null);
		// get the input values
		$this->input = $this->app->input;
		// only run these if we have an item
		if ($this->item)
		{
			// set the page direction globally
			$this->getDocument()->setDirection($this->translation->direction);
			// set the global language declaration
			// $this->getDocument()->setLanguage($this->translation->joomla); (soon ;)
			// set the linker
			$this->linker = $this->getLinker();
			$this->linker_new = $this->getNewLinker();
			// merge the system and linker
			$this->mergeNotes();
			$this->mergeTags();
			$this->mergeTaggedVerses();
			// should we not have tags at this point we should not load the tag feature
			if (empty($this->tags))
			{
				$this->params->set('activate_tags', null);
			}
			// build the tab menu
			$this->setTabsMenu();
			// set the selected verses
			$this->verses = new \stdClass();
			$this->verses->selected = $this->item->verses ? $this->getSelectedVerses($this->item->verses) : null;
			$this->verses->first = 1;
			$this->verses->last = 2;
			if (!empty($this->verses->selected))
			{
				$this->verses->first = reset($this->verses->selected);
				$this->verses->last = end($this->verses->selected);
			}
			// set the last verse in the chapter
			$this->last_verse = end($this->chapter->verses)->verse;
			$this->active = new \stdClass();
			$this->active->verse = false;
			// check if we have activity active
			if ($this->params->get('activate_notes') == 1 || $this->params->get('activate_tags') == 1 || $this->params->get('activate_sharing') == 1)
			{
				$this->active->verse = true;
				$this->active->target = ($this->params->get('activate_sharing') == 1) ? 'sharing' : (($this->params->get('activate_tags') == 1) ? 'tags' : 'notes');
				$this->active->tooltip = Text::_('COM_GETBIBLE_OPEN');
			}
			// start the modal state
			$this->modalState = new \stdClass();
			// set metadata
			$this->setMetaData();
		}
		else
		{
			$this->tab_name_placeholders = null;
		}
		// we get the verse count if we are going to show the install button
		if ($this->params->get('show_install_button') == 1)
		{
			$this->totalVerse = GetBibleFactory::_('GetBible.Watcher')->totalVerses($this->translation->abbreviation ?? 'kjv');
		}
		// sort translations
		$this->setTranslations();

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
	 * Set the daily verse url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getDailyVerseUrl(): string
	{
		if (empty($this->url_daily))
		{
			$this->setDailyVerseUrl();
		}
		return $this->url_daily ?? '';
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
	 * Get the return url
	 *
	 * @return  string
	 * @since  2.0.1
	 */
	public function getReturnUrl(): string
	{
		if (empty($this->url_return))
		{
			$this->setReturnUrl();
		}
		return '&bibleurl=' . $this->url_return;
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
		$title = Text::sprintf('COM_GETBIBLE_S_CHAPTER_S_S_S',
			$this->chapter->book_name,
			$this->chapter->chapter,
			$this->translation->abbreviation,
			$this->params->get('page_title', '')
		);
		$this->getDocument()->setTitle($title);

		// set the Generator
		$this->getDocument()->setGenerator('getBible! - Open Source Bible App.');

		// set the metadata values
		$description = Text::sprintf('COM_GETBIBLE_READ_S_CHAPTER_S_IN_THE_S',
			$this->chapter->book_name,
			$this->chapter->chapter,
			$this->translation->translation
		);
		$this->item->metadesc = $description;
		$this->item->metakey = Text::sprintf('COM_GETBIBLE_S_CHAPTER_S_S_S_S_S_BIBLE_S_SCRIPTURE_GETBIBLE',
			$this->chapter->book_name,
			$this->chapter->chapter,
			$this->chapter->name,
			$this->translation->translation,
			$this->translation->abbreviation,
			$this->translation->language,
			$this->translation->distribution_lcsh
		);
		$this->item->created_by = Text::_('COM_GETBIBLE_THE_WORD_OF_GOD');

		// set canonical URL
		$this->getDocument()->addHeadLink($this->getBaseUrl(), 'canonical');

		// OG:Title
		$this->getDocument()->setMetadata('og:title', $title, 'property');

		// OG:Description
		$this->getDocument()->setMetadata('og:description', $description, 'property');

		// OG:Image
		// $this->getDocument()->setMetadata('og:image', 'YOUR_IMAGE_URL_HERE', 'property');

		// OG:URL
		$this->getDocument()->setMetadata('og:url', $this->getBaseUrl(), 'property');

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
	 * Set the tabs menu
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setTabsMenu()
	{
		// set the active tab
		$this->tab_menu['active_tab'] = $this->input->getWord('tab', 'scripture');

		// check if we have set this before
		if (!isset($this->tab_menu['scripture_icon']))
		{
			// set the tab icons
			$icon = ($this->params->get('custom_icons') == 1) ? $this->params->get('scripture_icon', 'heart') : 'heart';
			$this->tab_menu['scripture_icon'] = ($this->params->get('show_scripture_icon') == 1) ? ' <span uk-icon="icon: ' . $icon . '"></span> ' : '';
			$icon = ($this->params->get('custom_icons') == 1) ? $this->params->get('translations_icon', 'world') : 'world';
			$this->tab_menu['translations_icon'] = ($this->params->get('show_translations_icon') == 1) ? ' <span uk-icon="icon: ' . $icon . '"></span> ' : '';
			$icon = ($this->params->get('custom_icons') == 1) ? $this->params->get('books_icon', 'album') : 'album';
			$this->tab_menu['books_icon'] = ($this->params->get('show_books_icon') == 1) ? ' <span uk-icon="icon: ' . $icon . '"></span> ' : '';
			$icon = ($this->params->get('custom_icons') == 1) ? $this->params->get('chapters_icon', 'grid') : 'grid';
			$this->tab_menu['chapters_icon'] = ($this->params->get('show_chapters_icon') == 1) ? ' <span uk-icon="icon: ' . $icon . '"></span> ' : '';

			// set the tab names
			$this->tab_menu['scripture'] = ($this->params->get('show_scripture_tab_text') == 1) ? $this->chapter->name . ' (' . $this->chapter->abbreviation . ')' : '';
			$this->tab_menu['translations'] = ($this->params->get('show_translations_tab_text') == 1) ? '[translations]' : '';
			$this->tab_menu['books'] = ($this->params->get('show_books_tab_text') == 1) ? '[books]' : '';
			$this->tab_menu['chapters'] = ($this->params->get('show_chapters_tab_text') == 1) ? '[chapters]' : '';

			// use updated tab names
			if ($this->params->get('set_default_tab_names') == 1)
			{
				$this->tab_menu['scripture'] = $this->params->get('scripture_tab') ?? $this->tab_menu['scripture'];
				$this->tab_menu['translations'] = $this->params->get('translations_tab') ?? $this->tab_menu['translations'];
				$this->tab_menu['books'] = $this->params->get('books_tab') ?? $this->tab_menu['books'];
				$this->tab_menu['chapters'] = $this->params->get('chapters_tab') ?? $this->tab_menu['chapters'];
			}

			// the dynamic placeholders
			$this->tab_name_placeholders = [
				'[translations]' => Text::_('COM_GETBIBLE_TRANSLATIONS'),
				'[books]' => Text::_('COM_GETBIBLE_BOOKS'),
				'[chapters]' => Text::_('COM_GETBIBLE_CHAPTERS'),
				'[details]' => Text::_('COM_GETBIBLE_DETAILS'),
				'[settings]' => Text::_('COM_GETBIBLE_SETTINGS'),
				'[translation]' => $this->chapter->translation,
				'[abbreviation]' => $this->chapter->abbreviation,
				'[lang]' => $this->chapter->lang,
				'[language]' => $this->chapter->language,
				'[book_nr]' => $this->chapter->book_nr,
				'[book_name]' => $this->chapter->book_name,
				'[chapter]' => $this->chapter->chapter,
				'[name]' => $this->chapter->name
			];

			// we do some placeholder updates for the scripture tab
			$this->tab_menu['scripture'] = str_replace(
				array_keys($this->tab_name_placeholders),
				array_values($this->tab_name_placeholders),
				$this->tab_menu['scripture']
			);

			// we do some placeholder updates for the translations tab
			$this->tab_menu['translations'] = str_replace(
				array_keys($this->tab_name_placeholders),
				array_values($this->tab_name_placeholders),
				$this->tab_menu['translations']
			);

			// we do some placeholder updates for the books tab
			$this->tab_menu['books'] = str_replace(
				array_keys($this->tab_name_placeholders),
				array_values($this->tab_name_placeholders),
				$this->tab_menu['books']
			);

			// we do some placeholder updates for the chapters tab
			$this->tab_menu['chapters'] = str_replace(
				array_keys($this->tab_name_placeholders),
				array_values($this->tab_name_placeholders),
				$this->tab_menu['chapters']
			);

			if ($this->params->get('show_settings') == 1)
			{
				$icon = ($this->params->get('custom_icons') == 1) ? $this->params->get('settings_icon', 'settings') : 'settings';
				$this->tab_menu['settings_icon'] = ($this->params->get('show_settings_icon') == 1) ? ' <span uk-icon="icon: ' . $icon . '"></span> ' : '';
				$this->tab_menu['settings'] = ($this->params->get('show_settings_tab_text') == 1) ? $this->params->get('settings_tab') ?? '[settings]' : '';
				// we do some placeholder updates for the settings tab
				$this->tab_menu['settings'] = str_replace(
					array_keys($this->tab_name_placeholders),
					array_values($this->tab_name_placeholders),
					$this->tab_menu['settings']
				);
			}

			if ($this->params->get('show_details') == 1)
			{
				$icon = ($this->params->get('custom_icons') == 1) ? $this->params->get('details_icon', 'info') : 'info';
				$this->tab_menu['details_icon'] = ($this->params->get('show_details_icon') == 1) ? ' <span uk-icon="icon: ' . $icon . '"></span> ' : '';
				$this->tab_menu['details'] = ($this->params->get('show_details_tab_text') == 1) ? $this->params->get('details_tab') ?? '[details]' : '';
				// we do some placeholder updates for the details tab
				$this->tab_menu['details'] = str_replace(
					array_keys($this->tab_name_placeholders),
					array_values($this->tab_name_placeholders),
					$this->tab_menu['details']
				);
			}
		}
	}

	/**
	 * Merge system and linker notes
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function mergeNotes(): void
	{
		$mergedNotes = [];

		// If $this->notes is an array and is not empty, add its elements to $mergedNotes
		if (is_array($this->notes) && $this->notes !== [])
		{
			foreach ($this->notes as $note)
			{
				// Use the 'verse' attribute as the key
				$mergedNotes[$note->verse] = $note;
			}
		}

		// If $this->linkernotes is an array and is not empty, add or replace its elements in $mergedNotes
		if (is_array($this->linkernotes) && $this->linkernotes !== [])
		{
			foreach ($this->linkernotes as $note)
			{
				// If the verse already exists in $mergedNotes, this will replace it
				// If it doesn't exist, this will add it
				$mergedNotes[$note->verse] = $note;
			}
		}

		// update the notes array if we have values
		if ($mergedNotes !== [])
		{
			// Reset the keys to be numeric and start from 0
			$this->notes = array_values($mergedNotes);
		}
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
				$tag->url = Route::_('index.php?option=com_getbible&view=tag&Itemid=' . $this->params->get('app_menu', 0) . $this->getReturnUrl() . '&guid=' . $tag->guid . '&t=' . $this->translation->abbreviation);
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
				$tag->url = Route::_('index.php?option=com_getbible&view=tag&Itemid=' . $this->params->get('app_menu', 0) . $this->getReturnUrl(). '&guid=' . $tag->guid . '&t=' . $this->translation->abbreviation);
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

		// If $this->taggedverses is an array and is not empty, add its elements to $mergeTags
		if (is_array($this->taggedverses) && $this->taggedverses !== [])
		{
			foreach ($this->taggedverses as $tag)
			{
				// we build the key
				$key = $tag->tag . '-' . $tag->verse;
				// set the tag url
				$tag->url = Route::_('index.php?option=com_getbible&view=tag&Itemid=' . $this->params->get('app_menu', 0) . $this->getReturnUrl() . '&guid=' . $tag->tag . '&t=' . $this->translation->abbreviation);
				// Use the 'verse' attribute as the key
				$mergeTags[$key] = $tag;
			}
		}

		// If $this->linkertaggedverses is an array and is not empty, add or replace its elements in $mergeTags
		if (is_array($this->linkertaggedverses) && $this->linkertaggedverses !== [])
		{
			foreach ($this->linkertaggedverses as $tag)
			{
				// we build the key
				$key = $tag->tag . '-' . $tag->verse;
				if ($tag->published != 1)
				{
					// we remove the tag if not published
					unset($mergeTags[$key]);
					continue;
				}
				// set the tag url
				$tag->url = Route::_('index.php?option=com_getbible&view=tag&Itemid=' . $this->params->get('app_menu', 0) . $this->getReturnUrl() . '&guid=' . $tag->tag . '&t=' . $this->translation->abbreviation);
				// If the verse already exists in $mergeTags, this will replace it
				// If it doesn't exist, this will add it
				$mergeTags[$key] = $tag;
			}
		}

		// update the notes array if we have values
		if ($mergeTags !== [])
		{
			// Reset the keys to be numeric and start from 0
			$this->taggedverses = array_values($mergeTags);
		}
	}

	/**
	 * Sort the Translations by Language
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setTranslations(): void
	{
		if ($this->translations)
		{
			$default = $this->params->get('default_translation', 'kjv');
			$bucket = [];
			foreach ($this->translations as $translation)
			{
				// get language name as key
				if (!empty($translation->language))
				{
					$lang = $translation->language;
				}
				elseif ($translation->lang === 'mlf')
				{
					$lang = 'Malayalam';
				}
				else
				{
					$lang = trim(preg_replace('/Bible\.?/', '', $translation->lcsh));
				}
				$bucket[$lang][$translation->translation] = $translation;

				// set the active language
				if ($translation->abbreviation === $this->chapter->abbreviation)
				{
					$this->activeLanguage = $lang;
				}
				// set the default translation
				elseif ($translation->abbreviation === $default)
				{
					$this->defaultTranslation = $translation;
				}
			}

			// Sort $bucket multidimensional array
			foreach($bucket as $language => $translations)
			{
				// Sort translations for each language
				uksort($translations, function($a, $b) {
					// using strcmp for comparison
					return strcmp($a, $b);
				});
				// Replace the original translations array with the sorted one
				$bucket[$language] = $translations;
			}

			// Sort languages
			uksort($bucket, function($a, $b) {
				return strcmp($a, $b);
			});

			$this->languages = $bucket;
		}
	}

	/**
	 * Set the base url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setBaseUrl()
	{
		$this->url_base = trim(Uri::base(), '/') . Route::_('index.php?option=com_getbible&view=app&t=' . $this->chapter->abbreviation . '&ref=' . $this->chapter->book_name  . '&c=' . $this->chapter->chapter);
	}

	/**
	 * Set the daily verse url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setDailyVerseUrl()
	{
		$this->url_daily = Route::_('index.php?option=com_getbible&view=app&Itemid=' . $this->params->get('app_menu', 0) . '&t=' . $this->translation->abbreviation);
	}

	/**
	 * Set the AJAX url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setAjaxUrl()
	{
		$this->url_ajax = Uri::base() . 'index.php?option=com_getbible&format=json&raw=true&' . Session::getFormToken() . '=1&task=ajax.';
	}

	/**
	 * Set the search url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setSearchUrl()
	{
		if ($this->params->get('activate_search') != 1)
		{
			return;
		}

		$words = $this->params->get('search_word', 1);
		$match = $this->params->get('search_match', 1);
		$case = $this->params->get('search_case', 1);

		// set the current search URL
		$this->url_search = Route::_('index.php?option=com_getbible&view=search&t=' . $this->translation->abbreviation . $this->getReturnUrl() . '&words=' . $words . '&match=' . $match . '&case=' . $case . '&target=1000');
	}

	/**
	 * Set the return url
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	protected function setReturnUrl()
	{
		// set the current return URL
		$this->url_return = urlencode(base64_encode($this->getBaseUrl()));
	}

	/**
	 * Check if the verse is a selected verse
	 *
	 * @param   int  $number  The verse number to check
	 *
	 * @return  bool  True if verse is selected
	 * @since  2.0.1
	 */
	protected function selectedVerse(int $number): bool
	{
		if ($this->verses->selected && in_array($number, $this->verses->selected))
		{
			return true;
		}

		return false;
	}

	/**
	 * Check if the verse is a tagged verse
	 *
	 * @param   int  $number  The verse number to check
	 *
	 * @return  int  1 = Active, 0 = no tags, -1 = Inactive
	 * @since  2.0.1
	 */
	protected function taggedVerse(int $number): int
	{
		// Check the global activation status
		if ($this->params->get('activate_tags') != 1)
		{
			return 0; // Tags are globally inactive
		}

		// Check if taggedVerses is a non-empty array
		if (!is_array($this->taggedverses) || empty($this->taggedverses))
		{
			return -1; // No active tags
		}

		// Iterate over taggedVerses to find a match
		foreach ($this->taggedverses as $tag)
		{
			// Check if verse property exists and matches the input number
			if (property_exists($tag, 'verse') && is_numeric($tag->verse) && (int) $tag->verse === $number)
			{
				return 1; // Verse has an active tag active
			}
		}

		// Verse is inactive if no match is found
		return -1; 
	}

	/**
	 * Check if the verse has a note
	 *
	 * @param   int  $number  The verse number to check
	 *
	 * @return  string|null  True if verse has a note
	 * @since  2.0.1
	 */
	protected function getVerseNote(int $number): ?string
	{
		if ($this->notes && is_array($this->notes) && $this->notes !== [])
		{
			foreach ($this->notes as $note)
			{
				if (isset($note->verse) && is_numeric($note->verse) && (int) $note->verse === $number &&
					isset($note->note) && strlen($note->note) > 0 && ($this->params->get('activate_notes', 1) == 1))
				{
					return $note->note;
				}
			}
		}

		return null;
	}

	/**
	 * Get the selected verses as an array
	 *
	 * @param   string  $verses  The string of verses reference
	 *
	 * @return  array  The array of verses
	 * @since  2.0.1
	 */
	protected function getSelectedVerses(string $verses): array
	{
		$result = [];

		$parts = explode(',', $verses);
		foreach ($parts as $part)
		{
			if (strpos($part, '-') !== false)
			{
				list($start, $end) = explode('-', $part);
				for ($i = (int)$start; $i <= (int)$end; $i++)
				{
					$result[] = $i;
				}
			}
			else
			{
				$result[] = (int)$part;
			}
		}

		return $result;
	}

	/**
	 * Get the targeted integration areas prompts
	 *
	 * @param   array|null  $prompts  The array of prompts
	 * @param   array       $targets  The array targets
	 *
	 * @return  array|null  The targeted prompts
	 * @since  2.0.1
	 */
	protected function promptIntegration($prompts, array $targets): ?array
	{
		if (is_array($prompts) && $prompts !== [] && $targets !== [])
		{
			$found = [];
			foreach ($prompts as $prompt)
			{
				if (in_array($prompt->integration, $targets))
				{
					$found[] = $prompt;
				}
			}

			if ($found !== [])
			{
				return $found;
			}
		}

		return null;
	}

	/**
	 * Get the Linker Details
	 *
	 * @return  array  The linker array.
	 * @since  2.0.1
	 */
	protected function getLinker(): array
	{
		return GetBibleFactory::_('GetBible.Linker')->activeDetails();
	}

	/**
	 * Get a new Linker GUID
	 *
	 * @return  string  The linker guid.
	 * @since  2.0.1
	 */
	protected function getNewLinker(): string
	{
		return GetBibleFactory::_('GetBible.Linker')->getNew();
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

		// always load these files.
		Html::_('stylesheet', "media/com_getbible/nouislider/css/nouislider.min.css", ['version' => 'auto']);
		Html::_('script', "media/com_getbible/nouislider/js/nouislider.min.js", ['version' => 'auto']);

		// Add View JavaScript File
		Html::_('script', "components/com_getbible/assets/js/app.js", ['version' => 'auto']);

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
		// load the meta description
		if (isset($this->item->metadesc) && $this->item->metadesc)
		{
			$this->getDocument()->setDescription($this->item->metadesc);
		}
		elseif ($this->params->get('menu-meta_description'))
		{
			$this->getDocument()->setDescription($this->params->get('menu-meta_description'));
		}
		// load the key words if set
		if (isset($this->item->metakey) && $this->item->metakey)
		{
			$this->getDocument()->setMetadata('keywords', $this->item->metakey);
		}
		elseif ($this->params->get('menu-meta_keywords'))
		{
			$this->getDocument()->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}
		// check the robot params
		if (isset($this->item->robots) && $this->item->robots)
		{
			$this->getDocument()->setMetadata('robots', $this->item->robots);
		}
		elseif ($this->params->get('robots'))
		{
			$this->getDocument()->setMetadata('robots', $this->params->get('robots'));
		}
		// check if autor is to be set
		if (isset($this->item->created_by) && $this->params->get('MetaAuthor') == '1')
		{
			$this->getDocument()->setMetaData('author', $this->item->created_by);
		}
		// check if metadata is available
		if (isset($this->item->metadata) && $this->item->metadata)
		{
			$mdata = json_decode($this->item->metadata,true);
			foreach ($mdata as $k => $v)
			{
				if ($v)
				{
					$this->getDocument()->setMetadata($k, $v);
				}
			}
		}
		// get color
		$verse_selected_color = $this->params->get('verse_selected_color', '#4747ff');
		
		// get search defaults
		$search_words = $this->params->get('search_words', 1);
		$search_match = $this->params->get('search_match', 1);
		$search_case = $this->params->get('search_case', 1);
		$translation = $this->translation->abbreviation;
		$book_nr = $this->chapter->book_nr ?? 0;
		$chapter_nr = $this->chapter->chapter ?? 0;
		
		// set the ajax url
		$url_ajax = $this->getAjaxUrl();
		
		// set some lang
		Text::script('COM_GETBIBLE_VIEW_ALL_VERSES_TAGGED');
		Text::script('COM_GETBIBLE_EDIT_TAG');
		// add styles
		foreach ($this->styles as $style)
		{
			Html::_('stylesheet', $style, ['version' => 'auto']);
		}
		// Set the Custom CSS script to view
		$this->document->addStyleDeclaration("
			.getbible-verse-selected {
				font-weight: bolder;
				color: $verse_selected_color;
			}
			html {
			    scroll-behavior: smooth;
			}
		");
		// add scripts
		foreach ($this->scripts as $script)
		{
			Html::_('script', $script, ['version' => 'auto']);
		}
		// Set the Custom JS script to view
		$this->getDocument()->addScriptDeclaration("
			const UrlAjax = '$url_ajax';
			const getShareHisWordUrl = (linker, translation, book, chapter) => {
				// build share His Word url
				return UrlAjax +
					'getShareHisWordUrl&translation=' + translation +
					'&linker=' + linker + '&book=' + book +
					'&chapter=' + chapter;
			};
			const getCheckValidLinkerUrl = (linker, oldLinker) => {
				// build share His Word url
				return UrlAjax +
					'checkValidLinker&linker=' + linker + '&old=' + oldLinker;
			};
			const getSearchURL = (search, translation) => {
				// build search url
				return UrlAjax +
					'getSearchUrl&translation=' + translation +
					'&book=$book_nr&chapter=$chapter_nr' +
					'&words=$search_words&match=$search_match' +
					'&case=$search_case&target=1000' +
					'&target_book=0&search=' + search;
			};
			const getOpenaiURL = (guid, words, verse, chapter, book, translation) => {
				// build open ai url
				return UrlAjax +
					'getOpenaiURL&translation=' + translation +
					'&guid=' + guid + '&book=' + book +
					'&chapter=' + chapter + '&verse=' + verse +
					'&words=' + words;
			};
			const getSetLinkerURL = (linker) => {
				// build set linker url
				return UrlAjax +
					'setLinker&linker=' + linker;
			};
			const getIsLinkerAuthenticatedURL = (linker) => {
				// build is linker authenticated url
				return UrlAjax +
					'isLinkerAuthenticated&linker=' + linker;
			};
			const revokeLinkerSessionURL = () => {
				// build set linker revoke access url
				return UrlAjax + 'revokeLinkerSession';
			};
			const getSetLinkerAccessURL = () => {
				// build set linker access url
				return UrlAjax + 'setLinkerAccess';
			};
			const revokeLinkerAccessURL = () => {
				// build set linker revoke access url
				return UrlAjax + 'revokeLinkerAccess';
			};
			const setLinkerNameURL = () => {
				// build set linker access url
				return UrlAjax + 'setLinkerName';
			};
			const getSetNoteURL = () => {
				// build set note url
				return UrlAjax + 'setNote';
			};
			const getCreateTagURL = () => {
				// build create tag url
				return UrlAjax + 'createTag';
			};
			const getUpdateTagURL = () => {
				// build create tag url
				return UrlAjax + 'updateTag';
			};
			const getDeleteTagURL = () => {
				// build create tag url
				return UrlAjax + 'deleteTag';
			};
			const getTagVerseURL = (translation, book, chapter, verse, tag) => {
				// build set tag url
				return UrlAjax +
					'tagVerse&translation=' + translation + '&book=' + book + '&chapter=' + chapter +
					'&verse=' + verse + '&tag=' + tag;
			};
			const getRemoveTagFromVerseURL = (tag) => {
				// build set tag url
				return UrlAjax +
					'removeTagFromVerse&tag=' + tag;
			};
			const installBibleChapterURL = (() => {
				let isFirstCall = true;
				return (translation, book, chapter) => {
					// build load Bible url
					let url = UrlAjax + 'installBibleChapter&translation=' + translation +
						'&book=' + book + '&chapter=' + chapter;
					// Append \"&force=1\" if it is the first call
					if (isFirstCall) {
						url += '&force=1';
						isFirstCall = false;
					}
			
					return url;
				};
			})();
			const getLinkersDisplayURL = () => {
				// build load Bible url
				return UrlAjax + 'getLinkersDisplay';
			};
		");
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
		$this->help_url = GetbibleHelper::getHelpUrl('app');
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
