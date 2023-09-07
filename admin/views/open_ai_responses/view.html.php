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
 * Getbible Html View class for the Open_ai_responses
 */
class GetbibleViewOpen_ai_responses extends HtmlView
{
	/**
	 * Open_ai_responses view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			GetbibleHelper::addSubmenu('open_ai_responses');
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
		$this->listDirn = $this->escape($this->state->get('list.direction', 'desc'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = GetbibleHelper::getActions('open_ai_response');
		$this->canEdit = $this->canDo->get('open_ai_response.edit');
		$this->canState = $this->canDo->get('open_ai_response.edit.state');
		$this->canCreate = $this->canDo->get('open_ai_response.create');
		$this->canDelete = $this->canDo->get('open_ai_response.delete');
		$this->canBatch = ($this->canDo->get('open_ai_response.batch') && $this->canDo->get('core.batch'));

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
		JToolBarHelper::title(JText::_('COM_GETBIBLE_OPEN_AI_RESPONSES'), 'reply');
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=open_ai_responses');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('open_ai_response.add');
		}

		// Only load if there are items
		if (GetbibleHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('open_ai_response.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('open_ai_responses.publish');
				JToolBarHelper::unpublishList('open_ai_responses.unpublish');
				JToolBarHelper::archiveList('open_ai_responses.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('open_ai_responses.checkin');
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
				JToolbarHelper::deleteList('', 'open_ai_responses.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('open_ai_responses.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('open_ai_responses');
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

		// Only load Response Id batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Response Id Selection
			$this->response_idOptions = JFormHelper::loadFieldType('openairesponsesfilterresponseid')->options;
			// We do some sanitation for Response Id filter
			if (GetbibleHelper::checkArray($this->response_idOptions) &&
				isset($this->response_idOptions[0]->value) &&
				!GetbibleHelper::checkString($this->response_idOptions[0]->value))
			{
				unset($this->response_idOptions[0]);
			}
			// Response Id Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_ID_LABEL').' -',
				'batch[response_id]',
				JHtml::_('select.options', $this->response_idOptions, 'value', 'text')
			);
		}

		// Only load Prompt Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Prompt Name Selection
			$this->promptNameOptions = JFormHelper::loadFieldType('Prompts')->options;
			// We do some sanitation for Prompt Name filter
			if (GetbibleHelper::checkArray($this->promptNameOptions) &&
				isset($this->promptNameOptions[0]->value) &&
				!GetbibleHelper::checkString($this->promptNameOptions[0]->value))
			{
				unset($this->promptNameOptions[0]);
			}
			// Prompt Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_PROMPT_LABEL').' -',
				'batch[prompt]',
				JHtml::_('select.options', $this->promptNameOptions, 'value', 'text')
			);
		}

		// Only load Response Model batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Response Model Selection
			$this->response_modelOptions = JFormHelper::loadFieldType('openairesponsesfilterresponsemodel')->options;
			// We do some sanitation for Response Model filter
			if (GetbibleHelper::checkArray($this->response_modelOptions) &&
				isset($this->response_modelOptions[0]->value) &&
				!GetbibleHelper::checkString($this->response_modelOptions[0]->value))
			{
				unset($this->response_modelOptions[0]);
			}
			// Response Model Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_MODEL_LABEL').' -',
				'batch[response_model]',
				JHtml::_('select.options', $this->response_modelOptions, 'value', 'text')
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
		$this->document->setTitle(JText::_('COM_GETBIBLE_OPEN_AI_RESPONSES'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_getbible/assets/css/open_ai_responses.css", (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
			'a.response_id' => JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_ID_LABEL'),
			'g.name' => JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_PROMPT_LABEL'),
			'a.response_object' => JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_OBJECT_LABEL'),
			'a.response_model' => JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_MODEL_LABEL'),
			'a.total_tokens' => JText::_('COM_GETBIBLE_OPEN_AI_RESPONSE_TOTAL_TOKENS_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
