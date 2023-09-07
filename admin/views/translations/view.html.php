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
 * Getbible Html View class for the Translations
 */
class GetbibleViewTranslations extends HtmlView
{
	/**
	 * Translations view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			GetbibleHelper::addSubmenu('translations');
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
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.language'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'asc'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = GetbibleHelper::getActions('translation');
		$this->canEdit = $this->canDo->get('translation.edit');
		$this->canState = $this->canDo->get('translation.edit.state');
		$this->canCreate = $this->canDo->get('translation.create');
		$this->canDelete = $this->canDo->get('translation.delete');
		$this->canBatch = ($this->canDo->get('translation.batch') && $this->canDo->get('core.batch'));

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
		JToolBarHelper::title(JText::_('COM_GETBIBLE_TRANSLATIONS'), 'book');
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=translations');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('translation.add');
		}

		// Only load if there are items
		if (GetbibleHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('translation.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('translations.publish');
				JToolBarHelper::unpublishList('translations.unpublish');
				JToolBarHelper::archiveList('translations.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('translations.checkin');
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
			if ($this->user->authorise('translation.update_book_names', 'com_getbible'))
			{
				// add Update Book Names button.
				JToolBarHelper::custom('translations.updateBookNames', 'bookmark custom-button-updatebooknames', '', 'COM_GETBIBLE_UPDATE_BOOK_NAMES', 'true');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'translations.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('translations.trash');
			}
		}
		if ($this->user->authorise('translation.update_translations_details', 'com_getbible'))
		{
			// add Update Translations Details button.
			JToolBarHelper::custom('translations.updateTranslationsDetails', 'book custom-button-updatetranslationsdetails', '', 'COM_GETBIBLE_UPDATE_TRANSLATIONS_DETAILS', false);
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('translations');
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

		// Only load Direction batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Direction Selection
			$this->directionOptions = JFormHelper::loadFieldType('translationsfilterdirection')->options;
			// We do some sanitation for Direction filter
			if (GetbibleHelper::checkArray($this->directionOptions) &&
				isset($this->directionOptions[0]->value) &&
				!GetbibleHelper::checkString($this->directionOptions[0]->value))
			{
				unset($this->directionOptions[0]);
			}
			// Direction Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_TRANSLATION_DIRECTION_LABEL').' -',
				'batch[direction]',
				JHtml::_('select.options', $this->directionOptions, 'value', 'text')
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
		$this->document->setTitle(JText::_('COM_GETBIBLE_TRANSLATIONS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_getbible/assets/css/translations.css", (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			'a.translation' => JText::_('COM_GETBIBLE_TRANSLATION_TRANSLATION_LABEL'),
			'a.abbreviation' => JText::_('COM_GETBIBLE_TRANSLATION_ABBREVIATION_LABEL'),
			'a.language' => JText::_('COM_GETBIBLE_TRANSLATION_LANGUAGE_LABEL'),
			'a.direction' => JText::_('COM_GETBIBLE_TRANSLATION_DIRECTION_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
