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



/**
 * Getbible View class
 */
class GetbibleViewGetbible extends JViewLegacy
{
	/**
	 * View display method
	 * @return void
	 */
	function display($tpl = null)
	{
		// Assign data to the view
		$this->icons			= $this->get('Icons');
		$this->contributors		= GetbibleHelper::getContributors();
		$this->wiki = $this->get('Wiki');
		$this->noticeboard = $this->get('Noticeboard');
		$this->readme = $this->get('Readme');
		$this->version = $this->get('Version');
		
		// get the manifest details of the component
		$this->manifest = GetbibleHelper::manifest();
		
		// Set the toolbar
		$this->addToolBar();
		
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
		$canDo = GetbibleHelper::getActions('getbible');
		JToolBarHelper::title(JText::_('COM_GETBIBLE_DASHBOARD'), 'grid-2');

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('getbible');
		if (GetbibleHelper::checkString($this->help_url))
		{
			JToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_getbible');
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();
		
		// add dashboard style sheets
		$document->addStyleSheet(JURI::root() . "administrator/components/com_getbible/assets/css/dashboard.css");
		
		// set page title
		$document->setTitle(JText::_('COM_GETBIBLE_DASHBOARD'));
		
		// add manifest to page JavaScript
		$document->addScriptDeclaration("var manifest = jQuery.parseJSON('" . json_encode($this->manifest) . "');", "text/javascript");
	}
}
