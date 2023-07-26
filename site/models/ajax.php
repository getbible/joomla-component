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

use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;
use VDM\Joomla\GetBible\Factory;
use VDM\Joomla\Utilities\GuidHelper;

/**
 * Getbible Ajax List Model
 */
class GetbibleModelAjax extends ListModel
{
	protected $app_params;

	public function __construct()
	{
		parent::__construct();
		// get params
		$this->app_params = JComponentHelper::getParams('com_getbible');

	}

	// Used in app
	/**
	 * Get share His Word URL
	 *
	 * @param   string   $linker       The linker GUID value
	 * @param   string   $translation  The translation abbreviation
	 * @param   int      $book         The book number
	 * @param   int      $chapter      The chapter number
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function getShareHisWordUrl(
		string $linker,
		string $translation,
		int $book,
		int $chapter): ?array
	{
		// we check if this is a valid linker value
		if (Factory::_('GetBible.Linker')->valid($linker))
		{
			return ['url' => trim(trim(JUri::base(), '/') . JRoute::_('index.php?option=com_getbible&view=app&t=' . $translation . '&Itemid=' . $this->app_params->get('app_menu', 0) . '&ref=' . $book . '&c=' . $chapter . '&Share_His_Word=' . $linker))];
		}

		return ['error' => JText::_('COM_GETBIBLE_THIS_SESSION_KEY_DOES_NOT_QUALIFY_FOR_SHARING')];
	}

	/**
	 * Check if the linker key is valid
	 *
	 * @param   string   $linker       The linker GUID value
	 * @param   string   $oldLinker       The old linker GUID value
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function checkValidLinker(
		string $linker,
		string $oldLinker): ?array
	{
		// we check if this is a valid linker value
		if (Factory::_('GetBible.Linker')->valid($linker)
			&& Factory::_('GetBible.Linker')->set($linker))
		{
			return [
				'success' => JText::_('COM_GETBIBLE_YOU_HAVE_ENTERED_A_VALID_SESSION_KEY'),
				'old' => Factory::_('GetBible.Linker')->valid($oldLinker)
			];
		}

		return ['error' => JText::_('COM_GETBIBLE_THIS_IS_NOT_A_VALID_SESSION_KEY')];
	}

	/**
	 * Load the given chapter of the Bible into the database
	 *
	 * @param   string    $translation    The translation abbreviation
	 * @param   int       $book           The book number
	 * @param   int       $chapter        The chapter number
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function installBibleChapter(string $translation, int $book, int $chapter): ?array
	{
		try
		{
			Factory::_('GetBible.Watcher')->api($translation, $book, $chapter);
		}
		catch(Exception $error)
		{
			return ['error' => $error->getMessage()];
		}

		return [
			'success' => JText::sprintf('COM_GETBIBLE_THE_CHAPTERS_OF_BOOKS_WAS_SUCCESSFULLY_INSTALLED_FOR_S_TRANSLATION', $chapter, $book, $translation),
			'total' => Factory::_('GetBible.Watcher')->totalVerses($translation)
		];
	}

	/**
	 * Get App URL
	 *
	 * @param   string       $translation  The translation abbreviation
	 * @param   string          $book         The book number
	 * @param   int          $chapter      The chapter number
	 * @param   int|null     $verse        The verse string
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function getAppUrl(
		string $translation,
		string $book,
		int $chapter = 1,
		?string $verse = ''): ?array
	{
		return ['url' => trim(trim(JUri::base(), '/') . JRoute::_('index.php?option=com_getbible&view=app&t=' . $translation . '&Itemid=' . $this->app_params->get('app_menu', 0) . '&ref=' . $book . '&c=' . $chapter . '&v=' . $verse))];
	}

	/**
	 * Set the session Linker
	 *
	 * @param   string   $linker  The linker GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function setLinker(string $linker): array
	{
		if (Factory::_('GetBible.Linker')->set($linker))
		{
			return ['success' => JText::_('COM_GETBIBLE_THE_SESSION_IS_SET')];
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Set the Linker access
	 *
	 * @param   string       $linker    The linker GUID value
	 * @param   string       $pass      The linker pass value
	 * @param   string|null  $oldPass   The linker old pass value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function setLinkerAccess(string $linker, string $pass, ?string $oldPass): array
	{
		if (($access = Factory::_('GetBible.Linker')->access($linker, $pass, $oldPass)) !== null)
		{
			return $access;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Set a Note
	 *
	 * @param   int          $book     The book in which the note is made
	 * @param   int          $chapter  The chapter in which the note is made
	 * @param   int          $verse    The verse where the note is made
	 * @param   string|null  $note     The note being made
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function setNote(
		int $book,
		int $chapter,
		int $verse,
		?string $note
	): array
	{
		if (($note = Factory::_('GetBible.Note')->set(
			$book,
			$chapter,
			$verse,
			$note
		)) !== null)
		{
			return $note;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Set a Tagged verse
	 *
	 * @param   string    $translation   The translation in which the note is made
	 * @param   int       $book          The book in which the note is made
	 * @param   int       $chapter       The chapter in which the note is made
	 * @param   int       $verse         The verse where the note is made
	 * @param   string    $tag           The tag being added
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function tagVerse(
		string $translation,
		int $book,
		int $chapter,
		int $verse,
		string $tag
	): array
	{
		if (($tag = Factory::_('GetBible.Tagged')->set(
			$translation,
			$book,
			$chapter,
			$verse,
			$tag
		)) !== null)
		{
			return $tag;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Remove a Tag from a verse
	 *
	 * @param   string   $tag    The tag GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function removeTagFromVerse(string $tag): array
	{
		if (($_tag = Factory::_('GetBible.Tagged')->delete($tag)) !== null)
		{
			return $_tag;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR')];
	}

	/**
	 * Set a Tag
	 *
	 * @param   string       $name          The tag name being created
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function setTag(string $name): array
	{
		if (($tag = Factory::_('GetBible.Tag')->set($name)) !== null)
		{
			return $tag;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR')];
	}

	/**
	 * Remove a Tag
	 *
	 * @param   string    $tag     The tag GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function removeTag(string $tag): array
	{
		if (Factory::_('GetBible.Tag')->delete($tag))
		{
			return ['success' => JText::_('COM_GETBIBLE_THE_TAG_WAS_SUCCESSFULLY_REMOVED')];
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR')];
	}

	// Used in search
	/**
	 * Get Search URL
	 *
	 * @param   string       $translation  The translation abbreviation
	 * @param   int          $words        The words search behaviour
	 * @param   int          $match        The match search behaviour
	 * @param   int          $case         The case search behaviour
	 * @param   int          $target        The target search behaviour
	 * @param   int          $book         The book search behaviour
	 * @param   string       $search       The search string
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function getSearchUrl(
		string $translation,
		int $words,
		int $match,
		int $case,
		int $target,
		int $book = 0,
		string $search = ''): ?array
	{
		if ($this->app_params->get('activate_search') == 1)
		{
			return ['url' => trim(trim(JUri::base(), '/') . JRoute::_('index.php?option=com_getbible&view=search&Itemid=' . $this->app_params->get('app_menu', 0) . '&t=' . $translation . '&words=' . $words . '&match=' . $match . '&case=' . $case . '&target=' . $target . '&search=' . $search))];
		}

		return ['error' => 'The search feature has not been activated. Please contact the system administrator of this website to resolve this.'];
	}

	// Used in openai
	/**
	 * Get Open AI URL
	 *
	 * @param   string       $words        The words number
	 * @param   string       $verse        The verse number
	 * @param   int          $chapter      The chapter number
	 * @param   int          $book         The book number
	 * @param   string       $translation  The translation abbreviation
	 * @param   string       $guid         The guid of the Prompt
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function getOpenaiURL(
		string $words,
		string $verse,
		int $chapter,
		int $book,
		string $translation,
		string $guid): ?array
	{
		if ($this->app_params->get('enable_open_ai') == 1 &&
			($abbreviation = GuidHelper::item($guid, 'prompt', 'a.abbreviation', 'getbible')) !== null)
		{
			if ($abbreviation === 'all' || $abbreviation === $translation)
			{
				return ['url' => trim(trim(JUri::base(), '/') . JRoute::_('index.php?option=com_getbible&view=openai&t=' . $translation . '&Itemid=' . $this->app_params->get('app_menu', 0) . '&guid=' . $guid . '&book=' . $book . '&chapter=' . $chapter . '&verse=' . $verse . '&words=' . $words))];
			}

			return ['error' => 'There was an error please try again.'];
		}

		return ['error' => 'The Open AI feature has not been activated. Please contact the system administrator of this website to resolve this.'];
	}
}
