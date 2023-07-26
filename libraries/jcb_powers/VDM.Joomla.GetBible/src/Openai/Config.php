<?php
/**
 * @package    GetBible
 *
 * @created    30th May, 2023
 * @author     Llewellyn van der Merwe <https://dev.vdm.io>
 * @git        GetBible <https://git.vdm.dev/getBible>
 * @copyright  Copyright (C) 2015 Vast Development Method. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace VDM\Joomla\GetBible\Openai;


use Joomla\CMS\Factory as JoomlaFactory;
use Joomla\Registry\Registry as JoomlaRegistry;
use Joomla\Input\Input;
use VDM\Joomla\Utilities\Component\Helper;
use VDM\Joomla\Abstraction\BaseConfig;


/**
 * GetBible Openai Configurations
 * 
 * @since 2.0.1
 */
class Config extends BaseConfig
{
	/**
	 * Hold a JInput object for easier access to the input variables.
	 *
	 * @var    Input
	 * @since 3.2.0
	 */
	protected Input $input;

	/**
	 * The Params
	 *
	 * @var     JoomlaRegistry
	 * @since 3.2.0
	 */
	protected JoomlaRegistry $params;

	/**
	 * Constructor
	 *
	 * @param Input|null           $input   Input
	 * @param JoomlaRegistry|null  $params  The component parameters
	 *
	 * @throws \Exception
	 * @since 3.2.0
	 */
	public function __construct(?Input $input = null, ?JoomlaRegistry $params = null)
	{
		$this->input = $input ?: JoomlaFactory::getApplication()->input;
		$this->params = $params ?: Helper::getParams('com_getbible');

		// run parent constructor
		parent::__construct();
	}

	/**
	 * get Prompt GUID
	 *
	 * @return  string|null  The translation abbreviation 
	 * @since 2.0.1
	 */
	protected function getPrompt(): ?string
	{
		return $this->input->getString('guid');
	}

	/**
	 * get Translation Abbreviation
	 *
	 * @return  string|null  The translation abbreviation 
	 * @since 2.0.1
	 */
	protected function getTranslation(): ?string
	{
		return $this->input->getString('t') ?? $this->input->getString('version') ?? $this->input->getString('translation');
	}

	/**
	 * get Book Number
	 *
	 * @return  int|null  The book number
	 * @since 2.0.1
	 */
	protected function getBook(): ?int
	{
		return $this->input->getInt('book');
	}

	/**
	 * get Chapter Number
	 *
	 * @return  int|null  The chapter number
	 * @since 2.0.1
	 */
	protected function getChapter(): ?int
	{
		return $this->input->getInt('chapter');
	}

	/**
	 * get Verse Number/s
	 *
	 * @return  string|null  The verse number/s
	 * @since 2.0.1
	 */
	protected function getVerse(): ?string
	{
		return $this->input->getString('verse');
	}

	/**
	 * get Words Number/s
	 *
	 * @return  string|null  The word number/s
	 * @since 2.0.1
	 */
	protected function getWords(): ?string
	{
		return $this->input->getString('words');
	}

	/**
	 * get Enable Open AI
	 *
	 * @return  bool   The switch to enable open AI
	 * @since 2.0.1
	 */
	protected function getEnableopenai(): bool
	{
		if ($this->params->get('enable_open_ai', 0) == 1)
		{
			return true;
		}

		return false;
	}

	/**
	 * get Enable Open AI Organisation
	 *
	 * @return  bool   The switch to enable open AI Organisation
	 * @since 2.0.1
	 */
	protected function getEnableopenaiorg(): bool
	{
		if ($this->params->get('enable_open_ai_org', 0) == 1)
		{
			return true;
		}

		return false;
	}

	/**
	 * get User Token
	 *
	 * @return  string|null   The token
	 * @since 2.0.1
	 */
	protected function getToken(): ?string
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return $this->params->get('openai_token');
	}

	/**
	 * get Org Token
	 *
	 * @return  string|null   The org token
	 * @since 2.0.1
	 */
	protected function getOrgToken(): ?string
	{
		if (!$this->enable_open_ai || !$this->enable_open_ai_org)
		{
			return null;
		}

		return $this->params->get('openai_org_token');
	}

	/**
	 * get Open AI Model name
	 *
	 * @return  string|null   The model name
	 * @since 2.0.1
	 */
	protected function getModel(): ?string
	{
		if (!$this->enable_open_ai)
		{die;
			return null;
		}

		return $this->params->get('openai_model');
	}

	/**
	 * get Max Tokens
	 *
	 * @return  int|null   The max tokens
	 * @since 2.0.1
	 */
	protected function getMaxtokens(): ?int
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return (int) $this->params->get('openai_max_tokens', 300);
	}

	/**
	 * get Temperature
	 *
	 * @return  float|null   The temperature
	 * @since 2.0.1
	 */
	protected function getTemperature(): ?float
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return (float) $this->params->get('openai_temperature', 1);
	}

	/**
	 * get Top P
	 *
	 * @return  float|null   The top p
	 * @since 2.0.1
	 */
	protected function getTopp(): ?float
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return (float) $this->params->get('openai_top_p', 1);
	}

	/**
	 * get N (number of results)
	 *
	 * @return  int|null   The number of results
	 * @since 2.0.1
	 */
	protected function getN(): ?int
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return (int) $this->params->get('openai_n', 1);
	}

	/**
	 * get Presence Penalty
	 *
	 * @return  float|null   The presence penalty
	 * @since 2.0.1
	 */
	protected function getPresencepenalty(): ?float
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return (float) $this->params->get('openai_presence_penalty', 0);
	}

	/**
	 * get Frequency Penalty
	 *
	 * @return  float|null   The frequency penalty
	 * @since 2.0.1
	 */
	protected function getFrequencypenalty(): ?float
	{
		if (!$this->enable_open_ai)
		{
			return null;
		}

		return (float) $this->params->get('openai_frequency_penalty', 0);
	}
}

