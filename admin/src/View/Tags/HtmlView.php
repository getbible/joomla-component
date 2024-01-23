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
namespace TrueChristianChurch\Component\Getbible\Administrator\View\Tags;

// No direct access to this file
\defined('_JEXEC') or die;

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

/**
 * Getbible Html View class for the Tags
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Tags view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = Factory::getApplication()->getIdentity();
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
		$this->canDo = GetbibleHelper::getActions('tag');
		$this->canEdit = $this->canDo->get('tag.edit');
		$this->canState = $this->canDo->get('tag.edit.state');
		$this->canCreate = $this->canDo->get('tag.create');
		$this->canDelete = $this->canDo->get('tag.delete');
		$this->canBatch = ($this->canDo->get('tag.batch') && $this->canDo->get('core.batch'));

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

		// Display the template
		parent::display($tpl);

		// Set the html view document stuff
		$this->setHtmlViewDoc();
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		ToolbarHelper::title(Text::_('COM_GETBIBLE_TAGS'), 'tags');

		if ($this->canCreate)
		{
			ToolbarHelper::addNew('tag.add');
		}

		// Only load if there are items
		if (ArrayHelper::check($this->items))
		{
			if ($this->canEdit)
			{
				ToolbarHelper::editList('tag.edit');
			}

			if ($this->canState)
			{
				ToolbarHelper::publishList('tags.publish');
				ToolbarHelper::unpublishList('tags.unpublish');
				ToolbarHelper::archiveList('tags.archive');

				if ($this->canDo->get('core.admin'))
				{
					ToolbarHelper::checkin('tags.checkin');
				}
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				ToolbarHelper::deleteList('', 'tags.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				ToolbarHelper::trash('tags.trash');
			}
		}

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('tags');
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
	 * Set this html view document related stuff.
	 *
	 * @return void
	 * @since   4.4.0
	 */
	protected function setHtmlViewDoc(): void
	{
		$this->getDocument()->setTitle(Text::_('COM_GETBIBLE_TAGS'));
		Html::_('stylesheet', "administrator/components/com_getbible/assets/css/tags.css", ['version' => 'auto']);
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
		if (!is_string($var))
		{
				return $var;
		}
		elseif(strlen($var) > 50)
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
			'a.name' => Text::_('COM_GETBIBLE_TAG_NAME_LABEL'),
			'g.name' => Text::_('COM_GETBIBLE_TAG_LINKER_LABEL'),
			'a.access' => Text::_('COM_GETBIBLE_TAG_ACCESS_LABEL'),
			'a.id' => Text::_('JGRID_HEADING_ID')
		);
	}
}
