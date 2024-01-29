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
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Getbible Ajax Base Controller
 */
class GetbibleControllerAjax extends BaseController
{
	public function __construct($config)
	{
		parent::__construct($config);
		// make sure all json stuff are set
		Factory::getDocument()->setMimeEncoding( 'application/json' );
		// get the application
		$app = Factory::getApplication();
		$app->setHeader('Content-Disposition','attachment;filename="getajax.json"');
		$app->setHeader('Access-Control-Allow-Origin', '*');
		// load the tasks 
		$this->registerTask('getShareHisWordUrl', 'ajax');
		$this->registerTask('checkValidLinker', 'ajax');
		$this->registerTask('isLinkerAuthenticated', 'ajax');
		$this->registerTask('installBibleChapter', 'ajax');
		$this->registerTask('getAppUrl', 'ajax');
		$this->registerTask('setLinker', 'ajax');
		$this->registerTask('setLinkerAccess', 'ajax');
		$this->registerTask('revokeLinkerSession', 'ajax');
		$this->registerTask('revokeLinkerAccess', 'ajax');
		$this->registerTask('setLinkerName', 'ajax');
		$this->registerTask('getLinkersDisplay', 'ajax');
		$this->registerTask('setNote', 'ajax');
		$this->registerTask('tagVerse', 'ajax');
		$this->registerTask('removeTagFromVerse', 'ajax');
		$this->registerTask('createTag', 'ajax');
		$this->registerTask('updateTag', 'ajax');
		$this->registerTask('deleteTag', 'ajax');
		$this->registerTask('getSearchUrl', 'ajax');
		$this->registerTask('getOpenaiURL', 'ajax');
	}

