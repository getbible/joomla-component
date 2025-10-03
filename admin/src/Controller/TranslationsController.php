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
use TrueChristianBible\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Translations Admin Controller
 *
 * @since  1.6
 */
class TranslationsController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_GETBIBLE_TRANSLATIONS';

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
	public function getModel($name = 'Translation', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Updates all translation details by syncing with the getBible API.
	 *
	 * This method fetches the latest translation metadata and updates the system.
	 * It requires user authorization and displays a success or error message accordingly.
	 *
	 * @return void
	 * @since  5.2.0
	 */
	public function updateTranslationsDetails(): void
	{
		// Protect against request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Check user authorization
		$user = $this->app->getIdentity();

		if ($user->authorise('translation.update_translations_details', 'com_getbible'))
		{
			// Attempt to sync translation details
			$updated = GetBibleFactory::_('GetBible.Watcher.Translation')->translations();

			if ($updated)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_UPDATE_COMPLETED') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_ALL_TRANSLATIONS_WERE_SUCCESSFULLY_UPDATED_AND_ARE_NOW_IN_SYNC_WITH_THE_GETBIBLE_API') . '</p>';

				$this->setRedirect(
					Route::_('index.php?option=com_getbible&view=translations', false),
					$message,
					'success'
				);
				return;
			}
		}
		else
		{
			// Unauthorized access
			$this->setRedirect(
				Route::_('index.php?option=com_getbible&view=translations', false),
				Text::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_UPDATE_TRANSLATION_DETAILS_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR'),
				'error'
			);
			return;
		}

		// Fallback error
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=translations', false),
			Text::_('COM_GETBIBLE_UPDATE_FAILED_PLEASE_TRY_AGAIN_LATER'),
			'error'
		);
	}

	/**
	 * Updates book names for selected translations by syncing with the getBible API.
	 *
	 * This method accepts a list of translation IDs, verifies authorization, and updates
	 * the book names accordingly. Messages are shown depending on success or failure.
	 *
	 * @return void
	 * @since 5.2.0
	 */
	public function updateBookNames(): void
	{
		// Protect against request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Check user authorization
		$user = $this->app->getIdentity();

		if ($user->authorise('translation.update_book_names', 'com_getbible'))
		{
			$pks = $this->input->post->get('cid', [], 'array');

			// Sanitize IDs
			ArrayHelper::toInteger($pks);

			// Ensure at least one translation was selected
			$number = UtilitiesArrayHelper::check($pks);

			if (!$number)
			{
				$this->setRedirect(
					Route::_('index.php?option=com_getbible&view=translations', false),
					Text::_('COM_GETBIBLE_NO_TRANSLATIONS_WERE_SELECTED_PLEASE_SELECT_AT_LEAST_ONE_AND_TRY_AGAIN'),
					'error'
				);
				return;
			}

			// Attempt to update book names
			if (GetBibleFactory::_('GetBible.Watcher.Book')->translations($pks))
			{
				$heading = '<h1>' . Text::_('COM_GETBIBLE_UPDATE_COMPLETED') . '</h1>';
				$body = $number === 1
					? '<p>' . Text::_('COM_GETBIBLE_THE_BOOK_NAMES_OF_THE_SELECTED_TRANSLATION_WERE_SUCCESSFULLY_UPDATED_AND_ARE_NOW_IN_SYNC_WITH_THE_GETBIBLE_API') . '</p>'
					: '<p>' . Text::_('COM_GETBIBLE_THE_BOOK_NAMES_OF_THE_SELECTED_TRANSLATIONS_WERE_SUCCESSFULLY_UPDATED_AND_ARE_NOW_IN_SYNC_WITH_THE_GETBIBLE_API') . '</p>';

				$this->setRedirect(
					Route::_('index.php?option=com_getbible&view=translations', false),
					$heading . $body,
					'success'
				);
				return;
			}
		}
		else
		{
			// Unauthorized access
			$this->setRedirect(
				Route::_('index.php?option=com_getbible&view=translations', false),
				Text::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_UPDATE_BOOK_NAMES_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR'),
				'error'
			);
			return;
		}

		// Fallback error
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=translations', false),
			Text::_('COM_GETBIBLE_UPDATE_FAILED_PLEASE_TRY_AGAIN_LATER'),
			'error'
		);
	}

	/**
	 * Removes duplicate books from the system.
	 *
	 * This method is triggered by an administrator to scan for and remove
	 * any duplicate book entries from the database. It verifies the user's
	 * permission and provides a success or error message accordingly.
	 *
	 * @return void
	 * @since  5.2.0
	 */
	public function removeDuplicateBooks(): void
	{
		// Protect against request forgeries
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Check if the user has permission to clean up duplicate books
		$user = $this->app->getIdentity();

		if ($user->authorise('book.remove_duplicate_books', 'com_getbible'))
		{
			// Perform the cleaning and get the number of removed duplicates
			$number = GetBibleFactory::_('GetBible.Cleaner')->books();

			// Prepare success message
			if ($number > 1)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_BOOK_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::sprintf('COM_GETBIBLE_A_TOTAL_OF_BS_DUPLICATE_BOOKSB_WERE_SUCCESSFULLY_REMOVED', $number) . '</p>';
			}
			elseif ($number === 1)
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_BOOK_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_ONE_DUPLICATE_BOOK_WAS_SUCCESSFULLY_REMOVED') . '</p>';
			}
			else
			{
				$message = '<h1>' . Text::_('COM_GETBIBLE_BOOK_CLEANUP_COMPLETE') . '</h1>'
					. '<p>' . Text::_('COM_GETBIBLE_NO_DUPLICATE_BOOKS_WERE_FOUND') . '</p>';
			}

			// Redirect to the books view with success message
			$this->setRedirect(
				Route::_('index.php?option=com_getbible&view=translations', false),
				$message,
				'success'
			);

			return;
		}

		// Redirect with error message if not authorized
		$this->setRedirect(
			Route::_('index.php?option=com_getbible&view=translations', false),
			Text::_('COM_GETBIBLE_YOU_DO_NOT_HAVE_PERMISSION_TO_REMOVE_DUPLICATE_BOOKS_PLEASE_CONTACT_YOUR_SYSTEM_ADMINISTRATOR'),
			'error'
		);
	}
}