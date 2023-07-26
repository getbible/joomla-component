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

namespace VDM\Joomla\GetBible\Tagged;


use Joomla\CMS\Router\Route;


/**
 * The GetBible Tagged Paragraphs
 * 
 * @since 2.0.1
 */
final class Paragraphs
{
	/**
	 * The Previous Book number
	 *
	 * @var    int
	 * @since  2.0.1
	 */
	private int $previousBook = 0;

	/**
	 * The Previous Chapter number
	 *
	 * @var    int
	 * @since  2.0.1
	 */
	private int $previousChapter = 0;

	/**
	 * The Previous Verse number
	 *
	 * @var    int
	 * @since  2.0.1
	 */
	private int $previousVerse = 0;

	/**
	 * The Current Group number
	 *
	 * @var    int
	 * @since  2.0.1
	 */
	private int $group = 0;

	/**
	 * The paragraphs being build
	 *
	 * @var    array
	 * @since  2.0.1
	 */
	private array $paragraphs = [];

	/**
	 * Main function to get sorted paragraphs.
	 *
	 * @param array  $items       The items to be sorted into paragraphs.
	 * @param string $translation The translation to be used.
	 *
	 * @return array The sorted paragraphs.
	 * @since  2.0.1
	 */
	public function get(array $items, string $translation = 'kjv'): array
	{
		$this->resetProperties();

		// sort the items by book, chapter, and verse
		usort($items, function ($a, $b) {
			if ($a->book_nr != $b->book_nr) {
				return $a->book_nr - $b->book_nr;
			}
			if ($a->chapter != $b->chapter) {
				return $a->chapter - $b->chapter;
			}
			return $a->verse - $b->verse;
		});

		$this->sortIntoParagraphs($items);
		$this->setUrls($translation);

		return $this->paragraphs;
	}

	/**
	* Reset properties before getting paragraphs
	 *
	 * @return  void
	 * @since  2.0.1
	*/
	private function resetProperties(): void
	{
		$this->previousBook = 0;
		$this->previousChapter = 0;
		$this->previousVerse = 0;
		$this->group = 0;
		$this->paragraphs = [];
	}

	/**
	 * Sort items into paragraphs.
	 *
	 * @param array  $items   The items to be sorted into paragraphs.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function sortIntoParagraphs($items): void
	{
		foreach ($items as $item)
		{
			if ($this->isNotSequential($item)) 
			{
				$this->group++;
			}

			$key = $this->getKey($item);

			if (empty($this->paragraphs[$key])) 
			{
				$this->setDetails($item, $key);
			}

			$this->addVerse($item, $key);

			$this->updatePrevious($item);
		}
	}

	/**
	 * Check if a verse is not sequential.
	 *
	 * @param  object $item The verse to be checked.
	 *
	 * @return bool         Whether the verse is not sequential.
	 * @since  2.0.1
	 */
	private function isNotSequential(object $item): bool
	{
		return $this->previousVerse > 0
			&& ((int) $item->book_nr > $this->previousBook
			|| (int) $item->chapter > $this->previousChapter
			|| (int) $item->verse > $this->previousVerse + 1);
	}

	/**
	 * Generate a key based on the verse item.
	 *
	 * @param  object $item The verse item.
	 *
	 * @return string       The generated key.
	 * @since  2.0.1
	 */
	private function getKey(object $item): string
	{
		return $item->book_nr . '_' . $item->chapter . '_' . $this->group;
	}

	/**
	 * Set details for a verse.
	 *
	 * @param object $item The verse item.
	 * @param string $key  The key for the paragraph.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function setDetails(object $item, string $key): void
	{
		$this->paragraphs[$key]['name'] = $item->name . ' ' . $item->chapter;
		$this->paragraphs[$key]['book'] = $item->name;
		$this->paragraphs[$key]['data_book'] = preg_replace('/[^\p{L}\p{N}\s]/u', '', $item->name);
		$this->paragraphs[$key]['chapter'] = $item->chapter;
	}

	/**
	 * Add a verse to the paragraphs.
	 *
	 * @param object $item The verse item.
	 * @param string $key  The key for the paragraph.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function addVerse(object $item, string $key): void
	{
		$this->paragraphs[$key]['verses'][$item->verse] = ['number' => $item->verse, 'text' => $item->text];
	}

	/**
	 * Update the previous verse, book, and chapter to the current one.
	 *
	 * @param object $item The current verse item.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function updatePrevious(object $item): void
	{
		$this->previousVerse = (int) $item->verse;
		$this->previousBook = (int) $item->book_nr;
		$this->previousChapter = (int) $item->chapter;
	}

	/**
	 * Set URLs for the paragraphs.
	 *
	 * @param string $translation The translation to be used.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function setUrls(string $translation): void
	{
		if ($this->paragraphs !== [])
		{
			foreach ($this->paragraphs as $chapter => &$paragraph)
			{
				$this->setVerseAndName($paragraph);
				$this->setUrl($paragraph, $translation);
			}
		}
	}

	/**
	 * Set verse and name for a paragraph.
	 *
	 * @param array $paragraph The paragraph to be updated.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function setVerseAndName(array &$paragraph): void
	{
		$verse = array_keys($paragraph['verses']);
		$first = reset($verse);
		$last = end($verse);

		if ($first == $last)
		{
			$paragraph['name'] .= ':' . $first;
			$paragraph['verse'] = $first;
		}
		else
		{
			$paragraph['name'] .= ':' . $first . '-' . $last;
			$paragraph['verse'] = $first . '-' . $last;
		}
	}

	/**
	 * Set URL for a paragraph.
	 *
	 * @param array  $paragraph   The paragraph to be updated.
	 * @param string $translation The translation to be used.
	 *
	 * @return  void
	 * @since  2.0.1
	 */
	private function setUrl(array &$paragraph, string $translation): void
	{
		$paragraph['url'] = Route::_('index.php?option=com_getbible&view=app&t=' . $translation . '&ref=' . $paragraph['book'] . '&chapter=' . $paragraph['chapter'] . '&verse=' . $paragraph['verse']);
	}
}

