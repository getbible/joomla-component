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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use VDM\Joomla\GetBible\Factory;

/**
 * Books Admin Controller
 */
class GetbibleControllerBooks extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_GETBIBLE_BOOKS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Book', $prefix = 'GetbibleModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function updateChaptersNames()
	{
		// Check for request forgeries
		JSession::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('book.update_chapters_names', 'com_getbible'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// check if there is any selections
			$number = UtilitiesArrayHelper::check($pks);
			if (!$number)
			{
				// Redirect to the list screen with error.
				$message = JText::_('COM_GETBIBLE_NO_BOOK_WAS_SELECTED_PLEASE_MAKE_A_SELECTION_AND_TRY_AGAIN');
				$this->setRedirect(JRoute::_('index.php?option=com_getbible&view=books', false), $message, 'error');
				return;
			}
			elseif (Factory::_('GetBible.Watcher.Chapter')->names($pks))
			{
				// Redirect to the list screen with success.
				$message = array();
				$message[] = '<h1>' . JText::_('COM_GETBIBLE_UPDATE_COMPLETED') . '</h1>';
				// get the data to export
				if ($number == 1)
				{
					$message[] = '<p>' . JText::_('COM_GETBIBLE_THE_CHAPTER_NAMES_OF_THE_BOOK_WERE_SUCCESSFULLY_UPDATED_AND_THEY_ARE_NOW_IN_SYNC_WITH_THE_GETBIBLE_API') . '</p>';
				}
				else
				{
					$message[] = '<p>' . JText::_('COM_GETBIBLE_THE_CHAPTER_NAMES_OF_THE_SELECTED_BOOKS_WERE_SUCCESSFULLY_UPDATED_AND_THEY_ARE_NOW_IN_SYNC_WITH_THE_GETBIBLE_API') . '</p>';
				}
				$this->setRedirect(JRoute::_('index.php?option=com_getbible&view=books', false), implode('', $message), 'Success');
				return;
			}
		}
		else
		{
			// Redirect to the list screen with error.
			$message = JText::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_UPDATE_THE_CHAPTER_NAMES_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR_FOR_MORE_HELP');
			$this->setRedirect(JRoute::_('index.php?option=com_getbible&view=books', false), $message, 'error');
			return;
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_GETBIBLE_UPDATE_FAILED_PLEASE_TRY_AGAIN_LATTER');
		$this->setRedirect(JRoute::_('index.php?option=com_getbible&view=books', false), $message, 'error');
		return;
	}
}
