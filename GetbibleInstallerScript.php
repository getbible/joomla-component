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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Version;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\Filesystem\Folder;
use Joomla\Database\DatabaseInterface;

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Script File of Getbible Component
 *
 * @since  3.6
 */
class Com_GetbibleInstallerScript implements InstallerScriptInterface
{
	/**
	 * The CMS Application.
	 *
	 * @var   CMSApplication
	 * @since 4.4.2
	 */
	protected CMSApplication $app;

	/**
	 * The database class.
	 *
	 * @since 4.4.2
	 */
	protected $db;

	/**
	 * The version number of the extension.
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $release;

	/**
	 * The table the parameters are stored in.
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $paramTable;

	/**
	 * The extension name. This should be set in the installer script.
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $extension;

	/**
	 * A list of files to be deleted
	 *
	 * @var   array
	 * @since  3.6
	 */
	protected $deleteFiles = [];

	/**
	 * A list of folders to be deleted
	 *
	 * @var   array
	 * @since  3.6
	 */
	protected $deleteFolders = [];

	/**
	 * A list of CLI script files to be copied to the cli directory
	 *
	 * @var   array
	 * @since  3.6
	 */
	protected $cliScriptFiles = [];

	/**
	 * Minimum PHP version required to install the extension
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $minimumPhp;

	/**
	 * Minimum Joomla! version required to install the extension
	 *
	 * @var   string
	 * @since  3.6
	 */
	protected $minimumJoomla;

	/**
	 * Extension script constructor.
	 *
	 * @since   3.0.0
	 */
	public function __construct()
	{
		$this->minimumJoomla = '4.3';
		$this->minimumPhp = JOOMLA_MINIMUM_PHP;
		$this->app ??= Factory::getApplication();
		$this->db = Factory::getContainer()->get(DatabaseInterface::class);

		// check if the files exist
		if (is_file(JPATH_ROOT . '/administrator/components/com_getbible/getbible.php'))
		{
			// remove Joomla 3 files
			$this->deleteFiles = [
				'/administrator/components/com_getbible/getbible.php',
				'/administrator/components/com_getbible/controller.php',
				'/components/com_getbible/getbible.php',
				'/components/com_getbible/controller.php',
				'/components/com_getbible/router.php',
			];
		}

		// check if the Folders exist
		if (is_dir(JPATH_ROOT . '/administrator/components/com_getbible/modules'))
		{
			// remove Joomla 3 folder
			$this->deleteFolders = [
				'/administrator/components/com_getbible/controllers',
				'/administrator/components/com_getbible/helpers',
				'/administrator/components/com_getbible/modules',
				'/administrator/components/com_getbible/tables',
				'/administrator/components/com_getbible/views',
				'/components/com_getbible/controllers',
				'/components/com_getbible/helpers',
				'/components/com_getbible/modules',
				'/components/com_getbible/views',
			];
		}
	}

