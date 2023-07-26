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

namespace VDM\Joomla\GetBible\Data;


use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Openai\Config;


/**
 * The GetBible Prompt Data
 * 
 * @since 2.0.1
 */
final class Prompt
{
	/**
	 * The Load class
	 *
	 * @var    Load
	 * @since  2.0.1
	 */
	protected Load $load;

	/**
	 * The Config class
	 *
	 * @var    Config
	 * @since  2.0.1
	 */
	protected Config $config;

	/**
	 * The prompts
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $prompts = [];

	/**
	 * Constructor
	 *
	 * @param Load       $load      The load object.
	 * @param Config     $config      The config object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(Load $load, Config $config)
	{
		$this->load = $load;
		$this->config = $config;
	}

	/**
	 * Get prompt guid
	 *
	 * @return  string     The global unique id
	 * @since   2.0.1
	 */
	public function getGuid(): string
	{
		$prompt = $this->get();
		return $prompt ? $prompt->guid ?? '' : '';
	}

	/**
	 * Get open ai token
	 *
	 * @return  string|null    The token
	 * @since   2.0.1
	 */
	public function getToken(): ?string
	{
		$default = $this->config->get('token');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->token_override) ||
			$prompt->token_override != 1)
		{
			return $default;
		}

		return $prompt->token ?? $default;
	}

	/**
	 * Get open ai organisation token
	 *
	 * @return  string|null    The token
	 * @since   2.0.1
	 */
	public function getOrgToken(): ?string
	{
		$default = $this->config->get('org_token');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->ai_org_token_override) ||
			$prompt->ai_org_token_override != 1)
		{
			return $default;
		}

		return $prompt->org_token ?? $default;
	}

	/**
	 * Get open ai model name
	 *
	 * @return  string|null    The model name
	 * @since   2.0.1
	 */
	public function getModel(): ?string
	{
		$default = $this->config->get('model');
		$prompt = $this->get();

		return $prompt ? $prompt->model ?? $default : $default;
	}

	/**
	 * Get the max tokens
	 *
	 * @return  int|null    The max tokens
	 * @since   2.0.1
	 */
	public function getMaxTokens(): ?int
	{
		$default = $this->config->get('max_tokens');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->max_tokens_override) ||
			$prompt->max_tokens_override != 1)
		{
			return $default;
		}

		return $prompt->max_tokens ?? $default;
	}

	/**
	 * Get the temperature
	 *
	 * @return  float|null    The temperature
	 * @since   2.0.1
	 */
	public function getTemperature(): ?float
	{
		$default = $this->config->get('temperature');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->temperature_override) ||
			$prompt->temperature_override != 1)
		{
			return $default;
		}

		return $prompt->temperature ?? $default;
	}

	/**
	 * Get the top p
	 *
	 * @return  float|null    The top p
	 * @since   2.0.1
	 */
	public function getTopP(): ?float
	{
		$default = $this->config->get('top_p');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->top_p_override) ||
			$prompt->top_p_override != 1)
		{
			return $default;
		}

		return $prompt->top_p ?? $default;
	}

	/**
	 * Get the number of results
	 *
	 * @return  int|null    The number of results
	 * @since   2.0.1
	 */
	public function getN(): ?int
	{
		$default = $this->config->get('n');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->n_override) ||
			$prompt->n_override != 1)
		{
			return $default;
		}

		return $prompt->n ?? $default;
	}

	/**
	 * Get presence penalty
	 *
	 * @return  float|null    The presence penalty
	 * @since   2.0.1
	 */
	public function getPresencePenalty(): ?float
	{
		$default = $this->config->get('presence_penalty');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->presence_penalty_override) ||
			$prompt->presence_penalty_override != 1)
		{
			return $default;
		}

		return $prompt->presence_penalty ?? $default;
	}

	/**
	 * Get frequency penalty
	 *
	 * @return  float|null    The frequency penalty
	 * @since   2.0.1
	 */
	public function getFrequencyPenalty(): ?float
	{
		$default = $this->config->get('frequency_penalty');
		$prompt = $this->get();

		if (!$prompt || !isset($prompt->frequency_penalty_override) ||
			$prompt->frequency_penalty_override != 1)
		{
			return $default;
		}

		return $prompt->frequency_penalty ?? $default;
	}

	/**
	 * Get the integration
	 *
	 * @return  int|null   1 = Word-Based, 2 = Verse-Based, 3 = Selection-Based
	 * @since   2.0.1
	 */
	public function getIntegration(): ?int
	{
		$prompt = $this->get();
		return $prompt ? $prompt->integration ?? null : null;
	}

	/**
	 * Get the cache behaviour
	 *
	 * @return  int|null   0 = Persistently, 2 = Basic, 3 = Advanced
	 * @since   2.0.1
	 */
	public function getCacheBehaviour(): ?int
	{
		$prompt = $this->get();
		return $prompt ? $prompt->cache_behaviour ?? null : null;
	}

	/**
	 * Get the cache capacity
	 *
	 * @return  int    The number to cache
	 * @since   2.0.1
	 */
	public function getCacheCapacity(): int
	{
		$prompt = $this->get();
		return $prompt ? $prompt->cache_capacity ?? 1 : 1;
	}

	/**
	 * Get the prompt messages
	 *
	 * @return  array|null    The array of massage
	 * @since   2.0.1
	 */
	public function getMessages(): ?array
	{
		$prompt = $this->get();
		return $prompt ? $prompt->messages ?? null : null;
	}

	/**
	 * Get the prompt
	 *
	 * @return  object|null   True on success
	 * @since   2.0.1
	 */
	protected function get(): ?object
	{
		// get from cache if not found
		$guid = $this->config->get('prompt');

		if (empty($guid))
		{
			return null;
		}

		if (!isset($this->prompts[$guid]))
		{
			$this->prompts[$guid] = $this->load->item(['guid' => $guid, 'published' => 1], 'prompt');

			if ($this->prompts[$guid] && isset($this->prompts[$guid]->messages) && is_object($this->prompts[$guid]->messages))
			{
				$this->prompts[$guid]->messages = array_values((array) $this->prompts[$guid]->messages);
			}
		}

		return $this->prompts[$guid];
	}
}

