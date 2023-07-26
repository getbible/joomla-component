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

use Joomla\CMS\MVC\View\HtmlView;
use VDM\Joomla\GetBible\Factory;

/**
 * Getbible Html View class for the Search
 */
class GetbibleViewSearch extends HtmlView
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
		$this->translations = $this->get('Translations');
		$this->books = $this->get('Books');
		$this->translation = $this->get('Translation');
		if ($this->params->get('activate_search') == 1)
		{
			$this->input = $this->app->input;
		
			// set the search Params
			$this->setSearchParams();
		
			// set the enough verses witch
			$this->enoughVerses = Factory::_('GetBible.Watcher')->enoughVerses($this->translation->abbreviation ?? 'kjv');
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
	 * Set the search params
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	public function setSearchParams()
	{
		$this->words = $this->input->getInt('words', $this->params->get('search_word', 1));
		$this->match = $this->input->getInt('match', $this->params->get('search_match', 1));
		$this->case = $this->input->getInt('case', $this->params->get('search_case', 1));
		$this->target = $this->input->getInt('target', 1000);
		$this->target = ($this->target == 4000) ? $this->input->getInt('book') : $this->target;
		$this->search = $this->input->getString('search') ?? $this->input->getString('s') ?? '';

		// set the value names
		$words = [
			1 => JText::_('COM_GETBIBLE_ALL_WORDS'),
			2 => JText::_('COM_GETBIBLE_ANY_WORDS'),
			3 => JText::_('COM_GETBIBLE_EXACT_WORDS')
		];
		$match = [
			1 => JText::_('COM_GETBIBLE_EXACT_MATCH'),
			2 => JText::_('COM_GETBIBLE_PARTIAL_MATCH')
		];
		$case = [
			1 => JText::_('COM_GETBIBLE_CASE_INSENSITIVE'),
			2 => JText::_('COM_GETBIBLE_CASE_SENSITIVE')
		];
		$target = [
			1000 => JText::_('COM_GETBIBLE_ALL_BOOKS'),
			2000 => JText::_('COM_GETBIBLE_OLD_TESTAMENT'),
			3000 => JText::_('COM_GETBIBLE_NEW_TESTAMENT'),
			4000 => JText::_('COM_GETBIBLE_A_BOOK')
		];

		$this->options_text = [];
		$this->options_text[] = $words[$this->words] ?? JText::_('COM_GETBIBLE_ALL_WORDS');
		$this->options_text[] = $match[$this->match] ?? JText::_('COM_GETBIBLE_EXACT_MATCH');
		$this->options_text[] = $case[$this->case] ?? JText::_('COM_GETBIBLE_CASE_INSENSITIVE');
		$this->options_text[] = $target[$this->target] ?? JText::_('COM_GETBIBLE_A_BOOK');

		// set the current search URL
		$this->url_search = JRoute::_('index.php?option=com_getbible&view=search&t=' . $this->translation->abbreviation . '&words=' . $this->words . '&match=' . $this->match . '&case=' . $this->case . '&target=' . $this->target . '&search=' . $this->search);
		$this->url_base = JUri::base();
		$this->url_bible = JRoute::_('index.php?option=com_getbible&view=app&Itemid=' . $this->params->get('app_menu', 0) . '&t=' . $this->translation->abbreviation);

		// referer the referer page
		//$referer = $this->input->server->get('HTTP_REFERER', null, 'STRING');
		//if (strpos($referer, $this->url_bible) !== null && strpos($referer, $this->url_base) !== null)
		//{
			// $this->url_bible = $referer; // needs more work!
		//}
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
		JHtml::_('stylesheet', "media/com_getbible/datatable/css/datatables.min.css", ['version' => 'auto']);
		JHtml::_('script', "media/com_getbible/datatable/js/pdfmake.min.js", ['version' => 'auto']);
		JHtml::_('script', "media/com_getbible/datatable/js/vfs_fonts.js", ['version' => 'auto']);
		JHtml::_('script', "media/com_getbible/datatable/js/datatables.min.js", ['version' => 'auto']);

		// Add View JavaScript File
		JHtml::_('script', "components/com_getbible/assets/js/search.js", ['version' => 'auto']);

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
		$search_found_color = $this->params->get('search_found_color', '#4747ff');
		$table_selection_color = $this->params->get('table_selection_color', '#dfdfdf');
		// add the document default css file
		JHtml::_('stylesheet', 'components/com_getbible/assets/css/search.css', ['version' => 'auto']);
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
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		
		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('search');
		if (GetbibleHelper::checkString($this->help_url))
		{
			JToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}
		// now initiate the toolbar
		$this->toolbar = JToolbar::getInstance();
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
