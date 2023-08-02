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
use VDM\Joomla\Utilities\JsonHelper;
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
		$linker = trim($linker);
		// we check if this is a valid linker value
		if (Factory::_('GetBible.Linker')->valid($linker))
		{
			return ['url' => trim(trim(JUri::base(), '/') . JRoute::_('index.php?option=com_getbible&view=app&translation=' . $translation . '&Itemid=' . $this->app_params->get('app_menu', 0) . '&book=' . $book . '&chapter=' . $chapter . '&Share_His_Word=' . $linker))];
		}

		return ['error' => JText::_('COM_GETBIBLE_THIS_SESSION_KEY_IS_NOT_YET_ELIGIBLE_FOR_SHARING_AS_NO_ACTIONS_HAVE_BEEN_PERFORMED_WITHIN_IT')];
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
		$linker = trim($linker);
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
	 * Load the given chapter of the Bible into the database, or force an update
	 *
	 * @param   string    $translation    The translation abbreviation
	 * @param   int       $book           The book number
	 * @param   int       $chapter        The chapter number
	 * @param   int       $force          The switch to force an update
	 *
	 * @return  array|null
	 * @since   3.2.0
	 **/
	public function installBibleChapter(string $translation, int $book, int $chapter, int $force = 0): ?array
	{
		try
		{
			$_force = ($force == 1) ? true:false;
			Factory::_('GetBible.Watcher')->sync($translation, $book, $chapter, $_force);
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
		if (Factory::_('GetBible.Linker')->set(trim($linker)))
		{
			return ['success' => JText::_('COM_GETBIBLE_THE_SESSION_IS_SET')];
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Set the Linker Name
	 *
	 * @param   string   $name  The linker name value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function setLinkerName(string $name): array
	{
		$name = trim($name);
		if (($result = Factory::_('GetBible.Linker')->setName($name)) !== null)
		{
			return $result;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Is the Linker Authenticated
	 *
	 * @param   string       $linker    The linker GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function isLinkerAuthenticated(string $linker): array
	{
		if (($authenticated = Factory::_('GetBible.Linker')->authenticated(trim($linker))) !== null)
		{
			return $authenticated;
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
		if (($access = Factory::_('GetBible.Linker')->access(trim($linker), $pass, $oldPass)) !== null)
		{
			return $access;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Revoke the Linker access
	 *
	 * @param   string       $linker    The linker GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function revokeLinkerAccess(string $linker): array
	{
		if (($revoked = Factory::_('GetBible.Linker')->revoke(trim($linker))) !== null)
		{
			return $revoked;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Revoke the Linker session
	 *
	 * @param   string       $linker    The linker GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function revokeLinkerSession(string $linker): array
	{
		if (($revoked = Factory::_('GetBible.Linker')->revokeSession(trim($linker))) !== null)
		{
			return $revoked;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR_PLEASE_TRY_AGAIN')];
	}

	/**
	 * Create a Tag
	 *
	 * @param   string       $name           The tag name being created
	 * @param   string|null  $description    The tag description being created
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function createTag(string $name, ?string $description): array
	{
		if (($tag = Factory::_('GetBible.Tag')->create($name, $description)) !== null)
		{
			return $tag;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR')];
	}

	/**
	 * Update a Tag
	 *
	 * @param   string         $tag            The tag GUID value
	 * @param   string         $name           The tag name being created
	 * @param   string|null    $description    The tag description being created
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function updateTag(string $tag, string $name, ?string $description): array
	{
		if (($tag = Factory::_('GetBible.Tag')->update($tag, $name, $description)) !== null)
		{
			return $tag;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR')];
	}

	/**
	 * Delete a Tag
	 *
	 * @param   string    $tag   The tag GUID value
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function deleteTag(string $tag): array
	{
		if (($result = Factory::_('GetBible.Tag')->delete($tag)) !== null)
		{
			return $result;
		}

		return  ['error' => JText::_('COM_GETBIBLE_THERE_HAS_BEEN_AN_ERROR')];
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
	 * Return the build list of linkers
	 *
	 * @param   string    $linkers    The json list of linker values
	 *
	 * @return  array
	 * @since   3.2.0
	 **/
	public function getLinkersDisplay(string $linkers): array
	{
		if (JsonHelper::check($linkers))
		{
			return ['display' => JLayoutHelper::render('getbiblelinkers', json_decode($linkers))];
		}

		return  ['display' => JLayoutHelper::render('getbiblelinkers', null)];
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
