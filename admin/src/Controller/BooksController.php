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
use TrueChristianBible\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;
use TrueChristianBible\Joomla\GetBible\Factory as GetBibleFactory;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Books Admin Controller
 *
 * @since  1.6
 */
class BooksController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_GETBIBLE_BOOKS';

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
	public function getModel($name = 'Book', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Update the names of chapters for selected books from the getBible API.
	 *
	 * Validates the CSRF token, authorizes the user, and updates chapter names
	 * by syncing them with the getBible API for all selected books.
	 *
	 * @return void
	 * @since  5.0.15
	 */
	public function updateChaptersNames(): void
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Authorize user permission
		$user = $this->app->getIdentity();

		if (!$user->authorise('book.update_chapters_names', 'com_getbible'))
		{
			$this->redirectToBooksWithMessage(
				'You do not have permission to update the chapter names, please contact your system administrator for more help.',
				'error'
			);
			return;
		}

		// Get and sanitize selected book IDs
		$pks = $this->input->post->get('cid', [], 'array');
		ArrayHelper::toInteger($pks);
		$count = UtilitiesArrayHelper::check($pks);

		if (!$count)
		{
			$this->redirectToBooksWithMessage(
				'No book was selected, please make a selection and try again!',
				'error'
			);
			return;
		}

		// Attempt to update chapter names
		if (GetBibleFactory::_('GetBible.Watcher.Chapter')->names($pks))
		{
			$message = [
				'<h1>' . Text::_('COM_GETBIBLE_UPDATE_COMPLETED') . '</h1>',
				'<p>' . Text::_(
					$count === 1
						? 'The chapter names of the book were successfully updated, and they are now in sync with the getBible API.'
						: 'The chapter names of the selected books were successfully updated, and they are now in sync with the getBible API.'
				) . '</p>'
			];
			$this->redirectToBooksWithMessage(implode('', $message), 'success');
			return;
		}

		$this->redirectToBooksWithMessage(
			'Update failed, please try again later!',
			'error'
		);
	}

	/**
	 * Force a hash check and resync for selected book chapters via the getBible API.
	 *
	 * This will ignore internal update timestamps and force all selected books
	 * to revalidate their chapters via external sync.
	 *
	 * @return void
	 * @since  5.0.15
	 */
	public function forceHashCheck(): void
	{
		// Check for request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Authorize user permission
		$user = $this->app->getIdentity();

		if (!$user->authorise('book.force_hash_check', 'com_getbible'))
		{
			$this->redirectToBooksWithMessage(
				'You do not have permission to enable forceful check, please contact your system administrator for more help.',
				'error'
			);
			return;
		}

		// Get and sanitize selected book IDs
		$pks = $this->input->post->get('cid', [], 'array');
		ArrayHelper::toInteger($pks);
		$count = UtilitiesArrayHelper::check($pks);

		if (!$count)
		{
			$this->redirectToBooksWithMessage(
				'No book was selected, please make a selection and try again!',
				'error'
			);
			return;
		}

		// Attempt to force chapter hash check
		if (GetBibleFactory::_('GetBible.Watcher.Chapter')->force($pks))
		{
			$message = [
				'<h1>' . Text::_('COM_GETBIBLE_FORCE_HASH_CHECK_ENABLED') . '</h1>',
				'<p>' . Text::_(
					$count === 1
						? 'The chapters of the book will be forcefully synced with the getBible API.'
						: 'The chapters of the selected books will be forcefully synced with the getBible API.'
				) . '</p>'
			];
			$this->redirectToBooksWithMessage(implode('', $message), 'success');
			return;
		}

		$this->redirectToBooksWithMessage(
			'Update failed, please try again later!',
			'error'
		);
	}

	/**
	 * Helper method to redirect with a message and message type.
	 *
	 * @param   string  $message   The message to show to the user.
	 * @param   string  $type	  The message type: 'message', 'warning', 'error', 'success'.
	 *
	 * @return  void
	 * @since   5.0.15
	 */
	protected function redirectToBooksWithMessage(string $message, string $type = 'message'): void
	{
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=books', false),
			$message,
			$type
		);
	}

	/**
	 * Removes duplicate chapters from the system.
	 *
	 * This method is triggered by an administrator to scan for and remove
	 * any duplicate chapter entries from the database. It verifies the user's
	 * permission and provides a success or error message accordingly.
	 *
	 * @return void
	 * @since  5.2.0
	 */
	public function removeDuplicateChapters(): void
	{
		// Protect against request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Check if the user has permission to clean up duplicate chapters
		$user = $this->app->getIdentity();

		if ($user->authorise('chapter.remove_duplicate_chapters', 'com_getbible'))
		{
			// Perform the cleaning and get the number of removed duplicates
			$number = GetBibleFactory::_('GetBible.Cleaner')->chapters();

			// Prepare success message
			if ($number > 1)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_CHAPTER_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::sprintf('COM_GETBIBLE_A_TOTAL_OF_BS_DUPLICATE_CHAPTERSB_WERE_SUCCESSFULLY_REMOVED', $number) . '</p>';
			}
			elseif ($number === 1)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_CHAPTER_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_ONE_DUPLICATE_CHAPTER_WAS_SUCCESSFULLY_REMOVED') . '</p>';
			}
			else
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_CHAPTER_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_NO_DUPLICATE_CHAPTERS_WERE_FOUND') . '</p>';
			}

			// Redirect to the books view with success message
			$this->setRedirect(
				Route::_('index.php?option=com_getbible&view=books', false),
				$message,
				'success'
			);

			return;
		}

		// Redirect with error message if not authorized
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=books', false),
			Text::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_REMOVE_DUPLICATE_CHAPTERS_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR'),
			'error'
		);
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
				Route::_('index.php?option=com_getbible&view=books', false),
				$message,
				'success'
			);

			return;
		}

		// Redirect with error message if not authorized
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=books', false),
			Text::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_REMOVE_DUPLICATE_VERSES_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR'),
			'error'
		);
	}
}