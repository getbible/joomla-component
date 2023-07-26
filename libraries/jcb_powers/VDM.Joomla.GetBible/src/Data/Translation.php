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
 * The GetBible Translation Data
 * 
 * @since 2.0.1
 */
final class Translation
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
	 * The translations
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	protected array $translations = [];

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
	 * Get the translation name
	 *
	 * @return  string   The translation name
	 * @since   2.0.1
	 */
	public function getName(): string
	{
		$translation = $this->get();
		return $translation ? $translation->translation ?? '' : '';
	}

	/**
	 * Get the translation language
	 *
	 * @return  string    The translation language
	 * @since   2.0.1
	 */
	public function getLanguage(): string
	{
		$translation = $this->get();
		$lang = trim(preg_replace('/Bible\.?/', '', $this->getLcsh()));
		return $translation ? $translation->language ?? $lang : '';
	}

	/**
	 * Get the translation distribution lcsh
	 *
	 * @return  string    The translation lcsh
	 * @since   2.0.1
	 */
	public function getLcsh(): string
	{
		$translation = $this->get();
		return $translation ? $translation->distribution_lcsh ?? '' : '';
	}

	/**
	 * Get the translation abbreviation
	 *
	 * @return  string    The translation abbreviation
	 * @since   2.0.1
	 */
	public function getAbbreviation(): string
	{
		$translation = $this->get();
		return $translation ? $translation->abbreviation ?? '' : '';
	}

	/**
	 * Get the translation
	 *
	 * @return  object|null   True on success
	 * @since   2.0.1
	 */
	public function get(): ?object
	{
		// get selected translation abbreviation
		$abbreviation = $this->config->get('translation');

		if (empty($abbreviation))
		{
			return null;
		}

		if (!isset($this->translations[$abbreviation]))
		{
			$this->translations[$abbreviation] = $this->load->item(
				['abbreviation' => $abbreviation, 'published' => 1],
				'translation'
			);
		}

		return $this->translations[$abbreviation];
	}
}

