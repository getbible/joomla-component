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
 * Getbible Html View class for the Open_ai_messages
 */
class GetbibleViewOpen_ai_messages extends HtmlView
{
	/**
	 * Open_ai_messages view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			GetbibleHelper::addSubmenu('open_ai_messages');
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
		$this->listDirn = $this->escape($this->state->get('list.direction', 'desc'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) Uri::getInstance()));
		// get global action permissions
		$this->canDo = GetbibleHelper::getActions('open_ai_message');
		$this->canEdit = $this->canDo->get('open_ai_message.edit');
		$this->canState = $this->canDo->get('open_ai_message.edit.state');
		$this->canCreate = $this->canDo->get('open_ai_message.create');
		$this->canDelete = $this->canDo->get('open_ai_message.delete');
		$this->canBatch = ($this->canDo->get('open_ai_message.batch') && $this->canDo->get('core.batch'));

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
		JHtmlSidebar::setAction('index.php?option=com_getbible&view=open_ai_messages');
		ToolbarHelper::title(Text::_('COM_GETBIBLE_OPEN_AI_MESSAGES'), 'comment');
		FormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('open_ai_message.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('open_ai_message.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('open_ai_messages.publish');
				ToolbarHelper::unpublishList('open_ai_messages.unpublish');
				ToolbarHelper::archiveList('open_ai_messages.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('open_ai_messages.checkin');
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
				ToolbarHelper::deleteList('', 'open_ai_messages.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('open_ai_messages.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('open_ai_messages');
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

		// Only load access batch if create, edit and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				Text::_('COM_GETBIBLE_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				Html::_('select.options', Html::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Only load Role batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Role Selection
			$this->roleOptions = FormHelper::loadFieldType('openaimessagesfilterrole')->options;
			// We do some sanitation for Role filter
			if (ArrayHelper::check($this->roleOptions) &&
				isset($this->roleOptions[0]->value) &&
				!StringHelper::check($this->roleOptions[0]->value))
			{
				unset($this->roleOptions[0]);
			}
			// Role Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_ROLE_LABEL').' -',
				'batch[role]',
				Html::_('select.options', $this->roleOptions, 'value', 'text')
			);
		}

		// Only load Open Ai Response Response Id batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Open Ai Response Response Id Selection
			$this->open_ai_responseResponse_idOptions = FormHelper::loadFieldType('Openairesponses')->options;
			// We do some sanitation for Open Ai Response Response Id filter
			if (ArrayHelper::check($this->open_ai_responseResponse_idOptions) &&
				isset($this->open_ai_responseResponse_idOptions[0]->value) &&
				!StringHelper::check($this->open_ai_responseResponse_idOptions[0]->value))
			{
				unset($this->open_ai_responseResponse_idOptions[0]);
			}
			// Open Ai Response Response Id Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_OPEN_AI_RESPONSE_LABEL').' -',
				'batch[open_ai_response]',
				Html::_('select.options', $this->open_ai_responseResponse_idOptions, 'value', 'text')
			);
		}

		// Only load Prompt Name batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Prompt Name Selection
			$this->promptNameOptions = FormHelper::loadFieldType('Prompts')->options;
			// We do some sanitation for Prompt Name filter
			if (ArrayHelper::check($this->promptNameOptions) &&
				isset($this->promptNameOptions[0]->value) &&
				!StringHelper::check($this->promptNameOptions[0]->value))
			{
				unset($this->promptNameOptions[0]);
			}
			// Prompt Name Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_PROMPT_LABEL').' -',
				'batch[prompt]',
				Html::_('select.options', $this->promptNameOptions, 'value', 'text')
			);
		}

		// Only load Source batch if create, edit, and batch is allowed
		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Set Source Selection
			$this->sourceOptions = FormHelper::loadFieldType('openaimessagesfiltersource')->options;
			// We do some sanitation for Source filter
			if (ArrayHelper::check($this->sourceOptions) &&
				isset($this->sourceOptions[0]->value) &&
				!StringHelper::check($this->sourceOptions[0]->value))
			{
				unset($this->sourceOptions[0]);
			}
			// Source Batch Selection
			JHtmlBatch_::addListSelection(
				'- Keep Original '.Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_SOURCE_LABEL').' -',
				'batch[source]',
				Html::_('select.options', $this->sourceOptions, 'value', 'text')
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
		$this->document->setTitle(Text::_('COM_GETBIBLE_OPEN_AI_MESSAGES'));
		Html::_('stylesheet', "administrator/components/com_getbible/assets/css/open_ai_messages.css", ['version' => 'auto']);
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
			'a.role' => Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_ROLE_LABEL'),
			'g.response_id' => Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_OPEN_AI_RESPONSE_LABEL'),
			'h.name' => Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_PROMPT_LABEL'),
			'a.source' => Text::_('COM_GETBIBLE_OPEN_AI_MESSAGE_SOURCE_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}

	/**
	 * Get the Document (helper method toward Joomla 4 and 5)
	 */
	public function getDocument()
	{
		$this->document ??= JFactory::getDocument();

		return $this->document;
	}
}
