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


use VDM\Joomla\GetBible\Data\Translation;
use VDM\Joomla\GetBible\Data\Book;
use VDM\Joomla\GetBible\Data\Chapter;
use VDM\Joomla\GetBible\Data\Verse;
use VDM\Joomla\GetBible\Data\Word;


/**
 * The GetBible Scripture
 * 
 * @since 2.0.1
 */
final class Scripture
{
	/**
	 * The Translation class
	 *
	 * @var    Translation
	 * @since  2.0.1
	 */
	protected Translation $translation;

	/**
	 * The Book class
	 *
	 * @var    Book
	 * @since  2.0.1
	 */
	protected Book $book;

	/**
	 * The Chapter class
	 *
	 * @var    Chapter
	 * @since  2.0.1
	 */
	protected Chapter $chapter;

	/**
	 * The Verse class
	 *
	 * @var    Verse
	 * @since  2.0.1
	 */
	protected Verse $verse;

	/**
	 * The Word class
	 *
	 * @var    Word
	 * @since  2.0.1
	 */
	protected Word $word;

	/**
	 * The Scripture object
	 *
	 * @var    object
	 * @since  2.0.1
	 */
	protected ?object $scripture;

	/**
	 * Constructor
	 *
	 * @param Translation   $translation   The translation object.
	 * @param Book          $book          The book object.
	 * @param Chapter       $chapter       The chapter object.
	 * @param Verse         $verse         The verse object.
	 * @param Word          $word          The word object.
	 *
	 * @since 2.0.1
	 */
	public function __construct(
		Translation $translation,
		Book $book,
		Chapter $chapter,
		Verse $verse,
		Word $word)
	{
		$this->translation = $translation;
		$this->book = $book;
		$this->chapter = $chapter;
		$this->verse = $verse;
		$this->word = $word;
	}

	/**
	 * Get the Open AI response
	 *
	 * @param   string  $key      The value key.
	 * @param   mixed   $default  The default value.
	 *
	 * @return  mixed   Scripture Values
	 * @since   2.0.1
	 */
	public function get(string $key, $default = null)
	{
		if (empty($this->scripture))
		{
			$this->scripture  = $this->getScripture();
		}

		return $this->scripture->{$key} ?? $default;
	}

	/**
	 * Get all related scripture values
	 *
	 * @return  object    Object of Scripture Values
	 * @since   2.0.1
	 */
	private function getScripture(): object
	{
		/**
		 *  do not change these keys !!!
		 *  this is an easy mapping
		 *  so called bad practice
		 *  that we use to simplify
		 *  access to these values
		 */
		return (object) [
			'translation_name' => $this->translation->getName(),
			'translation_language' => $this->translation->getLanguage(),
			'translation_lcsh' => $this->translation->getLcsh(),
			'translation_abbreviation' => $this->translation->getAbbreviation(),
			'book_number' => $this->book->getNumber(),
			'book_name' => $this->book->getName(),
			'chapter_number' => $this->chapter->getNumber(),
			'chapter_name' => $this->chapter->getName(),
			'chapter_text' => $this->chapter->getText(),
			'verse_number' => $this->verse->getNumber(),
			'verse_name' => $this->verse->getName(),
			'verse_text' => $this->verse->getText(),
			'selected_word_number' => $this->word->getNumber(),
			'selected_word_text' => $this->word->getText()
		];
	}
}

