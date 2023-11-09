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

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
JHTML::_('bootstrap.renderModal');

/**
 * Script File of Getbible Component
 */
class com_getbibleInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(ComponentAdapter $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(ComponentAdapter $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 */
	public function uninstall(ComponentAdapter $parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Note alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.note') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$note_found = $db->getNumRows();
		// Now check if there were any rows
		if ($note_found)
		{
			// Since there are load the needed  note type ids
			$note_ids = $db->loadColumn();
			// Remove Note from the content type table
			$note_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.note') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($note_condition);
			$db->setQuery($query);
			// Execute the query to remove Note items
			$note_done = $db->execute();
			if ($note_done)
			{
				// If successfully remove Note add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.note) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Note items from the contentitem tag map table
			$note_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.note') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($note_condition);
			$db->setQuery($query);
			// Execute the query to remove Note items
			$note_done = $db->execute();
			if ($note_done)
			{
				// If successfully remove Note add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.note) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Note items from the ucm content table
			$note_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_getbible.note') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($note_condition);
			$db->setQuery($query);
			// Execute the query to remove Note items
			$note_done = $db->execute();
			if ($note_done)
			{
				// If successfully removed Note add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.note) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Note items are cleared from DB
			foreach ($note_ids as $note_id)
			{
				// Remove Note items from the ucm base table
				$note_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $note_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($note_condition);
				$db->setQuery($query);
				// Execute the query to remove Note items
				$db->execute();

				// Remove Note items from the ucm history table
				$note_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $note_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($note_condition);
				$db->setQuery($query);
				// Execute the query to remove Note items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Tagged_verse alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tagged_verse') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$tagged_verse_found = $db->getNumRows();
		// Now check if there were any rows
		if ($tagged_verse_found)
		{
			// Since there are load the needed  tagged_verse type ids
			$tagged_verse_ids = $db->loadColumn();
			// Remove Tagged_verse from the content type table
			$tagged_verse_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tagged_verse') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($tagged_verse_condition);
			$db->setQuery($query);
			// Execute the query to remove Tagged_verse items
			$tagged_verse_done = $db->execute();
			if ($tagged_verse_done)
			{
				// If successfully remove Tagged_verse add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.tagged_verse) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Tagged_verse items from the contentitem tag map table
			$tagged_verse_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tagged_verse') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($tagged_verse_condition);
			$db->setQuery($query);
			// Execute the query to remove Tagged_verse items
			$tagged_verse_done = $db->execute();
			if ($tagged_verse_done)
			{
				// If successfully remove Tagged_verse add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.tagged_verse) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Tagged_verse items from the ucm content table
			$tagged_verse_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_getbible.tagged_verse') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($tagged_verse_condition);
			$db->setQuery($query);
			// Execute the query to remove Tagged_verse items
			$tagged_verse_done = $db->execute();
			if ($tagged_verse_done)
			{
				// If successfully removed Tagged_verse add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.tagged_verse) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Tagged_verse items are cleared from DB
			foreach ($tagged_verse_ids as $tagged_verse_id)
			{
				// Remove Tagged_verse items from the ucm base table
				$tagged_verse_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tagged_verse_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($tagged_verse_condition);
				$db->setQuery($query);
				// Execute the query to remove Tagged_verse items
				$db->execute();

				// Remove Tagged_verse items from the ucm history table
				$tagged_verse_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tagged_verse_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($tagged_verse_condition);
				$db->setQuery($query);
				// Execute the query to remove Tagged_verse items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Prompt alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.prompt') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$prompt_found = $db->getNumRows();
		// Now check if there were any rows
		if ($prompt_found)
		{
			// Since there are load the needed  prompt type ids
			$prompt_ids = $db->loadColumn();
			// Remove Prompt from the content type table
			$prompt_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.prompt') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($prompt_condition);
			$db->setQuery($query);
			// Execute the query to remove Prompt items
			$prompt_done = $db->execute();
			if ($prompt_done)
			{
				// If successfully remove Prompt add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.prompt) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Prompt items from the contentitem tag map table
			$prompt_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.prompt') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($prompt_condition);
			$db->setQuery($query);
			// Execute the query to remove Prompt items
			$prompt_done = $db->execute();
			if ($prompt_done)
			{
				// If successfully remove Prompt add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.prompt) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Prompt items from the ucm content table
			$prompt_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_getbible.prompt') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($prompt_condition);
			$db->setQuery($query);
			// Execute the query to remove Prompt items
			$prompt_done = $db->execute();
			if ($prompt_done)
			{
				// If successfully removed Prompt add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.prompt) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Prompt items are cleared from DB
			foreach ($prompt_ids as $prompt_id)
			{
				// Remove Prompt items from the ucm base table
				$prompt_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $prompt_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($prompt_condition);
				$db->setQuery($query);
				// Execute the query to remove Prompt items
				$db->execute();

				// Remove Prompt items from the ucm history table
				$prompt_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $prompt_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($prompt_condition);
				$db->setQuery($query);
				// Execute the query to remove Prompt items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Open_ai_response alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_response') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$open_ai_response_found = $db->getNumRows();
		// Now check if there were any rows
		if ($open_ai_response_found)
		{
			// Since there are load the needed  open_ai_response type ids
			$open_ai_response_ids = $db->loadColumn();
			// Remove Open_ai_response from the content type table
			$open_ai_response_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_response') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($open_ai_response_condition);
			$db->setQuery($query);
			// Execute the query to remove Open_ai_response items
			$open_ai_response_done = $db->execute();
			if ($open_ai_response_done)
			{
				// If successfully remove Open_ai_response add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.open_ai_response) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Open_ai_response items from the contentitem tag map table
			$open_ai_response_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_response') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($open_ai_response_condition);
			$db->setQuery($query);
			// Execute the query to remove Open_ai_response items
			$open_ai_response_done = $db->execute();
			if ($open_ai_response_done)
			{
				// If successfully remove Open_ai_response add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.open_ai_response) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Open_ai_response items from the ucm content table
			$open_ai_response_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_getbible.open_ai_response') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($open_ai_response_condition);
			$db->setQuery($query);
			// Execute the query to remove Open_ai_response items
			$open_ai_response_done = $db->execute();
			if ($open_ai_response_done)
			{
				// If successfully removed Open_ai_response add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.open_ai_response) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Open_ai_response items are cleared from DB
			foreach ($open_ai_response_ids as $open_ai_response_id)
			{
				// Remove Open_ai_response items from the ucm base table
				$open_ai_response_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $open_ai_response_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($open_ai_response_condition);
				$db->setQuery($query);
				// Execute the query to remove Open_ai_response items
				$db->execute();

				// Remove Open_ai_response items from the ucm history table
				$open_ai_response_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $open_ai_response_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($open_ai_response_condition);
				$db->setQuery($query);
				// Execute the query to remove Open_ai_response items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Open_ai_message alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_message') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$open_ai_message_found = $db->getNumRows();
		// Now check if there were any rows
		if ($open_ai_message_found)
		{
			// Since there are load the needed  open_ai_message type ids
			$open_ai_message_ids = $db->loadColumn();
			// Remove Open_ai_message from the content type table
			$open_ai_message_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_message') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($open_ai_message_condition);
			$db->setQuery($query);
			// Execute the query to remove Open_ai_message items
			$open_ai_message_done = $db->execute();
			if ($open_ai_message_done)
			{
				// If successfully remove Open_ai_message add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.open_ai_message) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Open_ai_message items from the contentitem tag map table
			$open_ai_message_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_message') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($open_ai_message_condition);
			$db->setQuery($query);
			// Execute the query to remove Open_ai_message items
			$open_ai_message_done = $db->execute();
			if ($open_ai_message_done)
			{
				// If successfully remove Open_ai_message add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.open_ai_message) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Open_ai_message items from the ucm content table
			$open_ai_message_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_getbible.open_ai_message') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($open_ai_message_condition);
			$db->setQuery($query);
			// Execute the query to remove Open_ai_message items
			$open_ai_message_done = $db->execute();
			if ($open_ai_message_done)
			{
				// If successfully removed Open_ai_message add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.open_ai_message) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Open_ai_message items are cleared from DB
			foreach ($open_ai_message_ids as $open_ai_message_id)
			{
				// Remove Open_ai_message items from the ucm base table
				$open_ai_message_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $open_ai_message_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($open_ai_message_condition);
				$db->setQuery($query);
				// Execute the query to remove Open_ai_message items
				$db->execute();

				// Remove Open_ai_message items from the ucm history table
				$open_ai_message_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $open_ai_message_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($open_ai_message_condition);
				$db->setQuery($query);
				// Execute the query to remove Open_ai_message items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Tag alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tag') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$tag_found = $db->getNumRows();
		// Now check if there were any rows
		if ($tag_found)
		{
			// Since there are load the needed  tag type ids
			$tag_ids = $db->loadColumn();
			// Remove Tag from the content type table
			$tag_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tag') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($tag_condition);
			$db->setQuery($query);
			// Execute the query to remove Tag items
			$tag_done = $db->execute();
			if ($tag_done)
			{
				// If successfully remove Tag add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.tag) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Tag items from the contentitem tag map table
			$tag_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tag') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($tag_condition);
			$db->setQuery($query);
			// Execute the query to remove Tag items
			$tag_done = $db->execute();
			if ($tag_done)
			{
				// If successfully remove Tag add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.tag) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Tag items from the ucm content table
			$tag_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_getbible.tag') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($tag_condition);
			$db->setQuery($query);
			// Execute the query to remove Tag items
			$tag_done = $db->execute();
			if ($tag_done)
			{
				// If successfully removed Tag add queued success message.
				$app->enqueueMessage(JText::_('The (com_getbible.tag) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Tag items are cleared from DB
			foreach ($tag_ids as $tag_id)
			{
				// Remove Tag items from the ucm base table
				$tag_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tag_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($tag_condition);
				$db->setQuery($query);
				// Execute the query to remove Tag items
				$db->execute();

				// Remove Tag items from the ucm history table
				$tag_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $tag_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($tag_condition);
				$db->setQuery($query);
				// Execute the query to remove Tag items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove getbible assets from the assets table
		$getbible_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_getbible%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($getbible_condition);
		$db->setQuery($query);
		$tag_done = $db->execute();
		if ($tag_done)
		{
			// If successfully removed getbible add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// Get the biggest rule column in the assets table at this point.
		$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$db->setQuery($get_rule_length);
		if ($db->execute())
		{
			$rule_length = $db->loadResult();
			// Check the size of the rules column
			if ($rule_length < 5120)
			{
				// Revert the assets table rules column back to the default
				$revert_rule = "ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';";
				$db->setQuery($revert_rule);
				$db->execute();
				$app->enqueueMessage(JText::_('Reverted the <b>#__assets</b> table rules column back to its default size of varchar(5120)'));
			}
			else
			{

				$app->enqueueMessage(JText::_('Could not revert the <b>#__assets</b> table rules column back to its default size of varchar(5120), since there is still one or more components that still requires the column to be larger.'));
			}
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible from the action_logs_extensions table
		$getbible_action_logs_extensions = array( $db->quoteName('extension') . ' = ' . $db->quote('com_getbible') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_logs_extensions'));
		$query->where($getbible_action_logs_extensions);
		$db->setQuery($query);
		// Execute the query to remove Getbible
		$getbible_removed_done = $db->execute();
		if ($getbible_removed_done)
		{
			// If successfully remove Getbible add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible extension was removed from the <b>#__action_logs_extensions</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Linker from the action_log_config table
		$linker_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.linker') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($linker_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.linker
		$linker_action_log_config_done = $db->execute();
		if ($linker_action_log_config_done)
		{
			// If successfully removed Getbible Linker add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.linker type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Note from the action_log_config table
		$note_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.note') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($note_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.note
		$note_action_log_config_done = $db->execute();
		if ($note_action_log_config_done)
		{
			// If successfully removed Getbible Note add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.note type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Tagged_verse from the action_log_config table
		$tagged_verse_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tagged_verse') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($tagged_verse_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.tagged_verse
		$tagged_verse_action_log_config_done = $db->execute();
		if ($tagged_verse_action_log_config_done)
		{
			// If successfully removed Getbible Tagged_verse add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.tagged_verse type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Prompt from the action_log_config table
		$prompt_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.prompt') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($prompt_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.prompt
		$prompt_action_log_config_done = $db->execute();
		if ($prompt_action_log_config_done)
		{
			// If successfully removed Getbible Prompt add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.prompt type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Open_ai_response from the action_log_config table
		$open_ai_response_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_response') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($open_ai_response_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.open_ai_response
		$open_ai_response_action_log_config_done = $db->execute();
		if ($open_ai_response_action_log_config_done)
		{
			// If successfully removed Getbible Open_ai_response add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.open_ai_response type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Open_ai_message from the action_log_config table
		$open_ai_message_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.open_ai_message') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($open_ai_message_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.open_ai_message
		$open_ai_message_action_log_config_done = $db->execute();
		if ($open_ai_message_action_log_config_done)
		{
			// If successfully removed Getbible Open_ai_message add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.open_ai_message type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Password from the action_log_config table
		$password_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.password') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($password_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.password
		$password_action_log_config_done = $db->execute();
		if ($password_action_log_config_done)
		{
			// If successfully removed Getbible Password add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.password type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Tag from the action_log_config table
		$tag_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.tag') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($tag_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.tag
		$tag_action_log_config_done = $db->execute();
		if ($tag_action_log_config_done)
		{
			// If successfully removed Getbible Tag add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.tag type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Translation from the action_log_config table
		$translation_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.translation') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($translation_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.translation
		$translation_action_log_config_done = $db->execute();
		if ($translation_action_log_config_done)
		{
			// If successfully removed Getbible Translation add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.translation type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Book from the action_log_config table
		$book_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.book') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($book_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.book
		$book_action_log_config_done = $db->execute();
		if ($book_action_log_config_done)
		{
			// If successfully removed Getbible Book add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.book type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Chapter from the action_log_config table
		$chapter_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.chapter') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($chapter_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.chapter
		$chapter_action_log_config_done = $db->execute();
		if ($chapter_action_log_config_done)
		{
			// If successfully removed Getbible Chapter add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.chapter type alias was removed from the <b>#__action_log_config</b> table'));
		}

		// Set db if not set already.
		if (!isset($db))
		{
			$db = JFactory::getDbo();
		}
		// Set app if not set already.
		if (!isset($app))
		{
			$app = JFactory::getApplication();
		}
		// Remove Getbible Verse from the action_log_config table
		$verse_action_log_config = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_getbible.verse') );
		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__action_log_config'));
		$query->where($verse_action_log_config);
		$db->setQuery($query);
		// Execute the query to remove com_getbible.verse
		$verse_action_log_config_done = $db->execute();
		if ($verse_action_log_config_done)
		{
			// If successfully removed Getbible Verse add queued success message.
			$app->enqueueMessage(JText::_('The com_getbible.verse type alias was removed from the <b>#__action_log_config</b> table'));
		}
		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:joomla@vdm.io">joomla@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://getbible.net" target="_blank">https://getbible.net</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(ComponentAdapter $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, ComponentAdapter $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
			// check if this has the old getBible extension installed
			$path = JPATH_ADMINISTRATOR . '/components/com_getbible/helpers/get.php';
			if (is_readable($path))
			{
				$app->enqueueMessage("
					<p>Unfortunately, your attempt to install getBible Version 2 has failed. The reason for this failure is due to an existing installation of the previous version which is incompatible with our latest update.
					We deeply regret this interruption to your installation process. To resolve this issue, please first uninstall the old version of getBible. If you have personalized notes and tags in the current version, <b>don't forget to manually back them up</b>, as they will be lost otherwise.</p>
					<p>We understand that this process may be inconvenient and could potentially pose challenges. Therefore, we've put a support team in place to assist you. Should you require any technical assistance or have questions, please don't hesitate to reach out to us at: <a href='https://git.vdm.dev/getBible/support' title='getBible Support'>getBible Support</a>.
					We appreciate your understanding and cooperation during this transition to getBible Version 2. Our team is committed to helping you every step of the way. Thank you!</p>", 'error');

				return false;
			}
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (File::exists(JPATH_ADMINISTRATOR . '/components/com_getbible/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_getbible/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			File::delete(JPATH_ADMINISTRATOR . '/components/com_getbible/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   ComponentAdapter  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, ComponentAdapter $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the note content type object.
			$note = new stdClass();
			$note->type_title = 'Getbible Note';
			$note->type_alias = 'com_getbible.note';
			$note->table = '{"special": {"dbtable": "#__getbible_note","key": "id","type": "Note","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$note->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","linker":"linker","guid":"guid","note":"note","verse":"verse","chapter":"chapter"}}';
			$note->router = 'GetbibleHelperRoute::getNoteRoute';
			$note->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/note.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$note_Inserted = $db->insertObject('#__content_types', $note);

			// Create the tagged_verse content type object.
			$tagged_verse = new stdClass();
			$tagged_verse->type_title = 'Getbible Tagged_verse';
			$tagged_verse->type_alias = 'com_getbible.tagged_verse';
			$tagged_verse->table = '{"special": {"dbtable": "#__getbible_tagged_verse","key": "id","type": "Tagged_verse","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tagged_verse->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","abbreviation":"abbreviation","linker":"linker","tag":"tag","guid":"guid","verse":"verse","chapter":"chapter"}}';
			$tagged_verse->router = 'GetbibleHelperRoute::getTagged_verseRoute';
			$tagged_verse->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/tagged_verse.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "tag","targetTable": "#__getbible_tag","targetColumn": "guid","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$tagged_verse_Inserted = $db->insertObject('#__content_types', $tagged_verse);

			// Create the prompt content type object.
			$prompt = new stdClass();
			$prompt->type_title = 'Getbible Prompt';
			$prompt->type_alias = 'com_getbible.prompt';
			$prompt->table = '{"special": {"dbtable": "#__getbible_prompt","key": "id","type": "Prompt","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$prompt->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","integration":"integration","cache_behaviour":"cache_behaviour","abbreviation":"abbreviation","guid":"guid","model":"model","presence_penalty":"presence_penalty","org_token":"org_token","token":"token","n_override":"n_override","cache_capacity":"cache_capacity","response_retrieval":"response_retrieval","frequency_penalty_override":"frequency_penalty_override","n":"n","max_tokens_override":"max_tokens_override","token_override":"token_override","max_tokens":"max_tokens","ai_org_token_override":"ai_org_token_override","temperature_override":"temperature_override","presence_penalty_override":"presence_penalty_override","top_p_override":"top_p_override","frequency_penalty":"frequency_penalty","top_p":"top_p","temperature":"temperature"}}';
			$prompt->router = 'GetbibleHelperRoute::getPromptRoute';
			$prompt->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/prompt.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","integration","cache_behaviour","n_override","cache_capacity","response_retrieval","frequency_penalty_override","n","max_tokens_override","token_override","max_tokens","ai_org_token_override","temperature_override","presence_penalty_override","top_p_override"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}';

			// Set the object into the content types table.
			$prompt_Inserted = $db->insertObject('#__content_types', $prompt);

			// Create the open_ai_response content type object.
			$open_ai_response = new stdClass();
			$open_ai_response->type_title = 'Getbible Open_ai_response';
			$open_ai_response->type_alias = 'com_getbible.open_ai_response';
			$open_ai_response->table = '{"special": {"dbtable": "#__getbible_open_ai_response","key": "id","type": "Open_ai_response","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$open_ai_response->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "response_id","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"response_id":"response_id","prompt":"prompt","response_object":"response_object","response_model":"response_model","total_tokens":"total_tokens","n":"n","frequency_penalty":"frequency_penalty","presence_penalty":"presence_penalty","word":"word","chapter":"chapter","lcsh":"lcsh","completion_tokens":"completion_tokens","prompt_tokens":"prompt_tokens","response_created":"response_created","abbreviation":"abbreviation","language":"language","max_tokens":"max_tokens","book":"book","temperature":"temperature","verse":"verse","top_p":"top_p","selected_word":"selected_word","model":"model"}}';
			$open_ai_response->router = 'GetbibleHelperRoute::getOpen_ai_responseRoute';
			$open_ai_response->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/open_ai_response.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","total_tokens","n","chapter","completion_tokens","prompt_tokens","max_tokens","book"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}';

			// Set the object into the content types table.
			$open_ai_response_Inserted = $db->insertObject('#__content_types', $open_ai_response);

			// Create the open_ai_message content type object.
			$open_ai_message = new stdClass();
			$open_ai_message->type_title = 'Getbible Open_ai_message';
			$open_ai_message->type_alias = 'com_getbible.open_ai_message';
			$open_ai_message->table = '{"special": {"dbtable": "#__getbible_open_ai_message","key": "id","type": "Open_ai_message","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$open_ai_message->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "role","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"role":"role","open_ai_response":"open_ai_response","prompt":"prompt","source":"source","content":"content","name":"name","index":"index"}}';
			$open_ai_message->router = 'GetbibleHelperRoute::getOpen_ai_messageRoute';
			$open_ai_message->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/open_ai_message.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","source","index"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "open_ai_response","targetTable": "#__getbible_open_ai_response","targetColumn": "response_id","displayColumn": "response_id"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$open_ai_message_Inserted = $db->insertObject('#__content_types', $open_ai_message);

			// Create the tag content type object.
			$tag = new stdClass();
			$tag->type_title = 'Getbible Tag';
			$tag->type_alias = 'com_getbible.tag';
			$tag->table = '{"special": {"dbtable": "#__getbible_tag","key": "id","type": "Tag","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tag->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","linker":"linker","guid":"guid","description":"description"}}';
			$tag->router = 'GetbibleHelperRoute::getTagRoute';
			$tag->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/tag.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","access"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$tag_Inserted = $db->insertObject('#__content_types', $tag);


			// Install the global extension params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Llewellyn van der Merwe","autorEmail":"joomla@vdm.io","default_translation":"kjv","show_install_button":"0","show_getbible_logo":"1","show_getbible_link":"1","show_hash_validation":"1","show_api_link":"1","activate_search":"0","search_found_color":"#4747ff","table_selection_color":"#dfdfdf","search_words":"1","search_match":"1","search_case":"1","bottom_search_position":"div","show_bottom_search_position_card":"1","bottom_search_position_card_style":"default","activate_notes":"0","activate_tags":"0","allow_untagging":"0","bottom_tag_position":"div","show_bottom_tag_position_card":"1","bottom_tag_position_card_style":"default","activate_sharing":"1","verse_layout_share":"1","verse_number_share":"1","local_link_share":"1","text_reference_share":"3","type_translation_share":"2","default_format_share":"1","verse_selected_color":"#4747ff","show_header":"1","verse_per_line":"1","show_top_menu":"1","top_menu_type":"1","show_bottom_menu":"0","bottom_menu_type":"1","previous_next_navigation":"1","set_custom_tabs":"0","custom_tabs":"div","set_default_tab_names":"0","custom_icons":"0","show_scripture_tab_text":"1","show_scripture_icon":"1","show_scripture_card":"1","scripture_card_style":"default","show_books_tab_text":"1","show_books_icon":"1","show_books_card":"1","books_card_style":"default","show_chapters_tab_text":"1","show_chapters_icon":"1","show_chapters_card":"1","chapters_card_style":"default","show_translations_tab_text":"1","show_translations_icon":"1","show_translations_card":"1","translations_card_style":"default","show_settings":"0","show_settings_tab_text":"1","show_settings_icon":"1","show_settings_card":"1","settings_card_style":"default","show_details":"1","show_details_tab_text":"1","show_details_icon":"1","show_details_card":"1","details_card_style":"default","bottom_app_position":"div","show_bottom_app_position_card":"1","bottom_app_position_card_style":"default","debug":"0","enable_open_ai":"0","openai_model":"gpt-4","openai_token":"secret","enable_open_ai_org":"0","openai_org_token":"secret","openai_max_tokens":"300","openai_temperature":"1","openai_top_p":"1","openai_n":"1","openai_presence_penalty":"0","openai_frequency_penalty":"0","bottom_ai_position":"div","show_bottom_ai_position_card":"1","bottom_ai_position_card_style":"default","check_in":"-1 day","save_history":"1","history_limit":"10","titleContributor1":"Modules","nameContributor1":"CrossWire","emailContributor1":"sword-support@crosswire.org","linkContributor1":"https://wiki.crosswire.org/","useContributor1":"2","showContributor1":"3","add_jquery_framework":"1","uikit_load":"1","uikit_min":""}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_getbible')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			// Get the biggest rule column in the assets table at this point.
			$get_rule_length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
			$db->setQuery($get_rule_length);
			if ($db->execute())
			{
				$rule_length = $db->loadResult();
				// Check the size of the rules column
				if ($rule_length <= 44480)
				{
					// Fix the assets table rules column size
					$fix_rules_size = "ALTER TABLE `#__assets` CHANGE `rules` `rules` TEXT NOT NULL COMMENT 'JSON encoded access control. Enlarged to TEXT by JCB';";
					$db->setQuery($fix_rules_size);
					$db->execute();
					$app->enqueueMessage(JText::_('The <b>#__assets</b> table rules column was resized to the TEXT datatype for the components possible large permission rules.'));
				}
			}
			echo '<a target="_blank" href="https://getbible.net" title="Get Bible">
				<img src="components/com_getbible/assets/images/vdm-component.jpg"/>
				</a>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the getbible action logs extensions object.
			$getbible_action_logs_extensions = new stdClass();
			$getbible_action_logs_extensions->extension = 'com_getbible';

			// Set the object into the action logs extensions table.
			$getbible_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $getbible_action_logs_extensions);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the linker action log config object.
			$linker_action_log_config = new stdClass();
			$linker_action_log_config->type_title = 'LINKER';
			$linker_action_log_config->type_alias = 'com_getbible.linker';
			$linker_action_log_config->id_holder = 'id';
			$linker_action_log_config->title_holder = 'name';
			$linker_action_log_config->table_name = '#__getbible_linker';
			$linker_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$linker_Inserted = $db->insertObject('#__action_log_config', $linker_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the note action log config object.
			$note_action_log_config = new stdClass();
			$note_action_log_config->type_title = 'NOTE';
			$note_action_log_config->type_alias = 'com_getbible.note';
			$note_action_log_config->id_holder = 'id';
			$note_action_log_config->title_holder = 'book_nr';
			$note_action_log_config->table_name = '#__getbible_note';
			$note_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$note_Inserted = $db->insertObject('#__action_log_config', $note_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the tagged_verse action log config object.
			$tagged_verse_action_log_config = new stdClass();
			$tagged_verse_action_log_config->type_title = 'TAGGED_VERSE';
			$tagged_verse_action_log_config->type_alias = 'com_getbible.tagged_verse';
			$tagged_verse_action_log_config->id_holder = 'id';
			$tagged_verse_action_log_config->title_holder = 'book_nr';
			$tagged_verse_action_log_config->table_name = '#__getbible_tagged_verse';
			$tagged_verse_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$tagged_verse_Inserted = $db->insertObject('#__action_log_config', $tagged_verse_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the prompt action log config object.
			$prompt_action_log_config = new stdClass();
			$prompt_action_log_config->type_title = 'PROMPT';
			$prompt_action_log_config->type_alias = 'com_getbible.prompt';
			$prompt_action_log_config->id_holder = 'id';
			$prompt_action_log_config->title_holder = 'name';
			$prompt_action_log_config->table_name = '#__getbible_prompt';
			$prompt_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$prompt_Inserted = $db->insertObject('#__action_log_config', $prompt_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the open_ai_response action log config object.
			$open_ai_response_action_log_config = new stdClass();
			$open_ai_response_action_log_config->type_title = 'OPEN_AI_RESPONSE';
			$open_ai_response_action_log_config->type_alias = 'com_getbible.open_ai_response';
			$open_ai_response_action_log_config->id_holder = 'id';
			$open_ai_response_action_log_config->title_holder = 'response_id';
			$open_ai_response_action_log_config->table_name = '#__getbible_open_ai_response';
			$open_ai_response_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$open_ai_response_Inserted = $db->insertObject('#__action_log_config', $open_ai_response_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the open_ai_message action log config object.
			$open_ai_message_action_log_config = new stdClass();
			$open_ai_message_action_log_config->type_title = 'OPEN_AI_MESSAGE';
			$open_ai_message_action_log_config->type_alias = 'com_getbible.open_ai_message';
			$open_ai_message_action_log_config->id_holder = 'id';
			$open_ai_message_action_log_config->title_holder = 'role';
			$open_ai_message_action_log_config->table_name = '#__getbible_open_ai_message';
			$open_ai_message_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$open_ai_message_Inserted = $db->insertObject('#__action_log_config', $open_ai_message_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the password action log config object.
			$password_action_log_config = new stdClass();
			$password_action_log_config->type_title = 'PASSWORD';
			$password_action_log_config->type_alias = 'com_getbible.password';
			$password_action_log_config->id_holder = 'id';
			$password_action_log_config->title_holder = 'name';
			$password_action_log_config->table_name = '#__getbible_password';
			$password_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$password_Inserted = $db->insertObject('#__action_log_config', $password_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the tag action log config object.
			$tag_action_log_config = new stdClass();
			$tag_action_log_config->type_title = 'TAG';
			$tag_action_log_config->type_alias = 'com_getbible.tag';
			$tag_action_log_config->id_holder = 'id';
			$tag_action_log_config->title_holder = 'name';
			$tag_action_log_config->table_name = '#__getbible_tag';
			$tag_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$tag_Inserted = $db->insertObject('#__action_log_config', $tag_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the translation action log config object.
			$translation_action_log_config = new stdClass();
			$translation_action_log_config->type_title = 'TRANSLATION';
			$translation_action_log_config->type_alias = 'com_getbible.translation';
			$translation_action_log_config->id_holder = 'id';
			$translation_action_log_config->title_holder = 'translation';
			$translation_action_log_config->table_name = '#__getbible_translation';
			$translation_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$translation_Inserted = $db->insertObject('#__action_log_config', $translation_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the book action log config object.
			$book_action_log_config = new stdClass();
			$book_action_log_config->type_title = 'BOOK';
			$book_action_log_config->type_alias = 'com_getbible.book';
			$book_action_log_config->id_holder = 'id';
			$book_action_log_config->title_holder = 'name';
			$book_action_log_config->table_name = '#__getbible_book';
			$book_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$book_Inserted = $db->insertObject('#__action_log_config', $book_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the chapter action log config object.
			$chapter_action_log_config = new stdClass();
			$chapter_action_log_config->type_title = 'CHAPTER';
			$chapter_action_log_config->type_alias = 'com_getbible.chapter';
			$chapter_action_log_config->id_holder = 'id';
			$chapter_action_log_config->title_holder = 'name';
			$chapter_action_log_config->table_name = '#__getbible_chapter';
			$chapter_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$chapter_Inserted = $db->insertObject('#__action_log_config', $chapter_action_log_config);

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the verse action log config object.
			$verse_action_log_config = new stdClass();
			$verse_action_log_config->type_title = 'VERSE';
			$verse_action_log_config->type_alias = 'com_getbible.verse';
			$verse_action_log_config->id_holder = 'id';
			$verse_action_log_config->title_holder = 'book_nr';
			$verse_action_log_config->table_name = '#__getbible_verse';
			$verse_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Set the object into the action log config table.
			$verse_Inserted = $db->insertObject('#__action_log_config', $verse_action_log_config);
		}
		// do any updates needed
		if ($type === 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the note content type object.
			$note = new stdClass();
			$note->type_title = 'Getbible Note';
			$note->type_alias = 'com_getbible.note';
			$note->table = '{"special": {"dbtable": "#__getbible_note","key": "id","type": "Note","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$note->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","linker":"linker","guid":"guid","note":"note","verse":"verse","chapter":"chapter"}}';
			$note->router = 'GetbibleHelperRoute::getNoteRoute';
			$note->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/note.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}';

			// Check if note type is already in content_type DB.
			$note_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($note->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$note->type_id = $db->loadResult();
				$note_Updated = $db->updateObject('#__content_types', $note, 'type_id');
			}
			else
			{
				$note_Inserted = $db->insertObject('#__content_types', $note);
			}

			// Create the tagged_verse content type object.
			$tagged_verse = new stdClass();
			$tagged_verse->type_title = 'Getbible Tagged_verse';
			$tagged_verse->type_alias = 'com_getbible.tagged_verse';
			$tagged_verse->table = '{"special": {"dbtable": "#__getbible_tagged_verse","key": "id","type": "Tagged_verse","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tagged_verse->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","abbreviation":"abbreviation","linker":"linker","tag":"tag","guid":"guid","verse":"verse","chapter":"chapter"}}';
			$tagged_verse->router = 'GetbibleHelperRoute::getTagged_verseRoute';
			$tagged_verse->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/tagged_verse.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "tag","targetTable": "#__getbible_tag","targetColumn": "guid","displayColumn": "name"}]}';

			// Check if tagged_verse type is already in content_type DB.
			$tagged_verse_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tagged_verse->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tagged_verse->type_id = $db->loadResult();
				$tagged_verse_Updated = $db->updateObject('#__content_types', $tagged_verse, 'type_id');
			}
			else
			{
				$tagged_verse_Inserted = $db->insertObject('#__content_types', $tagged_verse);
			}

			// Create the prompt content type object.
			$prompt = new stdClass();
			$prompt->type_title = 'Getbible Prompt';
			$prompt->type_alias = 'com_getbible.prompt';
			$prompt->table = '{"special": {"dbtable": "#__getbible_prompt","key": "id","type": "Prompt","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$prompt->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","integration":"integration","cache_behaviour":"cache_behaviour","abbreviation":"abbreviation","guid":"guid","model":"model","presence_penalty":"presence_penalty","org_token":"org_token","token":"token","n_override":"n_override","cache_capacity":"cache_capacity","response_retrieval":"response_retrieval","frequency_penalty_override":"frequency_penalty_override","n":"n","max_tokens_override":"max_tokens_override","token_override":"token_override","max_tokens":"max_tokens","ai_org_token_override":"ai_org_token_override","temperature_override":"temperature_override","presence_penalty_override":"presence_penalty_override","top_p_override":"top_p_override","frequency_penalty":"frequency_penalty","top_p":"top_p","temperature":"temperature"}}';
			$prompt->router = 'GetbibleHelperRoute::getPromptRoute';
			$prompt->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/prompt.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","integration","cache_behaviour","n_override","cache_capacity","response_retrieval","frequency_penalty_override","n","max_tokens_override","token_override","max_tokens","ai_org_token_override","temperature_override","presence_penalty_override","top_p_override"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}';

			// Check if prompt type is already in content_type DB.
			$prompt_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($prompt->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$prompt->type_id = $db->loadResult();
				$prompt_Updated = $db->updateObject('#__content_types', $prompt, 'type_id');
			}
			else
			{
				$prompt_Inserted = $db->insertObject('#__content_types', $prompt);
			}

			// Create the open_ai_response content type object.
			$open_ai_response = new stdClass();
			$open_ai_response->type_title = 'Getbible Open_ai_response';
			$open_ai_response->type_alias = 'com_getbible.open_ai_response';
			$open_ai_response->table = '{"special": {"dbtable": "#__getbible_open_ai_response","key": "id","type": "Open_ai_response","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$open_ai_response->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "response_id","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"response_id":"response_id","prompt":"prompt","response_object":"response_object","response_model":"response_model","total_tokens":"total_tokens","n":"n","frequency_penalty":"frequency_penalty","presence_penalty":"presence_penalty","word":"word","chapter":"chapter","lcsh":"lcsh","completion_tokens":"completion_tokens","prompt_tokens":"prompt_tokens","response_created":"response_created","abbreviation":"abbreviation","language":"language","max_tokens":"max_tokens","book":"book","temperature":"temperature","verse":"verse","top_p":"top_p","selected_word":"selected_word","model":"model"}}';
			$open_ai_response->router = 'GetbibleHelperRoute::getOpen_ai_responseRoute';
			$open_ai_response->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/open_ai_response.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","total_tokens","n","chapter","completion_tokens","prompt_tokens","max_tokens","book"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}';

			// Check if open_ai_response type is already in content_type DB.
			$open_ai_response_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($open_ai_response->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$open_ai_response->type_id = $db->loadResult();
				$open_ai_response_Updated = $db->updateObject('#__content_types', $open_ai_response, 'type_id');
			}
			else
			{
				$open_ai_response_Inserted = $db->insertObject('#__content_types', $open_ai_response);
			}

			// Create the open_ai_message content type object.
			$open_ai_message = new stdClass();
			$open_ai_message->type_title = 'Getbible Open_ai_message';
			$open_ai_message->type_alias = 'com_getbible.open_ai_message';
			$open_ai_message->table = '{"special": {"dbtable": "#__getbible_open_ai_message","key": "id","type": "Open_ai_message","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$open_ai_message->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "role","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"role":"role","open_ai_response":"open_ai_response","prompt":"prompt","source":"source","content":"content","name":"name","index":"index"}}';
			$open_ai_message->router = 'GetbibleHelperRoute::getOpen_ai_messageRoute';
			$open_ai_message->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/open_ai_message.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","source","index"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "open_ai_response","targetTable": "#__getbible_open_ai_response","targetColumn": "response_id","displayColumn": "response_id"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"}]}';

			// Check if open_ai_message type is already in content_type DB.
			$open_ai_message_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($open_ai_message->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$open_ai_message->type_id = $db->loadResult();
				$open_ai_message_Updated = $db->updateObject('#__content_types', $open_ai_message, 'type_id');
			}
			else
			{
				$open_ai_message_Inserted = $db->insertObject('#__content_types', $open_ai_message);
			}

			// Create the tag content type object.
			$tag = new stdClass();
			$tag->type_title = 'Getbible Tag';
			$tag->type_alias = 'com_getbible.tag';
			$tag->table = '{"special": {"dbtable": "#__getbible_tag","key": "id","type": "Tag","prefix": "getbibleTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$tag->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","linker":"linker","guid":"guid","description":"description"}}';
			$tag->router = 'GetbibleHelperRoute::getTagRoute';
			$tag->content_history_options = '{"formFile": "administrator/components/com_getbible/models/forms/tag.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","access"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}';

			// Check if tag type is already in content_type DB.
			$tag_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tag->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tag->type_id = $db->loadResult();
				$tag_Updated = $db->updateObject('#__content_types', $tag, 'type_id');
			}
			else
			{
				$tag_Inserted = $db->insertObject('#__content_types', $tag);
			}


			echo '<a target="_blank" href="https://getbible.net" title="Get Bible">
				<img src="components/com_getbible/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 2.0.24 Was Successful! Let us know if anything is not working as expected.</h3>';

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the getbible action logs extensions object.
			$getbible_action_logs_extensions = new stdClass();
			$getbible_action_logs_extensions->extension = 'com_getbible';

			// Check if getbible action log extension is already in action logs extensions DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_logs_extensions'));
			$query->where($db->quoteName('extension') . ' LIKE '. $db->quote($getbible_action_logs_extensions->extension));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the action logs extensions table if not found.
			if (!$db->getNumRows())
			{
				$getbible_action_logs_extensions_Inserted = $db->insertObject('#__action_logs_extensions', $getbible_action_logs_extensions);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the linker action log config object.
			$linker_action_log_config = new stdClass();
			$linker_action_log_config->id = null;
			$linker_action_log_config->type_title = 'LINKER';
			$linker_action_log_config->type_alias = 'com_getbible.linker';
			$linker_action_log_config->id_holder = 'id';
			$linker_action_log_config->title_holder = 'name';
			$linker_action_log_config->table_name = '#__getbible_linker';
			$linker_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if linker action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($linker_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$linker_action_log_config->id = $db->loadResult();
				$linker_action_log_config_Updated = $db->updateObject('#__action_log_config', $linker_action_log_config, 'id');
			}
			else
			{
				$linker_action_log_config_Inserted = $db->insertObject('#__action_log_config', $linker_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the note action log config object.
			$note_action_log_config = new stdClass();
			$note_action_log_config->id = null;
			$note_action_log_config->type_title = 'NOTE';
			$note_action_log_config->type_alias = 'com_getbible.note';
			$note_action_log_config->id_holder = 'id';
			$note_action_log_config->title_holder = 'book_nr';
			$note_action_log_config->table_name = '#__getbible_note';
			$note_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if note action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($note_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$note_action_log_config->id = $db->loadResult();
				$note_action_log_config_Updated = $db->updateObject('#__action_log_config', $note_action_log_config, 'id');
			}
			else
			{
				$note_action_log_config_Inserted = $db->insertObject('#__action_log_config', $note_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the tagged_verse action log config object.
			$tagged_verse_action_log_config = new stdClass();
			$tagged_verse_action_log_config->id = null;
			$tagged_verse_action_log_config->type_title = 'TAGGED_VERSE';
			$tagged_verse_action_log_config->type_alias = 'com_getbible.tagged_verse';
			$tagged_verse_action_log_config->id_holder = 'id';
			$tagged_verse_action_log_config->title_holder = 'book_nr';
			$tagged_verse_action_log_config->table_name = '#__getbible_tagged_verse';
			$tagged_verse_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if tagged_verse action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tagged_verse_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tagged_verse_action_log_config->id = $db->loadResult();
				$tagged_verse_action_log_config_Updated = $db->updateObject('#__action_log_config', $tagged_verse_action_log_config, 'id');
			}
			else
			{
				$tagged_verse_action_log_config_Inserted = $db->insertObject('#__action_log_config', $tagged_verse_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the prompt action log config object.
			$prompt_action_log_config = new stdClass();
			$prompt_action_log_config->id = null;
			$prompt_action_log_config->type_title = 'PROMPT';
			$prompt_action_log_config->type_alias = 'com_getbible.prompt';
			$prompt_action_log_config->id_holder = 'id';
			$prompt_action_log_config->title_holder = 'name';
			$prompt_action_log_config->table_name = '#__getbible_prompt';
			$prompt_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if prompt action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($prompt_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$prompt_action_log_config->id = $db->loadResult();
				$prompt_action_log_config_Updated = $db->updateObject('#__action_log_config', $prompt_action_log_config, 'id');
			}
			else
			{
				$prompt_action_log_config_Inserted = $db->insertObject('#__action_log_config', $prompt_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the open_ai_response action log config object.
			$open_ai_response_action_log_config = new stdClass();
			$open_ai_response_action_log_config->id = null;
			$open_ai_response_action_log_config->type_title = 'OPEN_AI_RESPONSE';
			$open_ai_response_action_log_config->type_alias = 'com_getbible.open_ai_response';
			$open_ai_response_action_log_config->id_holder = 'id';
			$open_ai_response_action_log_config->title_holder = 'response_id';
			$open_ai_response_action_log_config->table_name = '#__getbible_open_ai_response';
			$open_ai_response_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if open_ai_response action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($open_ai_response_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$open_ai_response_action_log_config->id = $db->loadResult();
				$open_ai_response_action_log_config_Updated = $db->updateObject('#__action_log_config', $open_ai_response_action_log_config, 'id');
			}
			else
			{
				$open_ai_response_action_log_config_Inserted = $db->insertObject('#__action_log_config', $open_ai_response_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the open_ai_message action log config object.
			$open_ai_message_action_log_config = new stdClass();
			$open_ai_message_action_log_config->id = null;
			$open_ai_message_action_log_config->type_title = 'OPEN_AI_MESSAGE';
			$open_ai_message_action_log_config->type_alias = 'com_getbible.open_ai_message';
			$open_ai_message_action_log_config->id_holder = 'id';
			$open_ai_message_action_log_config->title_holder = 'role';
			$open_ai_message_action_log_config->table_name = '#__getbible_open_ai_message';
			$open_ai_message_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if open_ai_message action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($open_ai_message_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$open_ai_message_action_log_config->id = $db->loadResult();
				$open_ai_message_action_log_config_Updated = $db->updateObject('#__action_log_config', $open_ai_message_action_log_config, 'id');
			}
			else
			{
				$open_ai_message_action_log_config_Inserted = $db->insertObject('#__action_log_config', $open_ai_message_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the password action log config object.
			$password_action_log_config = new stdClass();
			$password_action_log_config->id = null;
			$password_action_log_config->type_title = 'PASSWORD';
			$password_action_log_config->type_alias = 'com_getbible.password';
			$password_action_log_config->id_holder = 'id';
			$password_action_log_config->title_holder = 'name';
			$password_action_log_config->table_name = '#__getbible_password';
			$password_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if password action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($password_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$password_action_log_config->id = $db->loadResult();
				$password_action_log_config_Updated = $db->updateObject('#__action_log_config', $password_action_log_config, 'id');
			}
			else
			{
				$password_action_log_config_Inserted = $db->insertObject('#__action_log_config', $password_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the tag action log config object.
			$tag_action_log_config = new stdClass();
			$tag_action_log_config->id = null;
			$tag_action_log_config->type_title = 'TAG';
			$tag_action_log_config->type_alias = 'com_getbible.tag';
			$tag_action_log_config->id_holder = 'id';
			$tag_action_log_config->title_holder = 'name';
			$tag_action_log_config->table_name = '#__getbible_tag';
			$tag_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if tag action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($tag_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$tag_action_log_config->id = $db->loadResult();
				$tag_action_log_config_Updated = $db->updateObject('#__action_log_config', $tag_action_log_config, 'id');
			}
			else
			{
				$tag_action_log_config_Inserted = $db->insertObject('#__action_log_config', $tag_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the translation action log config object.
			$translation_action_log_config = new stdClass();
			$translation_action_log_config->id = null;
			$translation_action_log_config->type_title = 'TRANSLATION';
			$translation_action_log_config->type_alias = 'com_getbible.translation';
			$translation_action_log_config->id_holder = 'id';
			$translation_action_log_config->title_holder = 'translation';
			$translation_action_log_config->table_name = '#__getbible_translation';
			$translation_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if translation action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($translation_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$translation_action_log_config->id = $db->loadResult();
				$translation_action_log_config_Updated = $db->updateObject('#__action_log_config', $translation_action_log_config, 'id');
			}
			else
			{
				$translation_action_log_config_Inserted = $db->insertObject('#__action_log_config', $translation_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the book action log config object.
			$book_action_log_config = new stdClass();
			$book_action_log_config->id = null;
			$book_action_log_config->type_title = 'BOOK';
			$book_action_log_config->type_alias = 'com_getbible.book';
			$book_action_log_config->id_holder = 'id';
			$book_action_log_config->title_holder = 'name';
			$book_action_log_config->table_name = '#__getbible_book';
			$book_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if book action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($book_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$book_action_log_config->id = $db->loadResult();
				$book_action_log_config_Updated = $db->updateObject('#__action_log_config', $book_action_log_config, 'id');
			}
			else
			{
				$book_action_log_config_Inserted = $db->insertObject('#__action_log_config', $book_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the chapter action log config object.
			$chapter_action_log_config = new stdClass();
			$chapter_action_log_config->id = null;
			$chapter_action_log_config->type_title = 'CHAPTER';
			$chapter_action_log_config->type_alias = 'com_getbible.chapter';
			$chapter_action_log_config->id_holder = 'id';
			$chapter_action_log_config->title_holder = 'name';
			$chapter_action_log_config->table_name = '#__getbible_chapter';
			$chapter_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if chapter action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($chapter_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$chapter_action_log_config->id = $db->loadResult();
				$chapter_action_log_config_Updated = $db->updateObject('#__action_log_config', $chapter_action_log_config, 'id');
			}
			else
			{
				$chapter_action_log_config_Inserted = $db->insertObject('#__action_log_config', $chapter_action_log_config);
			}

			// Set db if not set already.
			if (!isset($db))
			{
				$db = JFactory::getDbo();
			}
			// Create the verse action log config object.
			$verse_action_log_config = new stdClass();
			$verse_action_log_config->id = null;
			$verse_action_log_config->type_title = 'VERSE';
			$verse_action_log_config->type_alias = 'com_getbible.verse';
			$verse_action_log_config->id_holder = 'id';
			$verse_action_log_config->title_holder = 'book_nr';
			$verse_action_log_config->table_name = '#__getbible_verse';
			$verse_action_log_config->text_prefix = 'COM_GETBIBLE';

			// Check if verse action log config is already in action_log_config DB.
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('id')));
			$query->from($db->quoteName('#__action_log_config'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($verse_action_log_config->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$verse_action_log_config->id = $db->loadResult();
				$verse_action_log_config_Updated = $db->updateObject('#__action_log_config', $verse_action_log_config, 'id');
			}
			else
			{
				$verse_action_log_config_Inserted = $db->insertObject('#__action_log_config', $verse_action_log_config);
			}
		}
		return true;
	}

	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (Folder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					Folder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					File::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return Folder::delete($dir);
			}
			return true;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input	array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	protected function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = Folder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!Folder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
