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

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * Getbible Open_ai_response Admin Model
 */
class GetbibleModelOpen_ai_response extends AdminModel
{
	/**
	 * The tab layout fields array.
	 *
	 * @var      array
	 */
	protected $tabLayoutFields = array(
		'details' => array(
			'left' => array(
				'response_object',
				'response_model',
				'response_created'
			),
			'right' => array(
				'prompt_tokens',
				'completion_tokens',
				'total_tokens'
			),
			'above' => array(
				'response_id',
				'prompt'
			)
		),
		'prompt' => array(
			'left' => array(
				'max_tokens',
				'temperature',
				'top_p'
			),
			'right' => array(
				'model',
				'presence_penalty',
				'frequency_penalty',
				'n'
			)
		),
		'bible' => array(
			'left' => array(
				'abbreviation',
				'language',
				'lcsh',
				'book'
			),
			'right' => array(
				'chapter',
				'verse',
				'word',
				'selected_word'
			)
		)
	);

	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_GETBIBLE';

	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_getbible.open_ai_response';

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'open_ai_response', $prefix = 'GetbibleTable', $config = array())
	{
		// add table path for when model gets used from other component
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_getbible/tables');
		// get instance of the table
		return JTable::getInstance($type, $prefix, $config);
	}
    
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			if (!empty($item->params) && !is_array($item->params))
			{
				// Convert the params field to an array.
				$registry = new Registry;
				$registry->loadString($item->params);
				$item->params = $registry->toArray();
			}

			if (!empty($item->metadata))
			{
				// Convert the metadata field to an array.
				$registry = new Registry;
				$registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();
			}
		}
		$this->open_ai_responsevvvy = $item->response_id;

		return $item;
	}

	/**
	 * Method to get list data.
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getVvymessage()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the getbible_open_ai_message table
		$query->from($db->quoteName('#__getbible_open_ai_message', 'a'));

		// From the getbible_open_ai_response table.
		$query->select($db->quoteName(['g.response_id','g.id'],['open_ai_response_response_id','open_ai_response_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_open_ai_response', 'g') . ' ON (' . $db->quoteName('a.open_ai_response') . ' = ' . $db->quoteName('g.response_id') . ')');

		// From the getbible_prompt table.
		$query->select($db->quoteName(['h.name','h.id'],['prompt_name','prompt_id']));
		$query->join('LEFT', $db->quoteName('#__getbible_prompt', 'h') . ' ON (' . $db->quoteName('a.prompt') . ' = ' . $db->quoteName('h.guid') . ')');

		// Filter by open_ai_responsevvvy global.
		$open_ai_responsevvvy = $this->open_ai_responsevvvy;
		if (is_numeric($open_ai_responsevvvy ))
		{
			$query->where('a.open_ai_response = ' . (int) $open_ai_responsevvvy );
		}
		elseif (is_string($open_ai_responsevvvy))
		{
			$query->where('a.open_ai_response = ' . $db->quote($open_ai_responsevvvy));
		}
		else
		{
			$query->where('a.open_ai_response = -5');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (GetbibleHelper::checkArray($_access))
		{
			// Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_getbible'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Order the results by ordering
		$query->order('a.published  ASC');
		$query->order('a.ordering  ASC');

		// Load the items
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			$items = $db->loadObjectList();

			// Set values to display correctly.
			if (GetbibleHelper::checkArray($items))
			{
				// Get the user object if not set.
				if (!isset($user) || !GetbibleHelper::checkObject($user))
				{
					$user = JFactory::getUser();
				}
				foreach ($items as $nr => &$item)
				{
					// Remove items the user can't access.
					$access = ($user->authorise('open_ai_message.access', 'com_getbible.open_ai_message.' . (int) $item->id) && $user->authorise('open_ai_message.access', 'com_getbible'));
					if (!$access)
					{
						unset($items[$nr]);
						continue;
					}

				}
			}

			// set selection value to a translatable value
			if (GetbibleHelper::checkArray($items))
			{
				foreach ($items as $nr => &$item)
				{
					// convert role
					$item->role = $this->selectionTranslationVvymessage($item->role, 'role');
					// convert source
					$item->source = $this->selectionTranslationVvymessage($item->source, 'source');
				}
			}

			return $items;
		}
		return false;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslationVvymessage($value,$name)
	{
		// Array of role language strings
		if ($name === 'role')
		{
			$roleArray = array(
				'system' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_SYSTEM',
				'user' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_USER',
				'assistant' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_ASSISTANT',
				'function' => 'COM_GETBIBLE_OPEN_AI_MESSAGE_FUNCTION'
			);
			// Now check if value is found in this array
			if (isset($roleArray[$value]) && GetbibleHelper::checkString($roleArray[$value]))
			{
				return $roleArray[$value];
			}
		}
		// Array of source language strings
		if ($name === 'source')
		{
			$sourceArray = array(
				1 => 'COM_GETBIBLE_OPEN_AI_MESSAGE_PROMPT',
				2 => 'COM_GETBIBLE_OPEN_AI_MESSAGE_OPEN_AI'
			);
			// Now check if value is found in this array
			if (isset($sourceArray[$value]) && GetbibleHelper::checkString($sourceArray[$value]))
			{
				return $sourceArray[$value];
			}
		}
		return $value;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @param   array    $options   Optional array of options for the form creation.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true, $options = array('control' => 'jform'))
	{
		// set load data option
		$options['load_data'] = $loadData;
		// check if xpath was set in options
		$xpath = false;
		if (isset($options['xpath']))
		{
			$xpath = $options['xpath'];
			unset($options['xpath']);
		}
		// check if clear form was set in options
		$clear = false;
		if (isset($options['clear']))
		{
			$clear = $options['clear'];
			unset($options['clear']);
		}

		// Get the form.
		$form = $this->loadForm('com_getbible.open_ai_response', 'open_ai_response', $options, $clear, $xpath);

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0, 'INT');
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0, 'INT');
		}

		$user = JFactory::getUser();

		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.state', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.state', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		// If this is a new item insure the greated by is set.
		if (0 == $id)
		{
			// Set the created_by to this user
			$form->setValue('created_by', null, $user->id);
		}
		// Modify the form based on Edit Creaded By access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.created_by', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.created_by', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'readonly', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}
		// Modify the form based on Edit Creaded Date access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.created', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.created', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created', 'filter', 'unset');
		}
		// Modify the form based on Edit Response Id access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.response_id', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.response_id', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('response_id', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('response_id', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('response_id'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('response_id', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('response_id', 'required', 'false');
			}
		}
		// Modify the form based on Edit Prompt access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.prompt', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.prompt', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('prompt', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('prompt', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('prompt'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('prompt', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('prompt', 'required', 'false');
			}
		}
		// Modify the form based on Edit Response Object access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.response_object', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.response_object', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('response_object', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('response_object', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('response_object'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('response_object', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('response_object', 'required', 'false');
			}
		}
		// Modify the form based on Edit Response Model access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.response_model', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.response_model', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('response_model', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('response_model', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('response_model'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('response_model', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('response_model', 'required', 'false');
			}
		}
		// Modify the form based on Edit Total Tokens access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.total_tokens', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.total_tokens', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('total_tokens', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('total_tokens', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('total_tokens'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('total_tokens', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('total_tokens', 'required', 'false');
			}
		}
		// Modify the form based on Edit N access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.n', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.n', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('n', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('n', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('n'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('n', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('n', 'required', 'false');
			}
		}
		// Modify the form based on Edit Frequency Penalty access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.frequency_penalty', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.frequency_penalty', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('frequency_penalty', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('frequency_penalty', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('frequency_penalty'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('frequency_penalty', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('frequency_penalty', 'required', 'false');
			}
		}
		// Modify the form based on Edit Presence Penalty access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.presence_penalty', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.presence_penalty', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('presence_penalty', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('presence_penalty', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('presence_penalty'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('presence_penalty', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('presence_penalty', 'required', 'false');
			}
		}
		// Modify the form based on Edit Word access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.word', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.word', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('word', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('word', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('word'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('word', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('word', 'required', 'false');
			}
		}
		// Modify the form based on Edit Chapter access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.chapter', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.chapter', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('chapter', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('chapter', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('chapter'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('chapter', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('chapter', 'required', 'false');
			}
		}
		// Modify the form based on Edit Lcsh access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.lcsh', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.lcsh', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('lcsh', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('lcsh', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('lcsh'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('lcsh', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('lcsh', 'required', 'false');
			}
		}
		// Modify the form based on Edit Completion Tokens access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.completion_tokens', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.completion_tokens', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('completion_tokens', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('completion_tokens', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('completion_tokens'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('completion_tokens', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('completion_tokens', 'required', 'false');
			}
		}
		// Modify the form based on Edit Prompt Tokens access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.prompt_tokens', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.prompt_tokens', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('prompt_tokens', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('prompt_tokens', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('prompt_tokens'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('prompt_tokens', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('prompt_tokens', 'required', 'false');
			}
		}
		// Modify the form based on Edit Response Created access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.response_created', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.response_created', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('response_created', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('response_created', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('response_created'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('response_created', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('response_created', 'required', 'false');
			}
		}
		// Modify the form based on Edit Abbreviation access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.abbreviation', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.abbreviation', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('abbreviation', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('abbreviation', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('abbreviation'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('abbreviation', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('abbreviation', 'required', 'false');
			}
		}
		// Modify the form based on Edit Language access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.language', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.language', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('language', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('language', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('language'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('language', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('language', 'required', 'false');
			}
		}
		// Modify the form based on Edit Max Tokens access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.max_tokens', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.max_tokens', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('max_tokens', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('max_tokens', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('max_tokens'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('max_tokens', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('max_tokens', 'required', 'false');
			}
		}
		// Modify the form based on Edit Book access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.book', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.book', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('book', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('book', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('book'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('book', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('book', 'required', 'false');
			}
		}
		// Modify the form based on Edit Temperature access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.temperature', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.temperature', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('temperature', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('temperature', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('temperature'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('temperature', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('temperature', 'required', 'false');
			}
		}
		// Modify the form based on Edit Verse access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.verse', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.verse', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('verse', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('verse', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('verse'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('verse', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('verse', 'required', 'false');
			}
		}
		// Modify the form based on Edit Top P access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.top_p', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.top_p', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('top_p', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('top_p', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('top_p'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('top_p', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('top_p', 'required', 'false');
			}
		}
		// Modify the form based on Edit Selected Word access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.selected_word', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.selected_word', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('selected_word', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('selected_word', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('selected_word'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('selected_word', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('selected_word', 'required', 'false');
			}
		}
		// Modify the form based on Edit Model access controls.
		if ($id != 0 && (!$user->authorise('open_ai_response.edit.model', 'com_getbible.open_ai_response.' . (int) $id))
			|| ($id == 0 && !$user->authorise('open_ai_response.edit.model', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('model', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('model', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('model'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('model', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('model', 'required', 'false');
			}
		}
		// Only load these values if no id is found
		if (0 == $id)
		{
			// Set redirected view name
			$redirectedView = $jinput->get('ref', null, 'STRING');
			// Set field name (or fall back to view name)
			$redirectedField = $jinput->get('field', $redirectedView, 'STRING');
			// Set redirected view id
			$redirectedId = $jinput->get('refid', 0, 'INT');
			// Set field id (or fall back to redirected view id)
			$redirectedValue = $jinput->get('field_id', $redirectedId, 'INT');
			if (0 != $redirectedValue && $redirectedField)
			{
				// Now set the local-redirected field default value
				$form->setValue($redirectedField, null, $redirectedValue);
			}
		}
		return $form;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	script files
	 */
	public function getScript()
	{
		return 'media/com_getbible/js/open_ai_response.js';
	}
    
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}

			$user = JFactory::getUser();
			// The record has been set. Check the record permissions.
			return $user->authorise('open_ai_response.delete', 'com_getbible.open_ai_response.' . (int) $record->id);
		}
		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		$recordId = (!empty($record->id)) ? $record->id : 0;

		if ($recordId)
		{
			// The record has been set. Check the record permissions.
			$permission = $user->authorise('open_ai_response.edit.state', 'com_getbible.open_ai_response.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absence of better information, revert to the component permissions.
		return $user->authorise('open_ai_response.edit.state', 'com_getbible');
	}
    
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		$user = JFactory::getUser();

		return $user->authorise('open_ai_response.edit', 'com_getbible.open_ai_response.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('open_ai_response.edit',  'com_getbible');
	}
    
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (isset($table->name))
		{
			$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		}
		
		if (isset($table->alias) && empty($table->alias))
		{
			$table->generateAlias();
		}
		
		if (empty($table->id))
		{
			$table->created = $date->toSql();
			// set the user
			if ($table->created_by == 0 || empty($table->created_by))
			{
				$table->created_by = $user->id;
			}
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__getbible_open_ai_response'));
				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			$table->modified = $date->toSql();
			$table->modified_by = $user->id;
		}
        
		if (!empty($table->id))
		{
			// Increment the items version number.
			$table->version++;
		}
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_getbible.edit.open_ai_response.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
			// run the perprocess of the data
			$this->preprocessData('com_getbible.open_ai_response', $data);
		}

		return $data;
	}

	/**
	 * Method to get the unique fields of this table.
	 *
	 * @return  mixed  An array of field names, boolean false if none is set.
	 *
	 * @since   3.0
	 */
	protected function getUniqueFields()
	{
		return false;
	}
	
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		if (!parent::delete($pks))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.2
	 */
	public function publish(&$pks, $value = 1)
	{
		if (!parent::publish($pks, $value))
		{
			return false;
		}
		
		return true;
        }
    
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize ids.
		$pks = array_unique($pks);
		ArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		// Set some needed variables.
		$this->user			= JFactory::getUser();
		$this->table			= $this->getTable();
		$this->tableClassName		= get_class($this->table);
		$this->contentType		= new JUcmType;
		$this->type			= $this->contentType->getTypeByTable($this->tableClassName);
		$this->canDo			= GetbibleHelper::getActions('open_ai_response');
		$this->batchSet			= true;

		if (!$this->canDo->get('core.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}
        
		if ($this->type == false)
		{
			$type = new JUcmType;
			$this->type = $type->getTypeByAlias($this->typeAlias);
		}

		$this->tagsObserver = $this->table->getObserverOfClass('JTableObserverTags');

		if (!empty($commands['move_copy']))
		{
			$cmd = ArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands, $pks, $contexts);

				if (is_array($result))
				{
					foreach ($result as $old => $new)
					{
						$contexts[$new] = $contexts[$old];
					}
					$pks = array_values($result);
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands, $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $values    The new values.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since 12.2
	 */
	protected function batchCopy($values, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user 		= JFactory::getUser();
			$this->table 		= $this->getTable();
			$this->tableClassName	= get_class($this->table);
			$this->canDo		= GetbibleHelper::getActions('open_ai_response');
		}

		if (!$this->canDo->get('open_ai_response.create') && !$this->canDo->get('open_ai_response.batch'))
		{
			return false;
		}

		// get list of unique fields
		$uniqueFields = $this->getUniqueFields();
		// remove move_copy from array
		unset($values['move_copy']);

		// make sure published is set
		if (!isset($values['published']))
		{
			$values['published'] = 0;
		}
		elseif (isset($values['published']) && !$this->canDo->get('open_ai_response.edit.state'))
		{
				$values['published'] = 0;
		}

		$newIds = array();
		// Parent exists so let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// only allow copy if user may edit this item.
			if (!$this->user->authorise('open_ai_response.edit', $contexts[$pk]))
			{
				// Not fatal error
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
				continue;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Only for strings
			if (GetbibleHelper::checkString($this->table->response_id) && !is_numeric($this->table->response_id))
			{
				$this->table->response_id = $this->generateUnique('response_id',$this->table->response_id);
			}

			// insert all set values
			if (GetbibleHelper::checkArray($values))
			{
				foreach ($values as $key => $value)
				{
					if (strlen($value) > 0 && isset($this->table->$key))
					{
						$this->table->$key = $value;
					}
				}
			}

			// update all unique fields
			if (GetbibleHelper::checkArray($uniqueFields))
			{
				foreach ($uniqueFields as $uniqueField)
				{
					$this->table->$uniqueField = $this->generateUnique($uniqueField,$this->table->$uniqueField);
				}
			}

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// TODO: Deal with ordering?
			// $this->table->ordering = 1;

			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$pk] = $newId;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Batch move items to a new category
	 *
	 * @param   integer  $value     The new category ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since 12.2
	 */
	protected function batchMove($values, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user		= JFactory::getUser();
			$this->table		= $this->getTable();
			$this->tableClassName	= get_class($this->table);
			$this->canDo		= GetbibleHelper::getActions('open_ai_response');
		}

		if (!$this->canDo->get('open_ai_response.edit') && !$this->canDo->get('open_ai_response.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// make sure published only updates if user has the permission.
		if (isset($values['published']) && !$this->canDo->get('open_ai_response.edit.state'))
		{
			unset($values['published']);
		}
		// remove move_copy from array
		unset($values['move_copy']);

		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('open_ai_response.edit', $contexts[$pk]))
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// insert all set values.
			if (GetbibleHelper::checkArray($values))
			{
				foreach ($values as $key => $value)
				{
					// Do special action for access.
					if ('access' === $key && strlen($value) > 0)
					{
						$this->table->$key = $value;
					}
					elseif (strlen($value) > 0 && isset($this->table->$key))
					{
						$this->table->$key = $value;
					}
				}
			}


			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$input	= JFactory::getApplication()->input;
		$filter	= JFilterInput::getInstance();
        
		// set the metadata to the Item Data
		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
            
			$metadata = new JRegistry;
			$metadata->loadArray($data['metadata']);
			$data['metadata'] = (string) $metadata;
		}
        
		// Set the Params Items to data
		if (isset($data['params']) && is_array($data['params']))
		{
			$params = new JRegistry;
			$params->loadArray($data['params']);
			$data['params'] = (string) $params;
		}

		// Alter the unique field for save as copy
		if ($input->get('task') === 'save2copy')
		{
			// Automatic handling of other unique fields
			$uniqueFields = $this->getUniqueFields();
			if (GetbibleHelper::checkArray($uniqueFields))
			{
				foreach ($uniqueFields as $uniqueField)
				{
					$data[$uniqueField] = $this->generateUnique($uniqueField,$data[$uniqueField]);
				}
			}
		}
		
		if (parent::save($data))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Method to generate a unique value.
	 *
	 * @param   string  $field name.
	 * @param   string  $value data.
	 *
	 * @return  string  New value.
	 *
	 * @since   3.0
	 */
	protected function generateUnique($field,$value)
	{

		// set field value unique
		$table = $this->getTable();

		while ($table->load(array($field => $value)))
		{
			$value = StringHelper::increment($value);
		}

		return $value;
	}

	/**
	 * Method to change the title
	 *
	 * @param   string   $title   The title.
	 *
	 * @return	array  Contains the modified title and alias.
	 *
	 */
	protected function _generateNewTitle($title)
	{

		// Alter the title
		$table = $this->getTable();

		while ($table->load(array('title' => $title)))
		{
			$title = StringHelper::increment($title);
		}

		return $title;
	}
}
