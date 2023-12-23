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
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use VDM\Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

/**
 * Getbible Html View class for the Tagged_verses
 */
class GetbibleViewTagged_verses extends HtmlView
{
	/**
	 * Tagged_verses view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			GetbibleHelper::addSubmenu('tagged_verses');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = Factory::getUser();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) Uri::getInstance()));
		// get global action permissions
		$this->canDo = GetbibleHelper::getActions('tagged_verse');
		$this->canEdit = $this->canDo->get('tagged_verse.edit');
		$this->canState = $this->canDo->get('tagged_verse.edit.state');
		$this->canCreate = $this->canDo->get('tagged_verse.create');
		$this->canDelete = $this->canDo->get('tagged_verse.delete');
		$this->canBatch = ($this->canDo->get('tagged_verse.batch') && $this->canDo->get('core.batch'));

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=tagged_verses');
		ToolbarHelper::title(Text::_('COM_GETBIBLE_TAGGED_VERSES'), 'tags-2');
		FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('tagged_verse.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('tagged_verse.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('tagged_verses.publish');
				ToolbarHelper::unpublishList('tagged_verses.unpublish');
				ToolbarHelper::archiveList('tagged_verses.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('tagged_verses.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = Toolbar::getInstance('toolbar');
				// set the batch button name
				$title = Text::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new FileLayout('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				ToolbarHelper::deleteList('', 'tagged_verses.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('tagged_verses.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('tagged_verses');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_getbible');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				Text::_('COM_GETBIBLE_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				Html::_('select.options', Html::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load Book Nr batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Book Nr Selection
			$this->book_nrOptions = FormHelper::loadFieldType('taggedversesfilterbooknr')->options;
			// We do some sanitation for Book Nr filter
			if (ArrayHelper::check($this->book_nrOptions) &&
				isset($this->book_nrOptions[0]->value) &&
				!StringHelper::check($this->book_nrOptions[0]->value))
			{
				unset($this->book_nrOptions[0]);
			}
			// Book Nr Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_BOOK_NR_LABEL').' -',
				'batch[book_nr]',
				Html::_('select.options', $this->book_nrOptions, 'value', 'text')
			);
		}

		// Only load Abbreviation Translation batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Abbreviation Translation Selection
			$this->abbreviationTranslationOptions = FormHelper::loadFieldType('Translations')->options;
			// We do some sanitation for Abbreviation Translation filter
			if (ArrayHelper::check($this->abbreviationTranslationOptions) &&
				isset($this->abbreviationTranslationOptions[0]->value) &&
				!StringHelper::check($this->abbreviationTranslationOptions[0]->value))
			{
				unset($this->abbreviationTranslationOptions[0]);
			}
			// Abbreviation Translation Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_ABBREVIATION_LABEL').' -',
				'batch[abbreviation]',
				Html::_('select.options', $this->abbreviationTranslationOptions, 'value', 'text')
			);
		}

		// Only load Access batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Access Selection
			$this->accessOptions = FormHelper::loadFieldType('taggedversesfilteraccess')->options;
			// We do some sanitation for Access filter
			if (ArrayHelper::check($this->accessOptions) &&
				isset($this->accessOptions[0]->value) &&
				!StringHelper::check($this->accessOptions[0]->value))
			{
				unset($this->accessOptions[0]);
			}
			// Access Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_ACCESS_LABEL').' -',
				'batch[access]',
				Html::_('select.options', $this->accessOptions, 'value', 'text')
			);
		}

		// Only load Linker Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Linker Name Selection
			$this->linkerNameOptions = FormHelper::loadFieldType('Linkers')->options;
			// We do some sanitation for Linker Name filter
			if (ArrayHelper::check($this->linkerNameOptions) &&
				isset($this->linkerNameOptions[0]->value) &&
				!StringHelper::check($this->linkerNameOptions[0]->value))
			{
				unset($this->linkerNameOptions[0]);
			}
			// Linker Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_LINKER_LABEL').' -',
				'batch[linker]',
				Html::_('select.options', $this->linkerNameOptions, 'value', 'text')
			);
		}

		// Only load Tag Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Tag Name Selection
			$this->tagNameOptions = FormHelper::loadFieldType('Tagers')->options;
			// We do some sanitation for Tag Name filter
			if (ArrayHelper::check($this->tagNameOptions) &&
				isset($this->tagNameOptions[0]->value) &&
				!StringHelper::check($this->tagNameOptions[0]->value))
			{
				unset($this->tagNameOptions[0]);
			}
			// Tag Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_TAG_LABEL').' -',
				'batch[tag]',
				Html::_('select.options', $this->tagNameOptions, 'value', 'text')
			);
		}

		// Only load Verse batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Verse Selection
			$this->verseOptions = FormHelper::loadFieldType('taggedversesfilterverse')->options;
			// We do some sanitation for Verse filter
			if (ArrayHelper::check($this->verseOptions) &&
				isset($this->verseOptions[0]->value) &&
				!StringHelper::check($this->verseOptions[0]->value))
			{
				unset($this->verseOptions[0]);
			}
			// Verse Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_VERSE_LABEL').' -',
				'batch[verse]',
				Html::_('select.options', $this->verseOptions, 'value', 'text')
			);
		}

		// Only load Chapter batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Chapter Selection
			$this->chapterOptions = FormHelper::loadFieldType('taggedversesfilterchapter')->options;
			// We do some sanitation for Chapter filter
			if (ArrayHelper::check($this->chapterOptions) &&
				isset($this->chapterOptions[0]->value) &&
				!StringHelper::check($this->chapterOptions[0]->value))
			{
				unset($this->chapterOptions[0]);
			}
			// Chapter Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_TAGGED_VERSE_CHAPTER_LABEL').' -',
				'batch[chapter]',
				Html::_('select.options', $this->chapterOptions, 'value', 'text')
			);
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = Factory::getDocument();
		}
		$this->document->setTitle(Text::_('COM_GETBIBLE_TAGGED_VERSES'));
		Html::_('stylesheet', "administrator/components/com_getbible/assets/css/tagged_verses.css", ['version' => 'auto']);
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return StringHelper::html($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return StringHelper::html($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array   Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => Text::_('JGRID_HEADING_ORDERING'),
			'a.published' => Text::_('JSTATUS'),
			'a.book_nr' => Text::_('COM_GETBIBLE_TAGGED_VERSE_BOOK_NR_LABEL'),
			'g.translation' => Text::_('COM_GETBIBLE_TAGGED_VERSE_ABBREVIATION_LABEL'),
			'a.access' => Text::_('COM_GETBIBLE_TAGGED_VERSE_ACCESS_LABEL'),
			'h.name' => Text::_('COM_GETBIBLE_TAGGED_VERSE_LINKER_LABEL'),
			'i.name' => Text::_('COM_GETBIBLE_TAGGED_VERSE_TAG_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
