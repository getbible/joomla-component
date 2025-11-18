<?php
/*----------------------------------------------------------------------------------|  io.vdm.dev  |----/
			Vast Development Method
/-------------------------------------------------------------------------------------------------------/

    @package    getBible.net

    @created    3rd December, 2015
    @author     Llewellyn van der Merwe <https://getbible.life>
    @git        Get Bible <https://git.vdm.dev/getBible>
    @github     Get Bible <https://github.com/getBible>
    @support    Get Bible <https://git.vdm.dev/getBible/support>
    @copyright  Copyright (C) 2015. All Rights Reserved
    @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/
namespace TrueChristianBible\Component\GetBible\Administrator\View\Getbible;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Document\Document;
use TrueChristianBible\Component\GetBible\Administrator\Helper\GetbibleHelper;
use TrueChristianBible\Joomla\Utilities\StringHelper;

// No direct access to this file
\defined('_JEXEC') or die;

/**
 * Getbible View class
 *
 * @since  1.6
 */
#[\AllowDynamicProperties]
class HtmlView extends BaseHtmlView
{
	/**
	 * @var array<string> List of icon identifiers to render in the dashboard view.
	 * @since 1.6
	 */
	public array $icons = [];

	/**
	 * @var array<string> List of CSS file URLs to be added to the page.
	 * @since 4.3
	 */
	public array $styles = [];

	/**
	 * @var array<string> List of JavaScript file URLs to be included on the page.
	 * @since 4.3
	 */
	public array $scripts = [];

	/**
	 * @var array<int, object> List of contributor objects fetched via the helper.
	 * @since 1.6
	 */
	public array $contributors = [];

	/**
	 * @var object|null The manifest metadata of the component as returned by `ComponentbuilderHelper::manifest()`.
	 * @since 1.6
	 */
	public $manifest = null;

	/**
	 * @var string|null Markdown content of the component's wiki page.
	 * @since 1.6
	 */
	public ?string $wiki = null;

	/**
	 * @var string|null The rendered or raw README markdown of the component.
	 * @since 1.6
	 */
	public ?string $readme = null;

	/**
	 * @var string|null The current version of the component.
	 * @since 1.6
	 */
	public ?string $version = null;

	/**
	 * @var string|null Help URL for the component dashboard view, if available.
	 * @since 1.6
	 */
	public ?string $help_url = null;

	/**
	 * View display method
	 *
	 * @return void
	 * @throws \Exception
	 * @since   1.6
	 */
	function display($tpl = null): void
	{
		// Assign data to the view
		$this->icons          = $this->get('Icons');
		$this->styles         = $this->get('Styles');
		$this->scripts        = $this->get('Scripts');
		$this->contributors   = GetbibleHelper::getContributors();

		// get the manifest details of the component
		$this->manifest = GetbibleHelper::manifest();
		$this->wiki = $this->get('Wiki');
		$this->noticeboard = $this->get('Noticeboard');
		$this->readme = $this->get('Readme');
		$this->version = $this->get('Version');

		// Set the toolbar
		$this->addToolBar();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \Exception(implode("\n", $errors), 500);
		}

		// Set the html view document stuff
		$this->_prepareDocument();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function addToolbar(): void
	{
		$canDo = GetbibleHelper::getActions('getbible');
		ToolbarHelper::title(Text::_('COM_GETBIBLE_DASHBOARD'), 'grid-2');

		// set help url for this view if found
		$this->help_url = GetbibleHelper::getHelpUrl('getbible');
		if (StringHelper::check($this->help_url))
		{
			ToolbarHelper::help('COM_GETBIBLE_HELP_MANAGER', false, $this->help_url);
		}

		if ($canDo->get('core.admin') || $canDo->get('core.options'))
		{
			ToolbarHelper::preferences('com_getbible');
		}
	}

	/**
	 * Prepare some document related stuff.
	 *
	 * @return  void
	 * @since   1.6
	 */
	protected function _prepareDocument(): void
	{
		// set page title
		$this->getDocument()->setTitle(Text::_('COM_GETBIBLE_DASHBOARD'));
		/** \Joomla\CMS\WebAsset\WebAssetManager $wa */
		$wa = $this->getDocument()->getWebAssetManager();
		// Register the inline script with properly encoded JSON
		$wa->addInlineScript(
			'var manifest = ' . json_encode($this->manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . ';'
		);
		// add styles
		foreach ($this->styles as $style)
		{
			Html::_('stylesheet', $style, ['version' => 'auto']);
		}
		// add scripts
		foreach ($this->scripts as $script)
		{
			Html::_('script', $script, ['version' => 'auto']);
		}
	}
}
