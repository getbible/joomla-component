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
use VDM\Joomla\Utilities\GuidHelper;
use VDM\Joomla\Utilities\GetHelper;

/**
 * Getbible Prompt Admin Model
 */
class GetbibleModelPrompt extends AdminModel
{
	/**
	 * The tab layout fields array.
	 *
	 * @var      array
	 */
	protected $tabLayoutFields = array(
		'prompt' => array(
			'left' => array(
				'integration',
				'integration_note',
				'cache_behaviour',
				'cache_persistently_expansive_caching_note',
				'cache_basic_note',
				'cache_advance_note'
			),
			'right' => array(
				'cache_capacity',
				'cache_capacity_note',
				'response_retrieval',
				'response_retrieval_note'
			),
			'fullwidth' => array(
				'openai_prompts_placeholders_basic_caching_note',
				'openai_prompts_placeholders_advanced_caching_note',
				'openai_prompts_placeholders_none_caching_note',
				'messages'
			),
			'above' => array(
				'name',
				'abbreviation'
			)
		),
		'open_ai' => array(
			'left' => array(
				'max_tokens_override',
				'max_tokens',
				'max_tokens_note',
				'temperature_override',
				'temperature',
				'temperature_note',
				'top_p_override',
				'top_p',
				'top_p_note',
				'n_override',
				'n',
				'n_note'
			),
			'right' => array(
				'token_override',
				'token',
				'ai_org_token_override',
				'org_token',
				'model',
				'openai_documentation_note',
				'presence_penalty_override',
				'presence_penalty',
				'presence_penalty_note',
				'frequency_penalty_override',
				'frequency_penalty',
				'frequency_penalty_note'
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
	public $typeAlias = 'com_getbible.prompt';

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
	public function getTable($type = 'prompt', $prefix = 'GetbibleTable', $config = array())
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

			if (!empty($item->messages))
			{
				// Convert the messages field to an array.
				$messages = new Registry;
				$messages->loadString($item->messages);
				$item->messages = $messages->toArray();
			}
		}

		return $item;
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
		$form = $this->loadForm('com_getbible.prompt', 'prompt', $options, $clear, $xpath);

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
		if ($id != 0 && (!$user->authorise('prompt.edit.state', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.state', 'com_getbible')))
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
		if ($id != 0 && (!$user->authorise('prompt.edit.created_by', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.created_by', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'readonly', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}
		// Modify the form based on Edit Creaded Date access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.created', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.created', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created', 'filter', 'unset');
		}
		// Modify the form based on Edit Name access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.name', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.name', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('name', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('name', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('name'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('name', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('name', 'required', 'false');
			}
		}
		// Modify the form based on Edit Integration access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.integration', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.integration', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('integration', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('integration', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('integration'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('integration', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('integration', 'required', 'false');
			}
		}
		// Modify the form based on Edit Cache Behaviour access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.cache_behaviour', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.cache_behaviour', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('cache_behaviour', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('cache_behaviour', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('cache_behaviour'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('cache_behaviour', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('cache_behaviour', 'required', 'false');
			}
		}
		// Modify the form based on Edit Abbreviation access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.abbreviation', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.abbreviation', 'com_getbible')))
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
		// Modify the form based on Edit Guid access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.guid', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.guid', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('guid', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('guid', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('guid'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('guid', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('guid', 'required', 'false');
			}
		}
		// Modify the form based on Edit Model access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.model', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.model', 'com_getbible')))
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
		// Modify the form based on Edit Presence Penalty access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.presence_penalty', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.presence_penalty', 'com_getbible')))
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
		// Modify the form based on Edit Org Token access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.org_token', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.org_token', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('org_token', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('org_token', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('org_token'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('org_token', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('org_token', 'required', 'false');
			}
		}
		// Modify the form based on Edit Token access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.token', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.token', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('token', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('token', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('token'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('token', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('token', 'required', 'false');
			}
		}
		// Modify the form based on Edit N Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.n_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.n_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('n_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('n_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('n_override', 'class', '');
			$form->setFieldAttribute('n_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('n_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('n_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('n_override', 'required', 'false');
			}
		}
		// Modify the form based on Edit Messages access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.messages', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.messages', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('messages', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('messages', 'readonly', 'true');
			// If there is no value continue.
			if (!$form->getValue('messages'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('messages', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('messages', 'required', 'false');
			}
		}
		// Modify the form based on Edit Response Retrieval access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.response_retrieval', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.response_retrieval', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('response_retrieval', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('response_retrieval', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('response_retrieval', 'class', '');
			$form->setFieldAttribute('response_retrieval', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('response_retrieval'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('response_retrieval', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('response_retrieval', 'required', 'false');
			}
		}
		// Modify the form based on Edit Frequency Penalty Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.frequency_penalty_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.frequency_penalty_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('frequency_penalty_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('frequency_penalty_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('frequency_penalty_override', 'class', '');
			$form->setFieldAttribute('frequency_penalty_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('frequency_penalty_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('frequency_penalty_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('frequency_penalty_override', 'required', 'false');
			}
		}
		// Modify the form based on Edit N access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.n', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.n', 'com_getbible')))
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
		// Modify the form based on Edit Max Tokens Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.max_tokens_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.max_tokens_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('max_tokens_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('max_tokens_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('max_tokens_override', 'class', '');
			$form->setFieldAttribute('max_tokens_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('max_tokens_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('max_tokens_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('max_tokens_override', 'required', 'false');
			}
		}
		// Modify the form based on Edit Token Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.token_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.token_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('token_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('token_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('token_override', 'class', '');
			$form->setFieldAttribute('token_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('token_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('token_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('token_override', 'required', 'false');
			}
		}
		// Modify the form based on Edit Max Tokens access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.max_tokens', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.max_tokens', 'com_getbible')))
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
		// Modify the form based on Edit Ai Org Token Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.ai_org_token_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.ai_org_token_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ai_org_token_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('ai_org_token_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('ai_org_token_override', 'class', '');
			$form->setFieldAttribute('ai_org_token_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('ai_org_token_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('ai_org_token_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('ai_org_token_override', 'required', 'false');
			}
		}
		// Modify the from the form based on Temperature Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.access.temperature_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.access.temperature_override', 'com_getbible')))
		{
			// Remove the field
			$form->removeField('temperature_override');
		}
		// Modify the form based on Edit Presence Penalty Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.presence_penalty_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.presence_penalty_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('presence_penalty_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('presence_penalty_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('presence_penalty_override', 'class', '');
			$form->setFieldAttribute('presence_penalty_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('presence_penalty_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('presence_penalty_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('presence_penalty_override', 'required', 'false');
			}
		}
		// Modify the form based on Edit Top P Override access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.top_p_override', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.top_p_override', 'com_getbible')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('top_p_override', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('top_p_override', 'readonly', 'true');
			// Disable radio button for display.
			$class = $form->getFieldAttribute('top_p_override', 'class', '');
			$form->setFieldAttribute('top_p_override', 'class', $class.' disabled no-click');
			// If there is no value continue.
			if (!$form->getValue('top_p_override'))
			{
				// Disable fields while saving.
				$form->setFieldAttribute('top_p_override', 'filter', 'unset');
				// Disable fields while saving.
				$form->setFieldAttribute('top_p_override', 'required', 'false');
			}
		}
		// Modify the form based on Edit Frequency Penalty access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.frequency_penalty', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.frequency_penalty', 'com_getbible')))
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
		// Modify the form based on Edit Top P access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.top_p', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.top_p', 'com_getbible')))
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
		// Modify the form based on Edit Temperature access controls.
		if ($id != 0 && (!$user->authorise('prompt.edit.temperature', 'com_getbible.prompt.' . (int) $id))
			|| ($id == 0 && !$user->authorise('prompt.edit.temperature', 'com_getbible')))
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

		// Only load the GUID if new item (or empty)
		if (0 == $id || !($val = $form->getValue('guid')))
		{
			$form->setValue('guid', null, GuidHelper::get());
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
		return 'media/com_getbible/js/prompt.js';
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
			return $user->authorise('prompt.delete', 'com_getbible.prompt.' . (int) $record->id);
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
			$permission = $user->authorise('prompt.edit.state', 'com_getbible.prompt.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absense of better information, revert to the component permissions.
		return $user->authorise('prompt.edit.state', 'com_getbible');
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

		return $user->authorise('prompt.edit', 'com_getbible.prompt.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('prompt.edit',  'com_getbible');
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
					->from($db->quoteName('#__getbible_prompt'));
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
		$data = JFactory::getApplication()->getUserState('com_getbible.edit.prompt.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
			// run the perprocess of the data
			$this->preprocessData('com_getbible.prompt', $data);
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
		return array('guid');
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
		$this->canDo			= GetbibleHelper::getActions('prompt');
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
			$this->canDo		= GetbibleHelper::getActions('prompt');
		}

		if (!$this->canDo->get('prompt.create') && !$this->canDo->get('prompt.batch'))
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
		elseif (isset($values['published']) && !$this->canDo->get('prompt.edit.state'))
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
			if (!$this->user->authorise('prompt.edit', $contexts[$pk]))
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
			if (GetbibleHelper::checkString($this->table->name) && !is_numeric($this->table->name))
			{
				$this->table->name = $this->generateUnique('name',$this->table->name);
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
			$this->canDo		= GetbibleHelper::getActions('prompt');
		}

		if (!$this->canDo->get('prompt.edit') && !$this->canDo->get('prompt.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// make sure published only updates if user has the permission.
		if (isset($values['published']) && !$this->canDo->get('prompt.edit.state'))
		{
			unset($values['published']);
		}
		// remove move_copy from array
		unset($values['move_copy']);

		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('prompt.edit', $contexts[$pk]))
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


		// Set the GUID if empty or not valid
		if (empty($data['guid']) && $data['id'] > 0)
		{
			// get the existing one
			$data['guid'] = (string) GetHelper::var('prompt', $data['id'], 'id', 'guid');
		}

		// Set the GUID if empty or not valid
		while (!GuidHelper::valid($data['guid'], "prompt", $data['id']))
		{
			// must always be set
			$data['guid'] = (string) GuidHelper::get();
		}

		// Set the messages items to data.
		if (isset($data['messages']) && is_array($data['messages']))
		{
			$messages = new JRegistry;
			$messages->loadArray($data['messages']);
			$data['messages'] = (string) $messages;
		}
		// Also check permission since the value may be removed due to permissions
		// Then we do not want to clear it out, but simple ignore the empty messages
		elseif (!isset($data['messages'])
			&& JFactory::getUser()->authorise('prompt.edit.messages', 'com_getbible'))
		{
			// Set the empty messages to data
			$data['messages'] = '';
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