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


use VDM\Joomla\GetBible\Data\Scripture;
use VDM\Joomla\GetBible\Data\Prompt;
use VDM\Joomla\GetBible\Database\Load;
use VDM\Joomla\GetBible\Openai\Config;


/**
 * The GetBible Response Data
 * 
 * @since 2.0.1
 */
final class Response
{
	/**
	 * The Scripture class
	 *
	 * @var    Scripture
	 * @since  2.0.1
	 */
	protected Scripture $scripture;

	/**
	 * The Prompt class
	 *
	 * @var    Prompt
	 * @since  2.0.1
	 */
	protected Prompt $prompt;

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
	 * The responses
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $responses = [];

	/**
	 * The total responses
	 *
	 * @var    int
	 * @since  2.0.1
	 */
	protected int $total = 0;

	/**
	 * Constructor
	 *
	 * @param Scripture    $scripture  The scripture object.
	 * @param Prompt       $prompt     The prompt object.
	 * @param Load         $load       The load object.
	 * @param Config       $config     The config object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Scripture $scripture,
		Prompt $prompt,
		Load $load,
		Config $config)
	{
		$this->scripture = $scripture;
		$this->prompt = $prompt;
		$this->load = $load;
		$this->config = $config;
	}

	/**
	 * Get the responses
	 *
	 * @return  array|null   True on success
	 * @since   2.0.1
	 */
	public function get(): ?array
	{
		// get the cache behaviour
		$cache = $this->prompt->getCacheBehaviour();

		if (empty($cache) || ($cache != 1 && $cache != 2))
		{
			return null;
		}

		// load the prompt GUID
		$guid = $this->config->get('prompt');

		if ($guid === null)
		{
			return null;
		}

		if (!isset($this->responses[$guid]))
		{
			$this->responses[$guid] = null;

			$query = ['prompt' => $guid, 'published' => 1];

			// Basic Caching - Words/Language
			if ($cache == 1)
			{
				// any empty string so cause no value to be returned
				$query['language'] = $this->scripture->get('translation_language', 'none__found');
				$query['selected_word'] = $this->scripture->get('selected_word_text', 'none__found');
			}
			// Advanced Caching - Verse/Contex
			else
			{
				// any empty string so cause no value to be returned
				$query['abbreviation'] = $this->scripture->get('translation_abbreviation', 'none__found');
				$query['book'] = $this->scripture->get('book_number', 'none__found');
				$query['chapter'] = $this->scripture->get('chapter_number', 'none__found');
				$query['verse'] = $this->scripture->get('verse_number', 'none__found');

				// get the integration
				$integration = $this->prompt->getIntegration();
				if ($integration == 1 || $integration == 3)
				{
					$query['word'] = $this->scripture->get('selected_word_number', 'none__found');
				}
			}
			$bucket = $this->load->items($query, 'open_ai_response');

			if (is_array($bucket) && $bucket !== [])
			{
				foreach($bucket as $nr => &$response)
				{
					$response->messages = $this->load->items([
						'prompt' => $guid,
						'open_ai_response' => $response->response_id,
						'published' => 1
					], 'open_ai_message');
				}

				$this->responses[$guid] = $bucket;
			}
		}

		return $this->responses[$guid];
	}

	/**
	 * is there enough response messages
	 *
	 * @return  bool true if there is enough messages
	 * @since   2.0.1
	 */
	public function isEnough(): bool
	{
		return ($this->getTotal() >= $this->prompt->getCacheCapacity());
	}

	/**
	 * Get the total responses in cache
	 *
	 * @return  int     Number responses from OpenAI
	 * @since   2.0.1
	 */
	public function getTotal(): int
	{
		if ($this->total > 0 || ($responses = $this->get()) === null)
		{
			return $this->total;
		}

		$this->total = count((array) $responses);

		return $this->total;
	}
}

