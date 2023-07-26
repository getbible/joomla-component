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

/**
 * Getbible Html View class for the Notes
 */
class GetbibleViewNotes extends HtmlView
{
	/**
	 * Notes view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			GetbibleHelper::addSubmenu('notes');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		// Load the filter form from xml.
		$this->filterForm = $this->get('FilterForm');
		// Load the active filters.
		$this->activeFilters = $this->get('ActiveFilters');
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'DESC'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = GetbibleHelper::getActions('note');
		$this->canEdit = $this->canDo->get('note.edit');
		$this->canState = $this->canDo->get('note.edit.state');
		$this->canCreate = $this->canDo->get('note.create');
		$this->canDelete = $this->canDo->get('note.delete');
		$this->canBatch = $this->canDo->get('core.batch');

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
		JToolBarHelper::title(JText::_('COM_GETBIBLE_NOTES'), 'file');
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=notes');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('note.add');
		}

		// Only load if there are items
		if (GetbibleHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('note.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('notes.publish');
				JToolBarHelper::unpublishList('notes.unpublish');
				JToolBarHelper::archiveList('notes.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('notes.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'notes.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('notes.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('notes');
		if (GetbibleHelper::checkString($this->help_url))
		{
				JToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_getbible');
		}

		// Only load published batch if state and batch is allowed
		if ($this->canState && $this->canBatch)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_GETBIBLE_KEEP_ORIGINAL_STATE'),
				'batch[published]',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
			);
		}

		// Only load Book Nr batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Book Nr Selection
			$this->book_nrOptions = JFormHelper::loadFieldType('notesfilterbooknr')->options;
			// We do some sanitation for Book Nr filter
			if (GetbibleHelper::checkArray($this->book_nrOptions) &&
				isset($this->book_nrOptions[0]->value) &&
				!GetbibleHelper::checkString($this->book_nrOptions[0]->value))
			{
				unset($this->book_nrOptions[0]);
			}
			// Book Nr Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_NOTE_BOOK_NR_LABEL').' -',
				'batch[book_nr]',
				JHtml::_('select.options', $this->book_nrOptions, 'value', 'text')
			);
		}

		// Only load Linker Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Linker Name Selection
			$this->linkerNameOptions = JFormHelper::loadFieldType('Linkers')->options;
			// We do some sanitation for Linker Name filter
			if (GetbibleHelper::checkArray($this->linkerNameOptions) &&
				isset($this->linkerNameOptions[0]->value) &&
				!GetbibleHelper::checkString($this->linkerNameOptions[0]->value))
			{
				unset($this->linkerNameOptions[0]);
			}
			// Linker Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_NOTE_LINKER_LABEL').' -',
				'batch[linker]',
				JHtml::_('select.options', $this->linkerNameOptions, 'value', 'text')
			);
		}

		// Only load Access batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Access Selection
			$this->accessOptions = JFormHelper::loadFieldType('notesfilteraccess')->options;
			// We do some sanitation for Access filter
			if (GetbibleHelper::checkArray($this->accessOptions) &&
				isset($this->accessOptions[0]->value) &&
				!GetbibleHelper::checkString($this->accessOptions[0]->value))
			{
				unset($this->accessOptions[0]);
			}
			// Access Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_NOTE_ACCESS_LABEL').' -',
				'batch[access]',
				JHtml::_('select.options', $this->accessOptions, 'value', 'text')
			);
		}

		// Only load Verse batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Verse Selection
			$this->verseOptions = JFormHelper::loadFieldType('notesfilterverse')->options;
			// We do some sanitation for Verse filter
			if (GetbibleHelper::checkArray($this->verseOptions) &&
				isset($this->verseOptions[0]->value) &&
				!GetbibleHelper::checkString($this->verseOptions[0]->value))
			{
				unset($this->verseOptions[0]);
			}
			// Verse Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_NOTE_VERSE_LABEL').' -',
				'batch[verse]',
				JHtml::_('select.options', $this->verseOptions, 'value', 'text')
			);
		}

		// Only load Chapter batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Chapter Selection
			$this->chapterOptions = JFormHelper::loadFieldType('notesfilterchapter')->options;
			// We do some sanitation for Chapter filter
			if (GetbibleHelper::checkArray($this->chapterOptions) &&
				isset($this->chapterOptions[0]->value) &&
				!GetbibleHelper::checkString($this->chapterOptions[0]->value))
			{
				unset($this->chapterOptions[0]);
			}
			// Chapter Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_NOTE_CHAPTER_LABEL').' -',
				'batch[chapter]',
				JHtml::_('select.options', $this->chapterOptions, 'value', 'text')
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
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_GETBIBLE_NOTES'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_getbible/assets/css/notes.css", (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			return GetbibleHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return GetbibleHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.book_nr' => JText::_('COM_GETBIBLE_NOTE_BOOK_NR_LABEL'),
			'g.name' => JText::_('COM_GETBIBLE_NOTE_LINKER_LABEL'),
			'a.access' => JText::_('COM_GETBIBLE_NOTE_ACCESS_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
