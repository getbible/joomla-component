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
namespace TrueChristianChurch\Component\Getbible\Site\Controller;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use VDM\Joomla\Utilities\StringHelper;
use VDM\Joomla\Utilities\ArrayHelper as UtilitiesArrayHelper;

/**
 * Getbible master site display controller.
 *
 * @since   4.0
 */
class DisplayController extends BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached.
	 * @param   boolean  $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link InputFilter::clean()}.
	 *
	 * @return  BaseController  This object to support chaining.
	 *
	 */
	function display($cachable = false, $urlparams = false)
	{
		// set default view if not set
		$view          = $this->input->getCmd('view', 'app');
		$this->input->set('view', $view);
		$isEdit        = $this->checkEditView($view);
		$layout        = $this->input->get('layout', null, 'WORD');
		$id            = $this->input->getInt('id');
		$cachable      = true;

		// ensure that the view is not cashable if edit view or if user is logged in
		$user = Factory::getApplication()->getIdentity();
		if ($user->get('id') || $isEdit)
		{
			$cachable = false;
		}

		// Check for edit form.
		if($isEdit)
		{
			if ($layout == 'edit' && !$this->checkEditId('com_getbible.edit.'.$view, $id))
			{
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
				$this->setMessage($this->getError(), 'error');
				// check if item was opened from other than its own list view
				$ref     = $this->input->getCmd('ref', 0);
				$refid     = $this->input->getInt('refid', 0);
				// set redirect
				if ($refid > 0 && StringHelper::check($ref))
				{
					// redirect to item of ref
					$this->setRedirect(Route::_('index.php?option=com_getbible&view='.(string)$ref.'&layout=edit&id='.(int)$refid, false));
				}
				elseif (StringHelper::check($ref))
				{
					// redirect to ref
					 $this->setRedirect(Route::_('index.php?option=com_getbible&view='.(string)$ref, false));
				}
				else
				{
					// normal redirect back to the list default site view
					$this->setRedirect(Route::_('index.php?option=com_getbible&view=app', false));
				}
				return false;
			}
		}

		// we may need to make this more dynamic in the future. (TODO)
		$safeurlparams = array(
			'catid' => 'INT',
			'id' => 'INT',
			'cid' => 'ARRAY',
			'year' => 'INT',
			'month' => 'INT',
			'limit' => 'UINT',
			'limitstart' => 'UINT',
			'showall' => 'INT',
			'return' => 'BASE64',
			'filter' => 'STRING',
			'filter_order' => 'CMD',
			'filter_order_Dir' => 'CMD',
			'filter-search' => 'STRING',
			'print' => 'BOOLEAN',
			'lang' => 'CMD',
			'Itemid' => 'INT');

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
