<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    2015-12-03 01:42:15
    @author     Llewellyn van der Merwe <https://getbible.life>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/
namespace TrueChristianBible\Component\GetBible\Administrator\View\Open_ai_responses;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Document\Document;
use TrueChristianBible\Component\GetBible\Administrator\Helper\GetbibleHelper;
use TrueChristianBible\Joomla\GetBible\Utilities\Permitted\Actions;
use TrueChristianBible\Joomla\Utilities\StringHelper;
use Joomla\CMS\Toolbar\Button\DropdownButton;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible Html View class for the Open_ai_responses
 *
 * @since  1.6
 */
#[\AllowDynamicProperties]
class HtmlView extends BaseHtmlView
{
	/**
	 * The items from the model
	 *
	 * @var    mixed
	 * @since  3.10.11
	 */
	public mixed $items;

	/**
	 * The state object
	 *
	 * @var    mixed
	 * @since  3.10.11
	 */
	public mixed $state;

	/**
	 * The styles url array
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	protected array $styles;

	/**
	 * The scripts url array
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	protected array $scripts;

	/**
	 * The actions object
	 *
	 * @var    object
	 * @since  3.10.11
	 */
	public object $canDo;

	/**
	 * The return here base64 url
	 *
	 * @var    string
	 * @since  3.10.11
	 */
	public string $return_here;

	/**
	 * The title key used in modal
	 *
	 * @var    string
	 * @since  5.2.1
	 */
	public string $modalTitleKey;

	/**
	 * The modal state
	 *
	 * @var    bool
	 * @since  5.2.1
	 */
	public bool $isModal;

	/**
	 * The empty state
	 *
	 * @var    bool
	 * @since  5.2.1
	 */
	protected bool $isEmptyState;

	/**
	 * The user object.
	 *
	 * @var    User
	 * @since  3.10.11
	 */
	public User $user;

	/**
	 * The Can Edit permission
	 *
	 * @var    ?bool
	 * @since  5.2.1
	 */
	public ?bool $canEdit = null;

	/**
	 * The Can Edit State permission
	 *
	 * @var    ?bool
	 * @since  5.2.1
	 */
	public ?bool $canState = null;

	/**
	 * The Can Create permission
	 *
	 * @var    ?bool
	 * @since  5.2.1
	 */
	public ?bool $canCreate = null;

	/**
	 * The Can Delete permission
	 *
	 * @var    ?bool
	 * @since  5.2.1
	 */
	public ?bool $canDelete = null;

	/**
	 * The Can Batch permission
	 *
	 * @var    ?bool
	 * @since  5.2.1
	 */
	public ?bool $canBatch = null;

	/**
	 * Open_ai_responses view display method
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 * @throws \Exception
	 * @since  1.6
	 */
	public function display($tpl = null): void
	{
		// Load module values
		$model = $this->getModel();
		$this->items = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->state = $model->getState();
		$this->isEmptyState = $model->getIsEmptyState();
		$this->styles = $model->getStyles();
		$this->scripts = $model->getScripts();
		$this->user ??= $this->getCurrentUser();
		// Load the filter form from xml for searchtools.
		$this->filterForm = $model->getFilterForm();
		// Load the active filters for searchtools.
		$this->activeFilters = $model->getActiveFilters();
		// Add the list ordering clause.
		$this->listOrder = $this->escape($this->state->get('list.ordering', 'a.id'));
		$this->listDirn = $this->escape($this->state->get('list.direction', 'desc'));
		$this->saveOrder = $this->listOrder == 'a.ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) Uri::getInstance()));
		// get the permitted actions the current user can do
		$this->canDo = Actions::get('open_ai_response');
		$this->canEdit = $this->canDo->get('open_ai_response.edit');
		$this->canState = $this->canDo->get('open_ai_response.edit.state');
		$this->canCreate = $this->canDo->get('open_ai_response.create');
		$this->canDelete = $this->canDo->get('open_ai_response.delete');
		$this->canBatch = ($this->canDo->get('open_ai_response.batch') && $this->canDo->get('core.batch'));

		// If we don't have items we load the empty state
		if (is_array($this->items) && !count((array) $this->items) && $this->isEmptyState)
		{
			$this->setLayout('emptystate');
		}

		// We don't need toolbar in the modal window.
		$this->isModal = true;
		if ($this->getLayout() !== 'modal')
		{
			$this->isModal = false;
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
	 * @throws  \Exception
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{
		ToolbarHelper::title(Text::_('COM_GETBIBLE_OPEN_AI_RESPONSES'), 'reply');
		/** @var  Toolbar $toolbar */
		$toolbar = $this->getDocument()->getToolbar();
		if ($this->canCreate)
		{
			$toolbar->addNew('open_ai_response.add');
		}

		// Only load if there are items
		if (!$this->isEmptyState)
		{
			/** @var  DropdownButton $dropdown */
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('icon-ellipsis-h')
				->buttonClass('btn btn-action')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();

			if ($this->canEdit)
			{
				$childBar->edit('open_ai_response.edit')->listCheck(true);
			}

			if ($this->canState)
			{
				$childBar->publish('open_ai_responses.publish')->listCheck(true);
				$childBar->unpublish('open_ai_responses.unpublish')->listCheck(true);
				$childBar->archive('open_ai_responses.archive')->listCheck(true);

				if ($this->canDo->get('core.admin'))
				{
					$childBar->checkin('open_ai_responses.checkin')->listCheck(true);
				}

				if ($this->state->get('filter.published') == -2 && $this->canDelete)
				{
					$toolbar->delete('open_ai_responses.delete', 'JTOOLBAR_DELETE_FROM_TRASH')
						->message('JGLOBAL_CONFIRM_DELETE')
						->listCheck(true);
				}
				elseif ($this->canDelete)
				{
					$childBar->trash('open_ai_responses.trash')->listCheck(true);
				}
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('open_ai_responses');
		if (StringHelper::check($this->help_url))
		{
			$toolbar->help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			$toolbar->preferences('com_getbible');
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
	 * Get the modal data/title key
	 *
	 * @return  string  The key value.
	 * @since   5.2.1
	 */
	public function getModalTitleKey(): string
	{
		return $this->modalTitleKey ?? 'id';
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
