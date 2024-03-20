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
namespace TrueChristianChurch\Component\Getbible\Administrator\View\Open_ai_responses;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Document\Document;
use TrueChristianChurch\Component\Getbible\Administrator\Helper\GetbibleHelper;
use VDM\Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\StringHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible Html View class for the Open_ai_responses
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Open_ai_responses view display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 * @since  1.6
	 */
	public function display($tpl = null)
	{
		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->styles = $this->get('Styles');
		$this->scripts = $this->get('Scripts');
		$this->user ??= Factory::getApplication()->getIdentity();
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
		$this->canDo = GetbibleHelper::getActions('open_ai_response');
		$this->canEdit = $this->canDo->get('open_ai_response.edit');
		$this->canState = $this->canDo->get('open_ai_response.edit.state');
		$this->canCreate = $this->canDo->get('open_ai_response.create');
		$this->canDelete = $this->canDo->get('open_ai_response.delete');
		$this->canBatch = ($this->canDo->get('open_ai_response.batch') && $this->canDo->get('core.batch'));

		// If we don't have items we load the empty state
		if (is_array($this->items) && !count((array) $this->items) && $this->isEmptyState = $this->get('IsEmptyState'))
		{
			$this->setLayout('emptystate');
		}

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors), 500);
		}

		// Set the html view document stuff
		$this->_prepareDocument();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_GETBIBLE_OPEN_AI_RESPONSES'), 'reply');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('open_ai_response.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('open_ai_response.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('open_ai_responses.publish');
				ToolbarHelper::unpublishList('open_ai_responses.unpublish');
				ToolbarHelper::archiveList('open_ai_responses.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('open_ai_responses.checkin');
				}
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				ToolbarHelper::deleteList('', 'open_ai_responses.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('open_ai_responses.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('open_ai_responses');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_getbible');
		}
	}

	/**
	 * Prepare some document related stuff.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function _prepareDocument(): void
	{
		// Load jQuery
		Html::_('jquery.framework');
		$this->getDocument()->setTitle(Text::_('COM_GETBIBLE_OPEN_AI_RESPONSES'));
		// add styles
		foreach ($this->styles as $style)
		{
			Html::_('stylesheet', $style, ['version' => 'auto']);
		}
		// add scripts
		foreach ($this->scripts as $script)
		{
			Html::_('script', $script, ['version' => 'auto']);
		}
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
	public function escape($var, bool $shorten = true, int $length = 50)
	{
		if (!is_string($var))
		{
			return $var;
		}

		return StringHelper::html($var, $this->_charset ?? 'UTF-8', $shorten, $length);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array   containing the field name to sort by as the key and display text as value
	 * @since   1.6
	 */
	protected function getSortFields()
	{
		return array(
			'a.ordering' => Text::_('JGRID_HEADING_ORDERING'),
			'a.published' => Text::_('JSTATUS'),
			'a.response_id' => Text::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_ID_LABEL'),
			'g.name' => Text::_('COM_GETBIBLE_OPEN_AI_RESPONSE_PROMPT_LABEL'),
			'a.response_object' => Text::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_OBJECT_LABEL'),
			'a.response_model' => Text::_('COM_GETBIBLE_OPEN_AI_RESPONSE_RESPONSE_MODEL_LABEL'),
			'a.total_tokens' => Text::_('COM_GETBIBLE_OPEN_AI_RESPONSE_TOTAL_TOKENS_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
