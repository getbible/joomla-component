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
namespace TrueChristianBible\Component\GetBible\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use TrueChristianBible\Component\GetBible\Administrator\Helper\GetbibleHelper;
use TrueChristianBible\Joomla\GetBible\Factory as GetBibleFactory;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Verses Admin Controller
 *
 * @since  1.6
 */
class VersesController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_GETBIBLE_VERSES';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Verse', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}


	/**
	 * Removes duplicate verses from the system.
	 *
	 * This method is triggered by an administrator to scan for and remove
	 * any duplicate verse entries from the database. It verifies the user's
	 * permission and provides a success or error message accordingly.
	 *
	 * @return void
	 * @since  5.2.0
	 */
	public function removeDuplicateVerses(): void
	{
		// Protect against request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Check if the user has permission to clean up duplicate verses
		$user = $this->app->getIdentity();

		if ($user->authorise('verse.remove_duplicate_verses', 'com_getbible'))
		{
			// Perform the cleaning and get the number of removed duplicates
			$number = GetBibleFactory::_('GetBible.Cleaner')->verses();

			// Prepare success message
			if ($number > 1)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_VERSE_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::sprintf('COM_GETBIBLE_A_TOTAL_OF_BS_DUPLICATE_VERSESB_WERE_SUCCESSFULLY_REMOVED', $number) . '</p>';
			}
			elseif ($number === 1)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_VERSE_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_ONE_DUPLICATE_VERSE_WAS_SUCCESSFULLY_REMOVED') . '</p>';
			}
			else
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_VERSE_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_NO_DUPLICATE_VERSES_WERE_FOUND') . '</p>';
			}

			// Redirect to the books view with success message
			$this->setRedirect(
				Route::_('index.php?option=com_getbible&view=verses', false),
				$message,
				'success'
			);

			return;
		}

		// Redirect with error message if not authorized
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=verses', false),
			Text::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_REMOVE_DUPLICATE_VERSES_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR'),
			'error'
		);
	}
}