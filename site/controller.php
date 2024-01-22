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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;

/**
 * Getbible Component Base Controller
 */
class GetbibleController extends BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController  This object to support chaining.
	 *
	 */

	function display($cachable = false, $urlparams = false)
	{
		// we may need to make this more dynamic in the future. (TODO)
		$safeurlparams = array(
			'ref' => 'STRING',
			't' => 'STRING',
			'version' => 'STRING',
			'translation' => 'STRING',
			'b' => 'INT',
			'book' => 'INT',
			'target_book' => 'INT',
			'c' => 'INT',
			'chapter' => 'INT',
			'verse' => 'STRING',
			'v' => 'STRING',
			'criteria' => 'STRING',
			'words' => 'STRING',
			'match' => 'STRING',
			'case' => 'STRING',
			'target' => 'STRING',
			'guid' => 'STRING',
			'tag' => 'STRING',
			'linker' => 'STRING',
			'return' => 'BASE64',
			'bibleurl' => 'BASE64',
			'layout' => 'STRING',
			'format' => 'STRING',
			'Itemid' => 'INT'
		);

		// should these not merge?
		if (UtilitiesArrayHelper::check($urlparams))
		{
			$safeurlparams = UtilitiesArrayHelper::merge(array($urlparams, $safeurlparams));
		}

		return parent::display($cachable, $safeurlparams);
	}

	protected function checkEditView($view)
	{
		if (StringHelper::check($view))
		{
			$views = array(

				);
			// check if this is a edit view
			if (in_array($view,$views))
			{
				return true;
			}
		}
		return false;
	}
}
