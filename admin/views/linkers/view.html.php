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
 * Getbible Html View class for the Linkers
 */
class GetbibleViewLinkers extends HtmlView
{
	/**
	 * Linkers view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			GetbibleHelper::addSubmenu('linkers');
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
		$this->canDo = GetbibleHelper::getActions('linker');
		$this->canEdit = $this->canDo->get('linker.edit');
		$this->canState = $this->canDo->get('linker.edit.state');
		$this->canCreate = $this->canDo->get('linker.create');
		$this->canDelete = $this->canDo->get('linker.delete');
		$this->canBatch = ($this->canDo->get('linker.batch') && $this->canDo->get('core.batch'));

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
		JToolBarHelper::title(JText::_('COM_GETBIBLE_LINKERS'), 'joomla');
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=linkers');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('linker.add');
		}

		// Only load if there are items
		if (GetbibleHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('linker.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('linkers.publish');
				JToolBarHelper::unpublishList('linkers.unpublish');
				JToolBarHelper::archiveList('linkers.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('linkers.checkin');
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
				JToolbarHelper::deleteList('', 'linkers.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('linkers.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('linkers');
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

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_GETBIBLE_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Name Selection
			$this->nameOptions = JFormHelper::loadFieldType('linkersfiltername')->options;
			// We do some sanitation for Name filter
			if (GetbibleHelper::checkArray($this->nameOptions) &&
				isset($this->nameOptions[0]->value) &&
				!GetbibleHelper::checkString($this->nameOptions[0]->value))
			{
				unset($this->nameOptions[0]);
			}
			// Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_LINKER_NAME_LABEL').' -',
				'batch[name]',
				JHtml::_('select.options', $this->nameOptions, 'value', 'text')
			);
		}

		// Only load Public Tagged Verses batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Public Tagged Verses Selection
			$this->public_tagged_versesOptions = JFormHelper::loadFieldType('linkersfilterpublictaggedverses')->options;
			// We do some sanitation for Public Tagged Verses filter
			if (GetbibleHelper::checkArray($this->public_tagged_versesOptions) &&
				isset($this->public_tagged_versesOptions[0]->value) &&
				!GetbibleHelper::checkString($this->public_tagged_versesOptions[0]->value))
			{
				unset($this->public_tagged_versesOptions[0]);
			}
			// Public Tagged Verses Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_LINKER_PUBLIC_TAGGED_VERSES_LABEL').' -',
				'batch[public_tagged_verses]',
				JHtml::_('select.options', $this->public_tagged_versesOptions, 'value', 'text')
			);
		}

		// Only load Public Notes batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Public Notes Selection
			$this->public_notesOptions = JFormHelper::loadFieldType('linkersfilterpublicnotes')->options;
			// We do some sanitation for Public Notes filter
			if (GetbibleHelper::checkArray($this->public_notesOptions) &&
				isset($this->public_notesOptions[0]->value) &&
				!GetbibleHelper::checkString($this->public_notesOptions[0]->value))
			{
				unset($this->public_notesOptions[0]);
			}
			// Public Notes Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_LINKER_PUBLIC_NOTES_LABEL').' -',
				'batch[public_notes]',
				JHtml::_('select.options', $this->public_notesOptions, 'value', 'text')
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
		$this->document->setTitle(JText::_('COM_GETBIBLE_LINKERS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_getbible/assets/css/linkers.css", (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			'a.name' => JText::_('COM_GETBIBLE_LINKER_NAME_LABEL'),
			'a.public_tagged_verses' => JText::_('COM_GETBIBLE_LINKER_PUBLIC_TAGGED_VERSES_LABEL'),
			'a.public_notes' => JText::_('COM_GETBIBLE_LINKER_PUBLIC_NOTES_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
