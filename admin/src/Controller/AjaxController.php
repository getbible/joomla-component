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
namespace TrueChristianChurch\Component\Getbible\Administrator\Controller;

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;

/**
 * Getbible Ajax Base Controller
 */
class AjaxController extends BaseController
{
    /**
     * Constructor.
     *
     * @param   array                 $config   An optional associative array of configuration settings.
     *                                          Recognized key values include 'name', 'default_task', 'model_path', and
     *                                          'view_path' (this list is not meant to be comprehensive).
     * @param   ?MVCFactoryInterface  $factory  The factory.
     * @param   ?CMSApplication       $app      The Application for the dispatcher
     * @param   ?Input                $input    Input
     *
     * @since   3.0
     */
    public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
	{
		parent::__construct($config, $factory, $app, $input);

		// make sure all json stuff are set
		$this->app->getDocument()->setMimeEncoding( 'application/json' );
		$this->app->setHeader('Content-Disposition','attachment;filename="getajax.json"');
		$this->app->setHeader('Access-Control-Allow-Origin', '*');
		// load the tasks 
		$this->registerTask('isNew', 'ajax');
		$this->registerTask('isRead', 'ajax');
		$this->registerTask('getWiki', 'ajax');
		$this->registerTask('getVersion', 'ajax');
	}

	public function ajax()
	{
		// get the user for later use
		$user         = $this->app->getIdentity();
		// get the input values
		$jinput       = $this->input ?? $this->app->input;
		// check if we should return raw
		$returnRaw    = $jinput->get('raw', false, 'BOOLEAN');
		// return to a callback function
		$callback     = $jinput->get('callback', null, 'CMD');
		// Check Token!
		$token        = Session::getFormToken();
		$call_token   = $jinput->get('token', 0, 'ALNUM');
		if($jinput->get($token, 0, 'ALNUM') || $token === $call_token)
		{
			// get the task
			$task = $this->getTask();
			switch($task)
			{
				case 'isNew':
					try
					{
						$noticeValue = $jinput->get('notice', NULL, 'STRING');
						if($noticeValue && $user->id != 0)
						{
							$ajaxModule = $this->getModel('ajax', 'Administrator');
							if ($ajaxModule)
							{
								$result = $ajaxModule->isNew($noticeValue);
							}
							else
							{
								$result = false;
							}
						}
						else
						{
							$result = false;
						}
						if($callback)
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback)
						{
							echo $callback."(".json_encode($e).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($e);
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'isRead':
					try
					{
						$noticeValue = $jinput->get('notice', NULL, 'STRING');
						if($noticeValue && $user->id != 0)
						{
							$ajaxModule = $this->getModel('ajax', 'Administrator');
							if ($ajaxModule)
							{
								$result = $ajaxModule->isRead($noticeValue);
							}
							else
							{
								$result = false;
							}
						}
						else
						{
							$result = false;
						}
						if($callback)
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback)
						{
							echo $callback."(".json_encode($e).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($e);
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'getWiki':
					try
					{
						$nameValue = $jinput->get('name', NULL, 'WORD');
						if($nameValue && $user->id != 0)
						{
							$ajaxModule = $this->getModel('ajax', 'Administrator');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getWiki($nameValue);
							}
							else
							{
								$result = false;
							}
						}
						else
						{
							$result = false;
						}
						if($callback)
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback)
						{
							echo $callback."(".json_encode($e).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($e);
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
				case 'getVersion':
					try
					{
						$versionValue = $jinput->get('version', NULL, 'INT');
						if($versionValue && $user->id != 0)
						{
							$ajaxModule = $this->getModel('ajax', 'Administrator');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getVersion($versionValue);
							}
							else
							{
								$result = false;
							}
						}
						else
						{
							$result = false;
						}
						if($callback)
						{
							echo $callback . "(".json_encode($result).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($result);
						}
						else
						{
							echo "(".json_encode($result).");";
						}
					}
					catch(Exception $e)
					{
						if($callback)
						{
							echo $callback."(".json_encode($e).");";
						}
						elseif($returnRaw)
						{
							echo json_encode($e);
						}
						else
						{
							echo "(".json_encode($e).");";
						}
					}
				break;
			}
		}
		else
		{
			// return to a callback function
			if($callback)
			{
				echo $callback."(".json_encode(false).");";
			}
			elseif($returnRaw)
			{
				echo json_encode(false);
			}
			else
			{
				echo "(".json_encode(false).");";
			}
		}
	}
}