	public function ajax()
	{
		// get the user for later use
		$user         = Factory::getUser();
		// get the input values
		$jinput       = Factory::getApplication()->input;
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
				case 'getShareHisWordUrl':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						$translationValue = $jinput->get('translation', null, 'ALNUM');
						$bookValue = $jinput->get('book', null, 'INT');
						$chapterValue = $jinput->get('chapter', null, 'INT');
						if($linkerValue && $translationValue && $bookValue && $chapterValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getShareHisWordUrl($linkerValue, $translationValue, $bookValue, $chapterValue);
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
					catch(\Exception $e)
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
				case 'checkValidLinker':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						$oldValue = $jinput->get('old', null, 'STRING');
						if($linkerValue && $oldValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->checkValidLinker($linkerValue, $oldValue);
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
					catch(\Exception $e)
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
				case 'isLinkerAuthenticated':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->isLinkerAuthenticated($linkerValue);
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
					catch(\Exception $e)
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
				case 'installBibleChapter':
					try
					{
						$translationValue = $jinput->get('translation', null, 'ALNUM');
						$bookValue = $jinput->get('book', null, 'INT');
						$chapterValue = $jinput->get('chapter', null, 'INT');
						$forceValue = $jinput->get('force', 0, 'INT');
						if($translationValue && $bookValue && $chapterValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->installBibleChapter($translationValue, $bookValue, $chapterValue, $forceValue);
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
					catch(\Exception $e)
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
				case 'getAppUrl':
					try
					{
						$translationValue = $jinput->get('translation', null, 'ALNUM');
						$bookValue = $jinput->get('book', null, 'STRING');
						$chapterValue = $jinput->get('chapter', 1, 'INT');
						$verseValue = $jinput->get('verse', null, 'STRING');
						if($translationValue && $bookValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getAppUrl($translationValue, $bookValue, $chapterValue, $verseValue);
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
					catch(\Exception $e)
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
				case 'setLinker':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->setLinker($linkerValue);
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
					catch(\Exception $e)
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
				case 'setLinkerAccess':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						$passValue = $jinput->get('pass', null, 'STRING');
						$oldValue = $jinput->get('old', null, 'STRING');
						if($linkerValue && $passValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->setLinkerAccess($linkerValue, $passValue, $oldValue);
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
					catch(\Exception $e)
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
				case 'revokeLinkerSession':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->revokeLinkerSession($linkerValue);
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
					catch(\Exception $e)
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
				case 'revokeLinkerAccess':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->revokeLinkerAccess($linkerValue);
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
					catch(\Exception $e)
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
				case 'setLinkerName':
					try
					{
						$nameValue = $jinput->get('name', null, 'STRING');
						if($nameValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->setLinkerName($nameValue);
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
					catch(\Exception $e)
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
				case 'getLinkersDisplay':
					try
					{
						$linkersValue = $jinput->get('linkers', null, 'STRING');
						if($linkersValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getLinkersDisplay($linkersValue);
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
					catch(\Exception $e)
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
				case 'setNote':
					try
					{
						$bookValue = $jinput->get('book', null, 'INT');
						$chapterValue = $jinput->get('chapter', null, 'INT');
						$verseValue = $jinput->get('verse', null, 'INT');
						$noteValue = $jinput->get('note', null, 'STRING');
						if($bookValue && $chapterValue && $verseValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->setNote($bookValue, $chapterValue, $verseValue, $noteValue);
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
					catch(\Exception $e)
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
				case 'tagVerse':
					try
					{
						$translationValue = $jinput->get('translation', null, 'ALNUM');
						$bookValue = $jinput->get('book', null, 'INT');
						$chapterValue = $jinput->get('chapter', null, 'INT');
						$verseValue = $jinput->get('verse', null, 'INT');
						$tagValue = $jinput->get('tag', null, 'STRING');
						if($translationValue && $bookValue && $chapterValue && $verseValue && $tagValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->tagVerse($translationValue, $bookValue, $chapterValue, $verseValue, $tagValue);
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
					catch(\Exception $e)
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
				case 'removeTagFromVerse':
					try
					{
						$tagValue = $jinput->get('tag', null, 'STRING');
						if($tagValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->removeTagFromVerse($tagValue);
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
					catch(\Exception $e)
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
				case 'createTag':
					try
					{
						$nameValue = $jinput->get('name', null, 'STRING');
						$descriptionValue = $jinput->get('description', null, 'STRING');
						if($nameValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->createTag($nameValue, $descriptionValue);
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
					catch(\Exception $e)
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
				case 'updateTag':
					try
					{
						$tagValue = $jinput->get('tag', null, 'STRING');
						$nameValue = $jinput->get('name', null, 'STRING');
						$descriptionValue = $jinput->get('description', null, 'STRING');
						if($tagValue && $nameValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->updateTag($tagValue, $nameValue, $descriptionValue);
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
					catch(\Exception $e)
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
				case 'deleteTag':
					try
					{
						$tagValue = $jinput->get('tag', null, 'STRING');
						if($tagValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->deleteTag($tagValue);
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
					catch(\Exception $e)
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
				case 'getSearchUrl':
					try
					{
						$translationValue = $jinput->get('translation', NULL, 'WORD');
						$wordsValue = $jinput->get('words', NULL, 'INT');
						$matchValue = $jinput->get('match', NULL, 'INT');
						$caseValue = $jinput->get('case', NULL, 'INT');
						$targetValue = $jinput->get('target', NULL, 'INT');
						$searchValue = $jinput->get('search', NULL, 'STRING');
						$target_bookValue = $jinput->get('target_book', 0, 'INT');
						$bookValue = $jinput->get('book', 0, 'INT');
						$chapterValue = $jinput->get('chapter', 0, 'INT');
						if($translationValue && $wordsValue && $matchValue && $caseValue && $targetValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getSearchUrl($translationValue, $wordsValue, $matchValue, $caseValue, $targetValue, $searchValue, $target_bookValue, $bookValue, $chapterValue);
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
					catch(\Exception $e)
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
				case 'getOpenaiURL':
					try
					{
						$wordsValue = $jinput->get('words', NULL, 'CMD');
						$verseValue = $jinput->get('verse', NULL, 'CMD');
						$chapterValue = $jinput->get('chapter', NULL, 'INT');
						$bookValue = $jinput->get('book', NULL, 'INT');
						$translationValue = $jinput->get('translation', NULL, 'WORD');
						$guidValue = $jinput->get('guid', NULL, 'STRING');
						if($chapterValue && $bookValue && $translationValue && $guidValue)
						{
							$ajaxModule = $this->getModel('ajax');
							if ($ajaxModule)
							{
								$result = $ajaxModule->getOpenaiURL($wordsValue, $verseValue, $chapterValue, $bookValue, $translationValue, $guidValue);
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
					catch(\Exception $e)
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
			// return raw
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