	/**
	 * Function called after the extension is installed.
	 *
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function install(InstallerAdapter $adapter): bool {return true;}

	/**
	 * Function called after the extension is updated.
	 *
	 * @param   InstallerAdapter   $adapter   The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function update(InstallerAdapter $adapter): bool {return true;}

	/**
	 * Function called after the extension is uninstalled.
	 *
	 * @param   InstallerAdapter   $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function uninstall(InstallerAdapter $adapter): bool
	{
		// Remove Related Component Data.

		// Remove Note Data
		$this->removeViewData("com_getbible.note");

		// Remove Tagged verse Data
		$this->removeViewData("com_getbible.tagged_verse");

		// Remove Prompt Data
		$this->removeViewData("com_getbible.prompt");

		// Remove Open ai response Data
		$this->removeViewData("com_getbible.open_ai_response");

		// Remove Open ai message Data
		$this->removeViewData("com_getbible.open_ai_message");

		// Remove Tag Data
		$this->removeViewData("com_getbible.tag");

		// Remove Asset Data.
		$this->removeAssetData();

		// Revert the assets table rules column back to the default.
		$this->removeDatabaseAssetsRulesFix();

		// Remove component from action logs extensions table.
		$this->removeActionLogsExtensions();

		// Remove Linker from action logs config table.
		$this->removeActionLogConfig('com_getbible.linker');

		// Remove Note from action logs config table.
		$this->removeActionLogConfig('com_getbible.note');

		// Remove Tagged_verse from action logs config table.
		$this->removeActionLogConfig('com_getbible.tagged_verse');

		// Remove Prompt from action logs config table.
		$this->removeActionLogConfig('com_getbible.prompt');

		// Remove Open_ai_response from action logs config table.
		$this->removeActionLogConfig('com_getbible.open_ai_response');

		// Remove Open_ai_message from action logs config table.
		$this->removeActionLogConfig('com_getbible.open_ai_message');

		// Remove Password from action logs config table.
		$this->removeActionLogConfig('com_getbible.password');

		// Remove Tag from action logs config table.
		$this->removeActionLogConfig('com_getbible.tag');

		// Remove Translation from action logs config table.
		$this->removeActionLogConfig('com_getbible.translation');

		// Remove Book from action logs config table.
		$this->removeActionLogConfig('com_getbible.book');

		// Remove Chapter from action logs config table.
		$this->removeActionLogConfig('com_getbible.chapter');

		// Remove Verse from action logs config table.
		$this->removeActionLogConfig('com_getbible.verse');
		// little notice as after service, in case of bad experience with component.
		echo '<div style="background-color: #fff;" class="alert alert-info">
		<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:joomla@vdm.io">joomla@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://getbible.net" target="_blank">https://getbible.net</a> today!</p></div>';

		return true;
	}

	/**
	 * Function called before extension installation/update/removal procedure commences.
	 *
	 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function preflight(string $type, InstallerAdapter $adapter): bool
	{
		// Check for the minimum PHP version before continuing
		if (!empty($this->minimumPhp) && version_compare(PHP_VERSION, $this->minimumPhp, '<'))
		{
			Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhp), Log::WARNING, 'jerror');

			return false;
		}

		// Check for the minimum Joomla version before continuing
		if (!empty($this->minimumJoomla) && version_compare(JVERSION, $this->minimumJoomla, '<'))
		{
			Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_JOOMLA', $this->minimumJoomla), Log::WARNING, 'jerror');

			return false;
		}

		// Extension manifest file version
		$this->extension = $adapter->getName();
		$this->release   = $adapter->getManifest()->version;

		// do any updates needed
		if ($type === 'update')
		{
		}

		// do any install needed
		if ($type === 'install')
		{
		}

		return true;
	}

	/**
	 * Function called after extension installation/update/removal procedure commences.
	 *
	 * @param   string            $type     The type of change (install or discover_install, update, uninstall)
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return  boolean  True on success
	 *
	 * @since   4.2.0
	 */
	public function postflight(string $type, InstallerAdapter $adapter): bool
	{
		// We check if we have dynamic folders to copy
		$this->moveFolders($adapter);

		// set the default component settings
		if ($type === 'install')
		{

			// Install Note Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Note',
				// typeAlias
				'com_getbible.note',
				// table
				'{"special": {"dbtable": "#__getbible_note","key": "id","type": "NoteTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","linker":"linker","guid":"guid","note":"note","verse":"verse","chapter":"chapter"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/note.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}'
			);
			// Install Tagged verse Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Tagged_verse',
				// typeAlias
				'com_getbible.tagged_verse',
				// table
				'{"special": {"dbtable": "#__getbible_tagged_verse","key": "id","type": "Tagged_verseTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","abbreviation":"abbreviation","linker":"linker","tag":"tag","guid":"guid","verse":"verse","chapter":"chapter"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/tagged_verse.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "tag","targetTable": "#__getbible_tag","targetColumn": "guid","displayColumn": "name"}]}'
			);
			// Install Prompt Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Prompt',
				// typeAlias
				'com_getbible.prompt',
				// table
				'{"special": {"dbtable": "#__getbible_prompt","key": "id","type": "PromptTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","integration":"integration","cache_behaviour":"cache_behaviour","abbreviation":"abbreviation","guid":"guid","model":"model","presence_penalty":"presence_penalty","org_token":"org_token","token":"token","n_override":"n_override","cache_capacity":"cache_capacity","response_retrieval":"response_retrieval","frequency_penalty_override":"frequency_penalty_override","n":"n","max_tokens_override":"max_tokens_override","token_override":"token_override","max_tokens":"max_tokens","ai_org_token_override":"ai_org_token_override","temperature_override":"temperature_override","presence_penalty_override":"presence_penalty_override","top_p_override":"top_p_override","frequency_penalty":"frequency_penalty","top_p":"top_p","temperature":"temperature"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/prompt.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","integration","cache_behaviour","n_override","cache_capacity","response_retrieval","frequency_penalty_override","n","max_tokens_override","token_override","max_tokens","ai_org_token_override","temperature_override","presence_penalty_override","top_p_override"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}'
			);
			// Install Open ai response Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Open_ai_response',
				// typeAlias
				'com_getbible.open_ai_response',
				// table
				'{"special": {"dbtable": "#__getbible_open_ai_response","key": "id","type": "Open_ai_responseTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "response_id","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"response_id":"response_id","prompt":"prompt","response_object":"response_object","response_model":"response_model","total_tokens":"total_tokens","n":"n","frequency_penalty":"frequency_penalty","presence_penalty":"presence_penalty","word":"word","chapter":"chapter","lcsh":"lcsh","completion_tokens":"completion_tokens","prompt_tokens":"prompt_tokens","response_created":"response_created","abbreviation":"abbreviation","language":"language","max_tokens":"max_tokens","book":"book","temperature":"temperature","verse":"verse","top_p":"top_p","selected_word":"selected_word","model":"model"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/open_ai_response.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","total_tokens","n","chapter","completion_tokens","prompt_tokens","max_tokens","book"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}'
			);
			// Install Open ai message Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Open_ai_message',
				// typeAlias
				'com_getbible.open_ai_message',
				// table
				'{"special": {"dbtable": "#__getbible_open_ai_message","key": "id","type": "Open_ai_messageTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "role","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"role":"role","open_ai_response":"open_ai_response","prompt":"prompt","source":"source","content":"content","name":"name","index":"index"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/open_ai_message.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","source","index"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "open_ai_response","targetTable": "#__getbible_open_ai_response","targetColumn": "response_id","displayColumn": "response_id"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"}]}'
			);
			// Install Tag Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Tag',
				// typeAlias
				'com_getbible.tag',
				// table
				'{"special": {"dbtable": "#__getbible_tag","key": "id","type": "TagTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","linker":"linker","guid":"guid","description":"description"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/tag.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","access"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}'
			);


			// Fix the assets table rules column size.
			$this->setDatabaseAssetsRulesFix(44480, "TEXT");
			// Install the global extension params.
			$this->setExtensionsParams(
				'{"autorName":"Llewellyn van der Merwe","autorEmail":"joomla@vdm.io","default_translation":"kjv","show_install_button":"0","show_getbible_logo":"1","show_getbible_link":"1","show_hash_validation":"1","show_api_link":"1","activate_search":"0","search_found_color":"#4747ff","table_selection_color":"#dfdfdf","search_words":"1","search_match":"1","search_case":"1","bottom_search_position":"div","show_bottom_search_position_card":"1","bottom_search_position_card_style":"default","activate_notes":"0","activate_tags":"0","allow_untagging":"0","bottom_tag_position":"div","show_bottom_tag_position_card":"1","bottom_tag_position_card_style":"default","activate_sharing":"1","verse_layout_share":"1","verse_number_share":"1","local_link_share":"1","text_reference_share":"3","type_translation_share":"2","default_format_share":"1","verse_selected_color":"#4747ff","show_header":"1","verse_per_line":"1","show_top_menu":"1","top_menu_type":"1","show_bottom_menu":"0","bottom_menu_type":"1","previous_next_navigation":"1","set_custom_tabs":"0","custom_tabs":"div","set_default_tab_names":"0","custom_icons":"0","show_scripture_tab_text":"1","show_scripture_icon":"1","show_scripture_card":"1","scripture_card_style":"default","show_books_tab_text":"1","show_books_icon":"1","show_books_card":"1","books_card_style":"default","show_chapters_tab_text":"1","show_chapters_icon":"1","show_chapters_card":"1","chapters_card_style":"default","show_translations_tab_text":"1","show_translations_icon":"1","show_translations_card":"1","translations_card_style":"default","show_settings":"0","show_settings_tab_text":"1","show_settings_icon":"1","show_settings_card":"1","settings_card_style":"default","show_details":"1","show_details_tab_text":"1","show_details_icon":"1","show_details_card":"1","details_card_style":"default","bottom_app_position":"div","show_bottom_app_position_card":"1","bottom_app_position_card_style":"default","debug":"0","enable_open_ai":"0","openai_model":"gpt-4","openai_token":"secret","enable_open_ai_org":"0","openai_org_token":"secret","openai_max_tokens":"300","openai_temperature":"1","openai_top_p":"1","openai_n":"1","openai_presence_penalty":"0","openai_frequency_penalty":"0","bottom_ai_position":"div","show_bottom_ai_position_card":"1","bottom_ai_position_card_style":"default","check_in":"-1 day","save_history":"1","history_limit":"10","titleContributor1":"Modules","nameContributor1":"CrossWire","emailContributor1":"sword-support@crosswire.org","linkContributor1":"https://wiki.crosswire.org/","useContributor1":"2","showContributor1":"3","add_jquery_framework":"1","uikit_load":"1","uikit_min":""}'
			);


			echo '<div style="background-color: #fff;" class="alert alert-info"><a target="_blank" href="https://getbible.net" title="Get Bible">
				<img src="components/com_getbible/assets/images/vdm-component.jpg"/>
				</a></div>';

			// Add component to the action logs extensions table.
			$this->setActionLogsExtensions();

			// Add Linker to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'LINKER',
				// typeAlias
				'com_getbible.linker',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_linker',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Note to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'NOTE',
				// typeAlias
				'com_getbible.note',
				// idHolder
				'id',
				// titleHolder
				'book_nr',
				// tableName
				'#__getbible_note',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Tagged_verse to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TAGGED_VERSE',
				// typeAlias
				'com_getbible.tagged_verse',
				// idHolder
				'id',
				// titleHolder
				'book_nr',
				// tableName
				'#__getbible_tagged_verse',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Prompt to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PROMPT',
				// typeAlias
				'com_getbible.prompt',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_prompt',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Open_ai_response to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'OPEN_AI_RESPONSE',
				// typeAlias
				'com_getbible.open_ai_response',
				// idHolder
				'id',
				// titleHolder
				'response_id',
				// tableName
				'#__getbible_open_ai_response',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Open_ai_message to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'OPEN_AI_MESSAGE',
				// typeAlias
				'com_getbible.open_ai_message',
				// idHolder
				'id',
				// titleHolder
				'role',
				// tableName
				'#__getbible_open_ai_message',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Password to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PASSWORD',
				// typeAlias
				'com_getbible.password',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_password',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Tag to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TAG',
				// typeAlias
				'com_getbible.tag',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_tag',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Translation to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TRANSLATION',
				// typeAlias
				'com_getbible.translation',
				// idHolder
				'id',
				// titleHolder
				'translation',
				// tableName
				'#__getbible_translation',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Book to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'BOOK',
				// typeAlias
				'com_getbible.book',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_book',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Chapter to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'CHAPTER',
				// typeAlias
				'com_getbible.chapter',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_chapter',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add Verse to the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'VERSE',
				// typeAlias
				'com_getbible.verse',
				// idHolder
				'id',
				// titleHolder
				'book_nr',
				// tableName
				'#__getbible_verse',
				// textPrefix
				'COM_GETBIBLE'
			);
		}

		// do any updates needed
		if ($type === 'update')
		{

			// Update Note Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Note',
				// typeAlias
				'com_getbible.note',
				// table
				'{"special": {"dbtable": "#__getbible_note","key": "id","type": "NoteTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","linker":"linker","guid":"guid","note":"note","verse":"verse","chapter":"chapter"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/note.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}'
			);
			// Update Tagged verse Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Tagged_verse',
				// typeAlias
				'com_getbible.tagged_verse',
				// table
				'{"special": {"dbtable": "#__getbible_tagged_verse","key": "id","type": "Tagged_verseTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "null","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"book_nr":"book_nr","abbreviation":"abbreviation","linker":"linker","tag":"tag","guid":"guid","verse":"verse","chapter":"chapter"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/tagged_verse.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","book_nr","access","verse","chapter"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "tag","targetTable": "#__getbible_tag","targetColumn": "guid","displayColumn": "name"}]}'
			);
			// Update Prompt Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Prompt',
				// typeAlias
				'com_getbible.prompt',
				// table
				'{"special": {"dbtable": "#__getbible_prompt","key": "id","type": "PromptTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","integration":"integration","cache_behaviour":"cache_behaviour","abbreviation":"abbreviation","guid":"guid","model":"model","presence_penalty":"presence_penalty","org_token":"org_token","token":"token","n_override":"n_override","cache_capacity":"cache_capacity","response_retrieval":"response_retrieval","frequency_penalty_override":"frequency_penalty_override","n":"n","max_tokens_override":"max_tokens_override","token_override":"token_override","max_tokens":"max_tokens","ai_org_token_override":"ai_org_token_override","temperature_override":"temperature_override","presence_penalty_override":"presence_penalty_override","top_p_override":"top_p_override","frequency_penalty":"frequency_penalty","top_p":"top_p","temperature":"temperature"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/prompt.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","integration","cache_behaviour","n_override","cache_capacity","response_retrieval","frequency_penalty_override","n","max_tokens_override","token_override","max_tokens","ai_org_token_override","temperature_override","presence_penalty_override","top_p_override"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}'
			);
			// Update Open ai response Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Open_ai_response',
				// typeAlias
				'com_getbible.open_ai_response',
				// table
				'{"special": {"dbtable": "#__getbible_open_ai_response","key": "id","type": "Open_ai_responseTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "response_id","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"response_id":"response_id","prompt":"prompt","response_object":"response_object","response_model":"response_model","total_tokens":"total_tokens","n":"n","frequency_penalty":"frequency_penalty","presence_penalty":"presence_penalty","word":"word","chapter":"chapter","lcsh":"lcsh","completion_tokens":"completion_tokens","prompt_tokens":"prompt_tokens","response_created":"response_created","abbreviation":"abbreviation","language":"language","max_tokens":"max_tokens","book":"book","temperature":"temperature","verse":"verse","top_p":"top_p","selected_word":"selected_word","model":"model"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/open_ai_response.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","total_tokens","n","chapter","completion_tokens","prompt_tokens","max_tokens","book"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"},{"sourceColumn": "abbreviation","targetTable": "#__getbible_translation","targetColumn": "abbreviation","displayColumn": "translation"}]}'
			);
			// Update Open ai message Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Open_ai_message',
				// typeAlias
				'com_getbible.open_ai_message',
				// table
				'{"special": {"dbtable": "#__getbible_open_ai_message","key": "id","type": "Open_ai_messageTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "role","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"role":"role","open_ai_response":"open_ai_response","prompt":"prompt","source":"source","content":"content","name":"name","index":"index"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/open_ai_message.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","source","index"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "open_ai_response","targetTable": "#__getbible_open_ai_response","targetColumn": "response_id","displayColumn": "response_id"},{"sourceColumn": "prompt","targetTable": "#__getbible_prompt","targetColumn": "guid","displayColumn": "name"}]}'
			);
			// Update Tag Content Types.
			$this->setContentType(
				// typeTitle
				'Getbible Tag',
				// typeAlias
				'com_getbible.tag',
				// table
				'{"special": {"dbtable": "#__getbible_tag","key": "id","type": "TagTable","prefix": "TrueChristianChurch\Component\Getbible\Administrator\Table"}}',
				// rules
				'',
				// fieldMappings
				'{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","linker":"linker","guid":"guid","description":"description"}}',
				// router
				'',
				// contentHistoryOptions
				'{"formFile": "administrator/components/com_getbible/forms/tag.xml","hideFields": ["asset_id","checked_out","checked_out_time"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","version","hits","access"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "linker","targetTable": "#__getbible_linker","targetColumn": "guid","displayColumn": "name"}]}'
			);



			echo '<div style="background-color: #fff;" class="alert alert-info"><a target="_blank" href="https://getbible.net" title="Get Bible">
				<img src="components/com_getbible/assets/images/vdm-component.jpg"/>
				</a>
				<h3>Upgrade to Version 4.0.10 Was Successful! Let us know if anything is not working as expected.</h3></div>';

			// Add/Update component in the action logs extensions table.
			$this->setActionLogsExtensions();

			// Add/Update Linker in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'LINKER',
				// typeAlias
				'com_getbible.linker',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_linker',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Note in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'NOTE',
				// typeAlias
				'com_getbible.note',
				// idHolder
				'id',
				// titleHolder
				'book_nr',
				// tableName
				'#__getbible_note',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Tagged_verse in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TAGGED_VERSE',
				// typeAlias
				'com_getbible.tagged_verse',
				// idHolder
				'id',
				// titleHolder
				'book_nr',
				// tableName
				'#__getbible_tagged_verse',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Prompt in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PROMPT',
				// typeAlias
				'com_getbible.prompt',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_prompt',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Open_ai_response in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'OPEN_AI_RESPONSE',
				// typeAlias
				'com_getbible.open_ai_response',
				// idHolder
				'id',
				// titleHolder
				'response_id',
				// tableName
				'#__getbible_open_ai_response',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Open_ai_message in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'OPEN_AI_MESSAGE',
				// typeAlias
				'com_getbible.open_ai_message',
				// idHolder
				'id',
				// titleHolder
				'role',
				// tableName
				'#__getbible_open_ai_message',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Password in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'PASSWORD',
				// typeAlias
				'com_getbible.password',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_password',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Tag in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TAG',
				// typeAlias
				'com_getbible.tag',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_tag',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Translation in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'TRANSLATION',
				// typeAlias
				'com_getbible.translation',
				// idHolder
				'id',
				// titleHolder
				'translation',
				// tableName
				'#__getbible_translation',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Book in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'BOOK',
				// typeAlias
				'com_getbible.book',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_book',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Chapter in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'CHAPTER',
				// typeAlias
				'com_getbible.chapter',
				// idHolder
				'id',
				// titleHolder
				'name',
				// tableName
				'#__getbible_chapter',
				// textPrefix
				'COM_GETBIBLE'
			);

			// Add/Update Verse in the action logs config table.
			$this->setActionLogConfig(
				// typeTitle
				'VERSE',
				// typeAlias
				'com_getbible.verse',
				// idHolder
				'id',
				// titleHolder
				'book_nr',
				// tableName
				'#__getbible_verse',
				// textPrefix
				'COM_GETBIBLE'
			);
		}

		// move CLI files
		$this->moveCliFiles();

		// remove old files and folders
		$this->removeFiles();

		return true;
	}

	/**
	 * Remove the files and folders in the given array from
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function removeFiles()
	{
		if (!empty($this->deleteFiles))
		{
			foreach ($this->deleteFiles as $file)
			{
				if (is_file(JPATH_ROOT . $file) && !File::delete(JPATH_ROOT . $file))
				{
					echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $file) . '<br>';
				}
			}
		}

		if (!empty($this->deleteFolders))
		{
			foreach ($this->deleteFolders as $folder)
			{
				if (is_dir(JPATH_ROOT . $folder) && !Folder::delete(JPATH_ROOT . $folder))
				{
					echo Text::sprintf('JLIB_INSTALLER_ERROR_FILE_FOLDER', $folder) . '<br>';
				}
			}
		}
	}

	/**
	 * Moves the CLI scripts into the CLI folder in the CMS
	 *
	 * @return  void
	 *
	 * @since   3.6
	 */
	protected function moveCliFiles()
	{
		if (!empty($this->cliScriptFiles))
		{
			foreach ($this->cliScriptFiles as $file)
			{
				$name = basename($file);

				if (file_exists(JPATH_ROOT . $file) && !File::move(JPATH_ROOT . $file, JPATH_ROOT . '/cli/' . $name))
				{
					echo Text::sprintf('JLIB_INSTALLER_FILE_ERROR_MOVE', $name);
				}
			}
		}
	}

	/**
	 * Set content type integration
	 *
	 * @param   string   $typeTitle
	 * @param   string   $typeAlias
	 * @param   string   $table
	 * @param   string   $rules
	 * @param   string   $fieldMappings
	 * @param   string   $router
	 * @param   string   $contentHistoryOptions
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setContentType(
		string $typeTitle,
		string $typeAlias,
		string $table,
		string $rules,
		string $fieldMappings,
		string $router,
		string $contentHistoryOptions): void
	{
		// Create the content type object.
		$content = new stdClass();
		$content->type_title = $typeTitle;
		$content->type_alias = $typeAlias;
		$content->table = $table;
		$content->rules = $rules;
		$content->field_mappings = $fieldMappings;
		$content->router = $router;
		$content->content_history_options = $contentHistoryOptions;

		// Check if content type is already in content_type DB.
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('type_id')));
		$query->from($this->db->quoteName('#__content_types'));
		$query->where($this->db->quoteName('type_alias') . ' LIKE '. $this->db->quote($content->type_alias));

		$this->db->setQuery($query);
		$this->db->execute();

		// Check if the type alias is already in the content types table.
		if ($this->db->getNumRows())
		{
			$content->type_id = $this->db->loadResult();
			if ($this->db->updateObject('#__content_types', $content, 'type_id'))
			{
				// If its successfully update.
				$this->app->enqueueMessage(
					Text::sprintf('The (%s) was found in the <b>#__content_types</b> table, and updated.', $content->type_alias)
				);
			}
		}
		elseif ($this->db->insertObject('#__content_types', $content))
		{
			// If its successfully added.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) was added to the <b>#__content_types</b> table.', $content->type_alias)
			);
		}
	}

	/**
	 * Set action log config integration
	 *
	 * @param   string   $typeTitle
	 * @param   string   $typeAlias
	 * @param   string   $idHolder
	 * @param   string   $titleHolder
	 * @param   string   $tableName
	 * @param   string   $textPrefix
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setActionLogConfig(
		string $typeTitle,
		string $typeAlias,
		string $idHolder,
		string $titleHolder,
		string $tableName,
		string $textPrefix): void
	{
		// Create the content action log config object.
		$content = new stdClass();
		$content->type_title = $typeTitle;
		$content->type_alias = $typeAlias;
		$content->id_holder = $idHolder;
		$content->title_holder = $titleHolder;
		$content->table_name = $tableName;
		$content->text_prefix = $textPrefix;

		// Check if the action log config is already in action_log_config DB.
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(['id']));
		$query->from($this->db->quoteName('#__action_log_config'));
		$query->where($this->db->quoteName('type_alias') . ' LIKE '. $this->db->quote($content->type_alias));

		$this->db->setQuery($query);
		$this->db->execute();

		// Check if the type alias is already in the action log config table.
		if ($this->db->getNumRows())
		{
			$content->id = $this->db->loadResult();
			if ($this->db->updateObject('#__action_log_config', $content, 'id'))
			{
				// If its successfully update.
				$this->app->enqueueMessage(
					Text::sprintf('The (%s) was found in the <b>#__action_log_config</b> table, and updated.', $content->type_alias)
				);
			}
		}
		elseif ($this->db->insertObject('#__action_log_config', $content))
		{
			// If its successfully added.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) was added to the <b>#__action_log_config</b> table.', $content->type_alias)
			);
		}
	}

	/**
	 * Set action logs extensions integration
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setActionLogsExtensions(): void
	{
		// Create the extension action logs object.
		$data = new stdClass();
		$data->extension = 'com_getbible';

		// Check if getbible action log extension is already in action logs extensions DB.
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(['id']));
		$query->from($this->db->quoteName('#__action_logs_extensions'));
		$query->where($this->db->quoteName('extension') . ' = '. $this->db->quote($data->extension));

		$this->db->setQuery($query);
		$this->db->execute();

		// Set the object into the action logs extensions table if not found.
		if ($this->db->getNumRows())
		{
			// If its already set don't set it again.
			$this->app->enqueueMessage(
				Text::_('The (com_getbible) is already in the <b>#__action_logs_extensions</b> table.')
			);
		}
		elseif ($this->db->insertObject('#__action_logs_extensions', $data))
		{
			// give a success message
			$this->app->enqueueMessage(
				Text::_('The (com_getbible) was successfully added to the <b>#__action_logs_extensions</b> table.')
			);
		}
	}

	/**
	 * Set global extension assets permission of this component
	 *   (on install only)
	 *
	 * @param   string   $rules   The component rules
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setAssetsRules(string $rules): void
	{
		// Condition.
		$conditions = [
			$this->db->quoteName('name') . ' = ' . $this->db->quote('com_getbible')
		];

		// Field to update.
		$fields = [
			$this->db->quoteName('rules') . ' = ' . $this->db->quote($rules),
		];

		$query = $this->db->getQuery(true);
		$query->update(
			$this->db->quoteName('#__assets')
		)->set($fields)->where($conditions);

		$this->db->setQuery($query);

		$done = $this->db->execute();
		if ($done)
		{
			// give a success message
			$this->app->enqueueMessage(
				Text::_('The (com_getbible) rules was successfully added to the <b>#__assets</b> table.')
			);
		}
	}

	/**
	 * Set global extension params of this component
	 *   (on install only)
	 *
	 * @param   string   $params   The component rules
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setExtensionsParams(string $params): void
	{
		// Condition.
		$conditions = [
			$this->db->quoteName('element') . ' = ' . $this->db->quote('com_getbible')
		];

		// Field to update.
		$fields = [
			$this->db->quoteName('params') . ' = ' . $this->db->quote($params),
		];

		$query = $this->db->getQuery(true);
		$query->update(
			$this->db->quoteName('#__extensions')
		)->set($fields)->where($conditions);

		$this->db->setQuery($query);

		$done = $this->db->execute();
		if ($done)
		{
			// give a success message
			$this->app->enqueueMessage(
				Text::_('The (com_getbible) params was successfully added to the <b>#__extensions</b> table.')
			);
		}
	}

	/**
	 * Set database fix (if needed)
	 *  => WHY DO WE NEED AN ASSET TABLE FIX?
	 *   https://git.vdm.dev/joomla/Component-Builder/issues/616#issuecomment-12085
	 *   https://www.mysqltutorial.org/mysql-varchar/
	 *   https://stackoverflow.com/a/15227917/1429677
	 *   https://forums.mysql.com/read.php?24,105964,105964
	 *
	 * @param   int     $accessWorseCase   This is the max rules column size com_getbible would needs.
	 * @param   string  $dataType          This datatype we will change the rules column to if it to small.
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function setDatabaseAssetsRulesFix(int $accessWorseCase, string $dataType): void
	{
		// Get the biggest rule column in the assets table at this point.
		$length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$this->db->setQuery($length);
		if ($this->db->execute())
		{
			$rule_length = $this->db->loadResult();
			// Check the size of the rules column
			if ($rule_length <= $accessWorseCase)
			{
				// Fix the assets table rules column size
				$fix = "ALTER TABLE `#__assets` CHANGE `rules` `rules` {$dataType} NOT NULL COMMENT 'JSON encoded access control. Enlarged to {$dataType} by Getbible';";
				$this->db->setQuery($fix);

				$done = $this->db->execute();
				if ($done)
				{
					$this->app->enqueueMessage(
						Text::sprintf('The <b>#__assets</b> table rules column was resized to the %s datatype for the components possible large permission rules.', $dataType)
					);
				}
			}
		}
	}

	/**
	 * Remove remnant data related to this view
	 *
	 * @param   string   $context   The view context
	 * @param   bool     $fields    The switch to also remove related field data
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeViewData(string $context, bool $fields = false): void
	{
		$this->removeContentTypes($context);
		$this->removeViewHistory($context);
		$this->removeUcmContent($context); // this might be obsolete...
		$this->removeContentItemTagMap($context);
		$this->removeActionLogConfig($context);

		if ($fields)
		{
			$this->removeFields($context);
			$this->removeFieldsGroups($context);
		}
	}

	/**
	 * Remove content types related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeContentTypes(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Select id from content type table
		$query->select($this->db->quoteName('type_id'));
		$query->from($this->db->quoteName('#__content_types'));

		// Where Item alias is found
		$query->where($this->db->quoteName('type_alias') . ' = '. $this->db->quote($context));
		$this->db->setQuery($query);

		// Execute query to see if alias is found
		$this->db->execute();
		$found = $this->db->getNumRows();

		// Now check if there were any rows
		if ($found)
		{
			// Since there are load the needed  item type ids
			$ids = $this->db->loadColumn();

			// Remove Item from the content type table
			$condition = [
				$this->db->quoteName('type_alias') . ' = '. $this->db->quote($context)
			];

			// Create a new query object.
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quoteName('#__content_types'));
			$query->where($condition);
			$this->db->setQuery($query);

			// Execute the query to remove Item items
			$done = $this->db->execute();
			if ($done)
			{
				// If successfully remove Item add queued success message.
				$this->app->enqueueMessage(
					Text::sprintf('The (%s) type alias was removed from the <b>#__content_type</b> table.', $context)
				);
			}

			// Make sure that all the items are cleared from DB
			$this->removeUcmBase($ids);
		}
	}

	/**
	 * Remove fields related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeFields(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Select ids from fields
		$query->select($this->db->quoteName('id'));
		$query->from($this->db->quoteName('#__fields'));

		// Where context is found
		$query->where(
			$this->db->quoteName('context') . ' = '. $this->db->quote($context)
		);
		$this->db->setQuery($query);

		// Execute query to see if context is found
		$this->db->execute();
		$found = $this->db->getNumRows();

		// Now check if there were any rows
		if ($found)
		{
			// Since there are load the needed  release_check field ids
			$ids = $this->db->loadColumn();

			// Create a new query object.
			$query = $this->db->getQuery(true);

			// Remove context from the field table
			$condition = [
				$this->db->quoteName('context') . ' = '. $this->db->quote($context)
			];

			$query->delete($this->db->quoteName('#__fields'));
			$query->where($condition);

			$this->db->setQuery($query);

			// Execute the query to remove release_check items
			$done = $this->db->execute();
			if ($done)
			{
				// If successfully remove context add queued success message.
				$this->app->enqueueMessage(
					Text::sprintf('The fields with context (%s) was removed from the <b>#__fields</b> table.', $context)
				);
			}

			// Make sure that all the field values are cleared from DB
			$this->removeFieldsValues($context, $ids);
		}
	}

	/**
	 * Remove fields values related to fields
	 *
	 * @param   string   $context   The view context
	 * @param   array    $ids       The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeFieldsValues(string $context, array $ids): void
	{
		$condition = [
			$this->db->quoteName('field_id') . ' IN ('. implode(',', $ids) .')'
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__fields_values'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove field values
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully remove release_check add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The fields values for (%s) was removed from the <b>#__fields_values</b> table.', $context)
			);
		}
	}

	/**
	 * Remove fields groups related to fields
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeFieldsGroups(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Select ids from fields
		$query->select($this->db->quoteName('id'));
		$query->from($this->db->quoteName('#__fields_groups'));

		// Where context is found
		$query->where(
			$this->db->quoteName('context') . ' = '. $this->db->quote($context)
		);
		$this->db->setQuery($query);

		// Execute query to see if context is found
		$this->db->execute();
		$found = $this->db->getNumRows();

		// Now check if there were any rows
		if ($found)
		{
			// Create a new query object.
			$query = $this->db->getQuery(true);

			// Remove context from the field table
			$condition = [
				$this->db->quoteName('context') . ' = '. $this->db->quote($context)
			];

			$query->delete($this->db->quoteName('#__fields_groups'));
			$query->where($condition);

			$this->db->setQuery($query);

			// Execute the query to remove release_check items
			$done = $this->db->execute();
			if ($done)
			{
				// If successfully remove context add queued success message.
				$this->app->enqueueMessage(
					Text::sprintf('The fields with context (%s) was removed from the <b>#__fields_groups</b> table.', $context)
				);
			}
		}
	}

	/**
	 * Remove history related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeViewHistory(string $context): void
	{
		// Remove Item items from the ucm content table
		$condition = [
			$this->db->quoteName('item_id') . ' LIKE ' . $this->db->quote($context . '.%')
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__history'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove Item items
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed Items add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) items were removed from the <b>#__history</b> table.', $context)
			);
		}
	}

	/**
	 * Remove ucm base values related to these IDs
	 *
	 * @param   array   $ids   The type ids
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeUcmBase(array $ids): void
	{
		// Make sure that all the items are cleared from DB
		foreach ($ids as $type_id)
		{
			// Remove Item items from the ucm base table
			$condition = [
				$this->db->quoteName('ucm_type_id') . ' = ' . $type_id
			];

			// Create a new query object.
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quoteName('#__ucm_base'));
			$query->where($condition);
			$this->db->setQuery($query);

			// Execute the query to remove Item items
			$this->db->execute();
		}

		$this->app->enqueueMessage(
			Text::_('All related items was removed from the <b>#__ucm_base</b> table.')
		);
	}

	/**
	 * Remove ucm content values related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeUcmContent(string $context): void
	{
		// Remove Item items from the ucm content table
		$condition = [
			$this->db->quoteName('core_type_alias') . ' = ' . $this->db->quote($context)
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__ucm_content'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove Item items
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed Item add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) type alias was removed from the <b>#__ucm_content</b> table.', $context)
			);
		}
	}

	/**
	 * Remove content item tag map related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeContentItemTagMap(string $context): void
	{
		// Create a new query object.
		$query = $this->db->getQuery(true);

		// Remove Item items from the contentitem tag map table
		$condition = [
			$this->db->quoteName('type_alias') . ' = '. $this->db->quote($context)
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__contentitem_tag_map'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove Item items
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully remove Item add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) type alias was removed from the <b>#__contentitem_tag_map</b> table.', $context)
			);
		}
	}

	/**
	 * Remove action log config related to this view
	 *
	 * @param   string   $context   The view context
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeActionLogConfig(string $context): void
	{
		// Remove getbible view from the action_log_config table
		$condition = [
			$this->db->quoteName('type_alias') . ' = '. $this->db->quote($context)
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__action_log_config'));
		$query->where($condition);
		$this->db->setQuery($query);

		// Execute the query to remove com_getbible.view
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed getbible view add queued success message.
			$this->app->enqueueMessage(
				Text::sprintf('The (%s) type alias was removed from the <b>#__action_log_config</b> table.', $context)
			);
		}
	}

	/**
	 * Remove Asset Table Integrated
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeAssetData(): void
	{
		// Remove getbible assets from the assets table
		$condition = [
			$this->db->quoteName('name') . ' LIKE ' . $this->db->quote('com_getbible.%')
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__assets'));
		$query->where($condition);
		$this->db->setQuery($query);
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully removed getbible add queued success message.
			$this->app->enqueueMessage(
				Text::_('All related (com_getbible) items was removed from the <b>#__assets</b> table.')
			);
		}
	}

	/**
	 * Remove action logs extensions integrated
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeActionLogsExtensions(): void
	{
		// Remove getbible from the action_logs_extensions table
		$extension = [
			$this->db->quoteName('extension') . ' = ' . $this->db->quote('com_getbible')
		];

		// Create a new query object.
		$query = $this->db->getQuery(true);
		$query->delete($this->db->quoteName('#__action_logs_extensions'));
		$query->where($extension);
		$this->db->setQuery($query);

		// Execute the query to remove getbible
		$done = $this->db->execute();
		if ($done)
		{
			// If successfully remove getbible add queued success message.
			$this->app->enqueueMessage(
				Text::_('The (com_getbible) extension was removed from the <b>#__action_logs_extensions</b> table.')
			);
		}
	}

	/**
	 * Remove remove database fix (if possible)
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function removeDatabaseAssetsRulesFix(): void
	{
		// Get the biggest rule column in the assets table at this point.
		$length = "SELECT CHAR_LENGTH(`rules`) as rule_size FROM #__assets ORDER BY rule_size DESC LIMIT 1";
		$this->db->setQuery($length);
		if ($this->db->execute())
		{
			$rule_length = $this->db->loadResult();
			// Check the size of the rules column
			if ($rule_length < 5120)
			{
				// Revert the assets table rules column back to the default
				$revert_rule = "ALTER TABLE `#__assets` CHANGE `rules` `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.';";
				$this->db->setQuery($revert_rule);
				$this->db->execute();

				$this->app->enqueueMessage(
					Text::_('Reverted the <b>#__assets</b> table rules column back to its default size of varchar(5120).')
				);
			}
			else
			{
				$this->app->enqueueMessage(
					Text::_('Could not revert the <b>#__assets</b> table rules column back to its default size of varchar(5120), since there is still one or more components that still requires the column to be larger.')
				);
			}
		}
	}

	/**
	 * Method to move folders into place.
	 *
	 * @param   InstallerAdapter  $adapter  The adapter calling this method
	 *
	 * @return void
	 * @since 4.4.2
	 */
	protected function moveFolders(InstallerAdapter $adapter): void
	{
		// get the installation path
		$installer = $adapter->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = Folder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = ['media','admin','site']; // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!Folder::copy($src, $dest, '', true))
					{
						$this->app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
