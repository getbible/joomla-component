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

namespace VDM\Joomla\GetBible\AI;


use VDM\Joomla\GetBible\Data\Scripture;
use VDM\Joomla\GetBible\Data\Prompt;
use VDM\Joomla\GetBible\Data\Placeholders;
use VDM\Joomla\Openai\Chat;
use VDM\Joomla\GetBible\Database\Insert;


/**
 * The GetBible AI Engineer
 * 
 * @since 2.0.1
 */
final class Engineer
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
	 * The Placeholders class
	 *
	 * @var    Placeholders
	 * @since  2.0.1
	 */
	protected Placeholders $placeholders;

	/**
	 * The Chat class
	 *
	 * @var    Chat
	 * @since  2.0.1
	 */
	protected Chat $chat;

	/**
	 * The Insert class
	 *
	 * @var    Insert
	 * @since  2.0.1
	 */
	protected Insert $insert;

	/**
	 * The request messages
	 *
	 * @var    array|null
	 * @since  2.0.1
	 */
	protected ?array $messages = null;

	/**
	 * The response object
	 *
	 * @var    object|null
	 * @since  2.0.1
	 */
	protected ?object $response = null;

	/**
	 * Constructor
	 *
	 * @param Scripture        $scripture      The scripture object.
	 * @param Prompt           $prompt         The prompt object.
	 * @param Placeholders     $placeholders   The placeholders object.
	 * @param Chat             $chat           The chat object.
	 * @param Insert           $insert         The insert object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Scripture $scripture,
		Prompt $prompt,
		Placeholders $placeholders,
		Chat $chat,
		Insert $insert)
	{
		$this->scripture = $scripture;
		$this->prompt = $prompt;
		$this->placeholders = $placeholders;
		$this->chat = $chat;
		$this->insert = $insert;
	}

	/**
	 * Return the current data state
	 *
	 * @return  array|null   True on success
	 * @since   2.0.1
	 */
	public function get(): ?array
	{
		if ($this->getResponse() && $this->modelMessages() && $this->modelResponse()
			&& $this->saveResponse() && $this->saveMessages())
		{
			// add this response's messages
			$this->response->messages = $this->messages ?? '';

			return [
				$this->response
			];
		}

		return null;
	}

	/**
	 * Save the messages
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	private function saveMessages(): bool
	{
		return $this->insert->items($this->messages, 'open_ai_message');
	}

	/**
	 * Save the response
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	private function saveResponse(): bool
	{
		return $this->insert->item($this->response, 'open_ai_response');
	}

	/**
	 * Prep the response data
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	private function modelResponse(): bool
	{
		if ($this->response)
		{
			$data = new \stdClass();
			// Bible link
			$data->abbreviation = $this->scripture->get('translation_abbreviation', '');
			$data->language = $this->scripture->get('translation_language', '');
			$data->lcsh = $this->scripture->get('translation_lcsh', '');
			$data->book = $this->scripture->get('book_number', '');
			$data->chapter = $this->scripture->get('chapter_number', '');
			$data->verse = $this->scripture->get('verse_number', '');
			$data->word = $this->scripture->get('selected_word_number', '');
			$data->selected_word = $this->scripture->get('selected_word_text', '');
			// Prompt data
			$data->prompt = $this->prompt->getGuid();
			$data->model = $this->prompt->getModel();
			$data->max_tokens = $this->prompt->getMaxTokens();
			$data->temperature = $this->prompt->getTemperature();
			$data->top_p = $this->prompt->getTopP();
			$data->presence_penalty = $this->prompt->getPresencePenalty();
			$data->frequency_penalty = $this->prompt->getFrequencyPenalty();
			$data->n = '';
			// Response data
			$data->response_id = $this->response->id ?? '';
			$data->response_object = $this->response->object ?? '';
			$data->response_model = $this->response->model ?? '';
			$data->response_created = $this->response->created ?? '';
			$data->prompt_tokens = $this->response->usage->prompt_tokens ?? '';
			$data->completion_tokens = $this->response->usage->completion_tokens ?? '';
			$data->total_tokens = $this->response->usage->total_tokens ?? '';

			$this->response = $data;

			return true;
		}

		return false;
	}

	/**
	 * Prep the messages
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	private function modelMessages(): bool
	{
		if (isset($this->response->choices) &&
			is_array($this->response->choices) &&
			$this->response->choices !== [])
		{
			// set some global keys
			$open_ai_response = $this->response->id ?? '';
			$prompt = $this->prompt->getGuid();
			// if Persistently we archive this response
			$cache_type = $this->prompt->getCacheBehaviour();
			$state = ($cache_type == 0) ? 2 : 1;

			// update the system messages
			$index = (int) 0 - count($this->messages);
			foreach ($this->messages as $n => &$message)
			{
				$message->prompt = $prompt;
				$message->open_ai_response = $open_ai_response;
				$message->source = 1; // prompt as source
				$message->index = $index;
				$message->published = $state;

				$index++;
			}

			// now add the response messages
			foreach ($this->response->choices as $choice)
			{
				$choice->message->prompt = $prompt;
				$choice->message->open_ai_response = $open_ai_response;
				$choice->message->source = 2; // open AI as source
				$choice->message->index = $choice->index;
				$choice->message->name = 'Open-AI';
				$choice->message->published = $state;

				$this->messages[] = $choice->message;
			}

			// we remove it from the response object
			unset($this->response->choices);

			return true;
		}

		return false;
	}

	/**
	 * Get the Open AI response
	 *
	 * @return  bool   True on success
	 * @since   2.0.1
	 */
	private function getResponse(): bool
	{
		if (($this->messages = $this->getMessages()) !== null)
		{
			$this->response = $this->chat->create(
				$this->prompt->getModel(),
				$this->messages,
				$this->prompt->getMaxTokens(),
				$this->prompt->getTemperature(),
				$this->prompt->getTopP(),
				$this->prompt->getN(),
				null,
				null,
				$this->prompt->getPresencePenalty(),
				$this->prompt->getFrequencyPenalty(),
				null
			);

			return true;
		}

		return false;
	}

	/**
	 * Get the ready to use prompt messages
	 *
	 * @return  array|null   Array of prompt messages
	 * @since   2.0.1
	 */
	private function getMessages(): ?array
	{
		if (($placeholders = $this->placeholders->get()) !== null &&
			($messages = $this->prompt->getMessages()) !== null)
		{
			return array_map(function ($message) use($placeholders) {
				$message->content = str_replace(
					array_keys($placeholders),
					array_values($placeholders),
					$message->content
				);
				return $message;
			}, $messages);
		}

		return null;
	}
}

