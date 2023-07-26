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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Utilities\ArrayHelper;

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
			'return' => 'BASE64',
			'layout' => 'STRING',
			'format' => 'STRING',
			'Itemid' => 'INT'
		);

		// should these not merge?
		if (GetbibleHelper::checkString($urlparams))
		{
			$safeurlparams = Super___0a59c65c_9daf_4bc9_baf4_e063ff9e6a8a___Power::merge(array($urlparams, $safeurlparams));
		}

		return parent::display($cachable, $safeurlparams);
	}

	protected function checkEditView($view)
	{
		if (GetbibleHelper::checkString($view))
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
