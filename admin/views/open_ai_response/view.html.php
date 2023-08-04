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
 * Open_ai_response Html View class
 */
class GetbibleViewOpen_ai_response extends HtmlView
{
	/**
	 * display method of View
	 * @return void
	 */
	public function display($tpl = null)
	{
		// set params
		$this->params = JComponentHelper::getParams('com_getbible');
		// Assign the variables
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');
		$this->state = $this->get('State');
		// get action permissions
		$this->canDo = GetbibleHelper::getActions('open_ai_response', $this->item);
		// get input
		$jinput = JFactory::getApplication()->input;
		$this->ref = $jinput->get('ref', 0, 'word');
		$this->refid = $jinput->get('refid', 0, 'int');
		$return = $jinput->get('return', null, 'base64');
		// set the referral string
		$this->referral = '';
		if ($this->refid && $this->ref)
		{
			// return to the item that referred to this item
			$this->referral = '&ref=' . (string)$this->ref . '&refid=' . (int)$this->refid;
		}
		elseif($this->ref)
		{
			// return to the list view that referred to this item
			$this->referral = '&ref=' . (string)$this->ref;
		}
		// check return value
		if (!is_null($return))
		{
			// add the return value
			$this->referral .= '&return=' . (string)$return;
		}

		// Get Linked view data
		$this->vvymessage = $this->get('Vvymessage');

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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId	= $user->id;
		$isNew = $this->item->id == 0;

		JToolbarHelper::title( JText::_($isNew ? 'COM_GETBIBLE_OPEN_AI_RESPONSE_NEW' : 'COM_GETBIBLE_OPEN_AI_RESPONSE_EDIT'), 'pencil-2 article-add');
		// Built the actions for new and existing records.
		if (GetbibleHelper::checkString($this->referral))
		{
			if ($this->canDo->get('open_ai_response.create') && $isNew)
			{
				// We can create the record.
				JToolBarHelper::save('open_ai_response.save', 'JTOOLBAR_SAVE');
			}
			elseif ($this->canDo->get('open_ai_response.edit'))
			{
				// We can save the record.
				JToolBarHelper::save('open_ai_response.save', 'JTOOLBAR_SAVE');
			}
			if ($isNew)
			{
				// Do not creat but cancel.
				JToolBarHelper::cancel('open_ai_response.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				// We can close it.
				JToolBarHelper::cancel('open_ai_response.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		else
		{
			if ($isNew)
			{
				// For new records, check the create permission.
				if ($this->canDo->get('open_ai_response.create'))
				{
					JToolBarHelper::apply('open_ai_response.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('open_ai_response.save', 'JTOOLBAR_SAVE');
					JToolBarHelper::custom('open_ai_response.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				};
				JToolBarHelper::cancel('open_ai_response.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				if ($this->canDo->get('open_ai_response.edit'))
				{
					// We can save the new record
					JToolBarHelper::apply('open_ai_response.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('open_ai_response.save', 'JTOOLBAR_SAVE');
					// We can save this record, but check the create permission to see
					// if we can return to make a new one.
					if ($this->canDo->get('open_ai_response.create'))
					{
						JToolBarHelper::custom('open_ai_response.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
				$canVersion = ($this->canDo->get('core.version') && $this->canDo->get('open_ai_response.version'));
				if ($this->state->params->get('save_history', 1) && $this->canDo->get('open_ai_response.edit') && $canVersion)
				{
					JToolbarHelper::versions('com_getbible.open_ai_response', $this->item->id);
				}
				if ($this->canDo->get('open_ai_response.create'))
				{
					JToolBarHelper::custom('open_ai_response.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
				JToolBarHelper::cancel('open_ai_response.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		JToolbarHelper::divider();
		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('open_ai_response');
		if (GetbibleHelper::checkString($this->help_url))
		{
			JToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}
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
		if(strlen($var) > 30)
		{
    		// use the helper htmlEscape method instead and shorten the string
			return GetbibleHelper::htmlEscape($var, $this->_charset, true, 30);
		}
		// use the helper htmlEscape method instead.
		return GetbibleHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = ($this->item->id < 1);
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_($isNew ? 'COM_GETBIBLE_OPEN_AI_RESPONSE_NEW' : 'COM_GETBIBLE_OPEN_AI_RESPONSE_EDIT'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_getbible/assets/css/open_ai_response.css", (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');

		// Add the CSS for Footable
		$this->document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
		JHtml::_('stylesheet', 'media/com_getbible/footable-v3/css/footable.standalone.min.css', ['version' => 'auto']);
		// Add the JavaScript for Footable (adding all functions)
		JHtml::_('script', 'media/com_getbible/footable-v3/js/footable.min.js', ['version' => 'auto']);

		$footable = "jQuery(document).ready(function() { jQuery(function () { jQuery('.footable').footable();});});";
		$this->document->addScriptDeclaration($footable);

		$this->document->addScript(JURI::root() . $this->script, (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript');
		$this->document->addScript(JURI::root() . "administrator/components/com_getbible/views/open_ai_response/submitbutton.js", (GetbibleHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript'); 
		JText::script('view not acceptable. Error');
	}
}
