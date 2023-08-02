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
 * Getbible Ajax Base Controller
 */
class GetbibleControllerAjax extends BaseController
{
	public function __construct($config)
	{
		parent::__construct($config);
		// make sure all json stuff are set
		JFactory::getDocument()->setMimeEncoding( 'application/json' );
		// get the application
		$app = JFactory::getApplication();
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
		$user 		= JFactory::getUser();
		// get the input values
		$jinput 	= JFactory::getApplication()->input;
		// check if we should return raw
		$returnRaw	= $jinput->get('raw', false, 'BOOLEAN');
		// return to a callback function
		$callback	= $jinput->get('callback', null, 'CMD');
		// Check Token!
		$token 		= JSession::getFormToken();
		$call_token	= $jinput->get('token', 0, 'ALNUM');
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
							$result = $this->getModel('ajax')->getShareHisWordUrl($linkerValue, $translationValue, $bookValue, $chapterValue);
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
				case 'checkValidLinker':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						$oldValue = $jinput->get('old', null, 'STRING');
						if($linkerValue && $oldValue)
						{
							$result = $this->getModel('ajax')->checkValidLinker($linkerValue, $oldValue);
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
				case 'isLinkerAuthenticated':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$result = $this->getModel('ajax')->isLinkerAuthenticated($linkerValue);
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
				case 'installBibleChapter':
					try
					{
						$translationValue = $jinput->get('translation', null, 'ALNUM');
						$bookValue = $jinput->get('book', null, 'INT');
						$chapterValue = $jinput->get('chapter', null, 'INT');
						$forceValue = $jinput->get('force', 0, 'INT');
						if($translationValue && $bookValue && $chapterValue)
						{
							$result = $this->getModel('ajax')->installBibleChapter($translationValue, $bookValue, $chapterValue, $forceValue);
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
				case 'getAppUrl':
					try
					{
						$translationValue = $jinput->get('translation', null, 'ALNUM');
						$bookValue = $jinput->get('book', null, 'STRING');
						$chapterValue = $jinput->get('chapter', 1, 'INT');
						$verseValue = $jinput->get('verse', null, 'STRING');
						if($translationValue && $bookValue)
						{
							$result = $this->getModel('ajax')->getAppUrl($translationValue, $bookValue, $chapterValue, $verseValue);
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
				case 'setLinker':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$result = $this->getModel('ajax')->setLinker($linkerValue);
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
				case 'setLinkerAccess':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						$passValue = $jinput->get('pass', null, 'STRING');
						$oldValue = $jinput->get('old', null, 'STRING');
						if($linkerValue && $passValue)
						{
							$result = $this->getModel('ajax')->setLinkerAccess($linkerValue, $passValue, $oldValue);
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
				case 'revokeLinkerSession':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$result = $this->getModel('ajax')->revokeLinkerSession($linkerValue);
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
				case 'revokeLinkerAccess':
					try
					{
						$linkerValue = $jinput->get('linker', null, 'STRING');
						if($linkerValue)
						{
							$result = $this->getModel('ajax')->revokeLinkerAccess($linkerValue);
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
				case 'setLinkerName':
					try
					{
						$nameValue = $jinput->get('name', null, 'STRING');
						if($nameValue)
						{
							$result = $this->getModel('ajax')->setLinkerName($nameValue);
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
				case 'getLinkersDisplay':
					try
					{
						$linkersValue = $jinput->get('linkers', null, 'STRING');
						if($linkersValue)
						{
							$result = $this->getModel('ajax')->getLinkersDisplay($linkersValue);
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
				case 'setNote':
					try
					{
						$bookValue = $jinput->get('book', null, 'INT');
						$chapterValue = $jinput->get('chapter', null, 'INT');
						$verseValue = $jinput->get('verse', null, 'INT');
						$noteValue = $jinput->get('note', null, 'STRING');
						if($bookValue && $chapterValue && $verseValue)
						{
							$result = $this->getModel('ajax')->setNote($bookValue, $chapterValue, $verseValue, $noteValue);
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
							$result = $this->getModel('ajax')->tagVerse($translationValue, $bookValue, $chapterValue, $verseValue, $tagValue);
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
				case 'removeTagFromVerse':
					try
					{
						$tagValue = $jinput->get('tag', null, 'STRING');
						if($tagValue)
						{
							$result = $this->getModel('ajax')->removeTagFromVerse($tagValue);
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
				case 'createTag':
					try
					{
						$nameValue = $jinput->get('name', null, 'STRING');
						$descriptionValue = $jinput->get('description', null, 'STRING');
						if($nameValue)
						{
							$result = $this->getModel('ajax')->createTag($nameValue, $descriptionValue);
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
				case 'updateTag':
					try
					{
						$tagValue = $jinput->get('tag', null, 'STRING');
						$nameValue = $jinput->get('name', null, 'STRING');
						$descriptionValue = $jinput->get('description', null, 'STRING');
						if($tagValue && $nameValue)
						{
							$result = $this->getModel('ajax')->updateTag($tagValue, $nameValue, $descriptionValue);
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
				case 'deleteTag':
					try
					{
						$tagValue = $jinput->get('tag', null, 'STRING');
						if($tagValue)
						{
							$result = $this->getModel('ajax')->deleteTag($tagValue);
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
				case 'getSearchUrl':
					try
					{
						$translationValue = $jinput->get('translation', NULL, 'WORD');
						$wordsValue = $jinput->get('words', NULL, 'INT');
						$matchValue = $jinput->get('match', NULL, 'INT');
						$caseValue = $jinput->get('case', NULL, 'INT');
						$targetValue = $jinput->get('target', NULL, 'INT');
						$bookValue = $jinput->get('book', NULL, 'INT');
						$searchValue = $jinput->get('search', NULL, 'STRING');
						if($translationValue && $wordsValue && $matchValue && $caseValue && $targetValue)
						{
							$result = $this->getModel('ajax')->getSearchUrl($translationValue, $wordsValue, $matchValue, $caseValue, $targetValue, $bookValue, $searchValue);
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
							$result = $this->getModel('ajax')->getOpenaiURL($wordsValue, $verseValue, $chapterValue, $bookValue, $translationValue, $guidValue);
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
