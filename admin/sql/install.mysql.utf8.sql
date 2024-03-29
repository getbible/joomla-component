SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `#__getbible_linker` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`guid` VARCHAR(36) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`public_notes` TINYINT(1) NOT NULL DEFAULT 0,
	`public_tagged_verses` TINYINT(1) NOT NULL DEFAULT 0,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_guid` (`guid`),
	KEY `idx_public_tagged_verses` (`public_tagged_verses`),
	KEY `idx_public_notes` (`public_notes`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_note` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`access` TINYINT(1) NOT NULL DEFAULT 0,
	`book_nr` INT(7) NOT NULL DEFAULT 0,
	`chapter` INT(7) NOT NULL DEFAULT 0,
	`guid` VARCHAR(36) NOT NULL DEFAULT '',
	`linker` VARCHAR(36) NOT NULL DEFAULT '',
	`note` TEXT NOT NULL,
	`verse` INT(7) NOT NULL DEFAULT 0,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_book_nr` (`book_nr`),
	KEY `idx_linker` (`linker`),
	KEY `idx_guid` (`guid`),
	KEY `idx_verse` (`verse`),
	KEY `idx_chapter` (`chapter`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_tagged_verse` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(100) NOT NULL DEFAULT '',
	`access` TINYINT(1) NOT NULL DEFAULT 0,
	`book_nr` INT(7) NOT NULL DEFAULT 0,
	`chapter` INT(7) NOT NULL DEFAULT 0,
	`guid` VARCHAR(36) NOT NULL DEFAULT '',
	`linker` VARCHAR(36) NOT NULL DEFAULT '',
	`tag` VARCHAR(36) NOT NULL DEFAULT '',
	`verse` INT(7) NOT NULL DEFAULT 0,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_book_nr` (`book_nr`),
	KEY `idx_abbreviation` (`abbreviation`),
	KEY `idx_linker` (`linker`),
	KEY `idx_tag` (`tag`),
	KEY `idx_guid` (`guid`),
	KEY `idx_verse` (`verse`),
	KEY `idx_chapter` (`chapter`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_prompt` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(100) NOT NULL DEFAULT '',
	`ai_org_token_override` TINYINT(1) NOT NULL DEFAULT 0,
	`cache_behaviour` TINYINT(1) NOT NULL DEFAULT 0,
	`cache_capacity` INT(11) NOT NULL DEFAULT 0,
	`frequency_penalty` FLOAT(11) NOT NULL DEFAULT 0,
	`frequency_penalty_override` TINYINT(1) NOT NULL DEFAULT 0,
	`guid` VARCHAR(36) NOT NULL DEFAULT '',
	`integration` TINYINT(1) NOT NULL DEFAULT 1,
	`max_tokens` INT(11) NOT NULL DEFAULT 0,
	`max_tokens_override` TINYINT(1) NOT NULL DEFAULT 0,
	`messages` TEXT NOT NULL,
	`model` VARCHAR(50) NOT NULL DEFAULT '',
	`n` INT(7) NOT NULL DEFAULT 0,
	`n_override` TINYINT(1) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`org_token` VARCHAR(100) NOT NULL DEFAULT '',
	`presence_penalty` FLOAT(11) NOT NULL DEFAULT 0,
	`presence_penalty_override` TINYINT(1) NOT NULL DEFAULT 0,
	`response_retrieval` TINYINT(1) NOT NULL DEFAULT 0,
	`temperature` FLOAT(11) NOT NULL DEFAULT 0,
	`temperature_override` TINYINT(1) NOT NULL DEFAULT 0,
	`token` VARCHAR(100) NOT NULL DEFAULT '',
	`token_override` TINYINT(1) NOT NULL DEFAULT 0,
	`top_p` FLOAT(11) NOT NULL DEFAULT 0,
	`top_p_override` TINYINT(1) NOT NULL DEFAULT 0,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_integration` (`integration`),
	KEY `idx_cache_behaviour` (`cache_behaviour`),
	KEY `idx_abbreviation` (`abbreviation`),
	KEY `idx_guid` (`guid`),
	KEY `idx_model` (`model`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_open_ai_response` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(100) NOT NULL DEFAULT '',
	`book` INT(11) NOT NULL DEFAULT 0,
	`chapter` INT(11) NOT NULL DEFAULT 0,
	`completion_tokens` INT(11) NOT NULL DEFAULT 0,
	`frequency_penalty` FLOAT(11) NOT NULL DEFAULT 0,
	`language` VARCHAR(255) NOT NULL DEFAULT '',
	`lcsh` VARCHAR(255) NOT NULL DEFAULT '',
	`max_tokens` INT(11) NOT NULL DEFAULT 0,
	`model` VARCHAR(50) NOT NULL DEFAULT '',
	`n` INT(7) NOT NULL DEFAULT 0,
	`presence_penalty` FLOAT(11) NOT NULL DEFAULT 0,
	`prompt` VARCHAR(36) NOT NULL DEFAULT '',
	`prompt_tokens` INT(11) NOT NULL DEFAULT 0,
	`response_created` VARCHAR(255) NOT NULL DEFAULT '',
	`response_id` VARCHAR(255) NOT NULL DEFAULT '',
	`response_model` VARCHAR(255) NOT NULL DEFAULT '',
	`response_object` VARCHAR(255) NOT NULL DEFAULT '',
	`selected_word` TEXT NOT NULL,
	`temperature` FLOAT(11) NOT NULL DEFAULT 0,
	`top_p` FLOAT(11) NOT NULL DEFAULT 0,
	`total_tokens` INT(11) NOT NULL DEFAULT 0,
	`verse` VARCHAR(255) NOT NULL DEFAULT '',
	`word` VARCHAR(255) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_response_id` (`response_id`),
	KEY `idx_prompt` (`prompt`),
	KEY `idx_response_object` (`response_object`),
	KEY `idx_response_model` (`response_model`),
	KEY `idx_total_tokens` (`total_tokens`),
	KEY `idx_word` (`word`),
	KEY `idx_chapter` (`chapter`),
	KEY `idx_lcsh` (`lcsh`),
	KEY `idx_completion_tokens` (`completion_tokens`),
	KEY `idx_prompt_tokens` (`prompt_tokens`),
	KEY `idx_response_created` (`response_created`),
	KEY `idx_abbreviation` (`abbreviation`),
	KEY `idx_language` (`language`),
	KEY `idx_book` (`book`),
	KEY `idx_verse` (`verse`),
	KEY `idx_model` (`model`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_open_ai_message` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`content` TEXT NOT NULL,
	`index` INT(11) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`open_ai_response` VARCHAR(255) NOT NULL DEFAULT '',
	`prompt` VARCHAR(36) NOT NULL DEFAULT '',
	`role` VARCHAR(255) NOT NULL DEFAULT '',
	`source` TINYINT(1) NOT NULL DEFAULT 0,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_role` (`role`),
	KEY `idx_open_ai_response` (`open_ai_response`),
	KEY `idx_prompt` (`prompt`),
	KEY `idx_source` (`source`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_password` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`guid` VARCHAR(36) NOT NULL DEFAULT '',
	`linker` VARCHAR(36) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`password` VARCHAR(100) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_linker` (`linker`),
	KEY `idx_guid` (`guid`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_tag` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`access` TINYINT(1) NOT NULL DEFAULT 0,
	`description` TEXT NULL,
	`guid` VARCHAR(36) NOT NULL DEFAULT '',
	`linker` VARCHAR(36) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_linker` (`linker`),
	KEY `idx_guid` (`guid`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_translation` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(64) NOT NULL DEFAULT '',
	`direction` VARCHAR(64) NOT NULL DEFAULT '',
	`distribution_abbreviation` VARCHAR(64) NOT NULL DEFAULT '',
	`distribution_about` TEXT NOT NULL,
	`distribution_history` TEXT NOT NULL,
	`distribution_lcsh` VARCHAR(64) NOT NULL DEFAULT '',
	`distribution_license` TEXT NOT NULL,
	`distribution_source` TEXT NOT NULL,
	`distribution_sourcetype` VARCHAR(255) NOT NULL DEFAULT '',
	`distribution_versification` VARCHAR(64) NOT NULL DEFAULT '',
	`distribution_version` VARCHAR(64) NOT NULL DEFAULT '',
	`distribution_version_date` VARCHAR(64) NOT NULL DEFAULT '',
	`encoding` VARCHAR(64) NOT NULL DEFAULT '',
	`lang` VARCHAR(255) NOT NULL DEFAULT '',
	`language` VARCHAR(100) NOT NULL DEFAULT '',
	`sha` VARCHAR(64) NOT NULL DEFAULT '',
	`translation` VARCHAR(255) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_translation` (`translation`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_book` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(100) NOT NULL DEFAULT '',
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`nr` INT(7) NOT NULL DEFAULT 0,
	`sha` VARCHAR(64) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_abbreviation` (`abbreviation`),
	KEY `idx_nr` (`nr`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_chapter` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(100) NOT NULL DEFAULT '',
	`book_nr` INT(7) NOT NULL DEFAULT 0,
	`chapter` INT(7) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`sha` VARCHAR(64) NOT NULL DEFAULT '',
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_name` (`name`),
	KEY `idx_chapter` (`chapter`),
	KEY `idx_book_nr` (`book_nr`),
	KEY `idx_abbreviation` (`abbreviation`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__getbible_verse` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`asset_id` INT(10) unsigned NOT NULL DEFAULT 0 COMMENT 'FK to the #__assets table.',
	`abbreviation` VARCHAR(100) NOT NULL DEFAULT '',
	`book_nr` INT(7) NOT NULL DEFAULT 0,
	`chapter` INT(7) NOT NULL DEFAULT 0,
	`name` VARCHAR(255) NOT NULL DEFAULT '',
	`text` TEXT NOT NULL,
	`verse` INT(7) NOT NULL DEFAULT 0,
	`params` text NULL,
	`published` TINYINT(3) NOT NULL DEFAULT 1,
	`created_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`modified_by` INT(10) unsigned NOT NULL DEFAULT 0,
	`created` DATETIME DEFAULT CURRENT_TIMESTAMP,
	`modified` DATETIME DEFAULT NULL,
	`checked_out` int(11) unsigned NOT NULL DEFAULT 0,
	`checked_out_time` DATETIME DEFAULT NULL,
	`version` INT(10) unsigned NOT NULL DEFAULT 1,
	`hits` INT(10) unsigned NOT NULL DEFAULT 0,
	`access` INT(10) unsigned NOT NULL DEFAULT 0,
	`ordering` INT(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `idx_book_nr` (`book_nr`),
	KEY `idx_chapter` (`chapter`),
	KEY `idx_verse` (`verse`),
	KEY `idx_abbreviation` (`abbreviation`),
	KEY `idx_name` (`name`),
	KEY `idx_access` (`access`),
	KEY `idx_checkout` (`checked_out`),
	KEY `idx_createdby` (`created_by`),
	KEY `idx_modifiedby` (`modified_by`),
	KEY `idx_state` (`published`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `#__getbible_tag`
--

INSERT INTO `#__getbible_tag` (`id`, `access`, `description`, `guid`, `name`, `published`, `created`) VALUES
(1, 1, 'Understanding unfaithfulness in marriage.', 'a1d263b6-3848-4ac1-92e8-df7c2b78c649', 'Adultery', 1, '2015-07-16 17:49:49'),
(2, 1, 'Exploring reasons for believing in the Bible.', 'b5fb8c11-c3df-4925-92e8-df7c2b78c649', 'Authority of the Bible', 1, '2015-07-14 15:35:40'),
(3, 1, '', '8f6d2789-7ecf-4769-87b7-af564b59f7d5', 'Baptism', 1, '2015-07-16 17:41:24'),
(4, 1, 'Understanding the biblical definition of love.', '6493a897-755a-4b6c-8b7e-ff8b12d3e9c0', 'Biblical Love', 1, '2015-07-14 06:11:55'),
(5, 1, 'Exploring the concept of blessings and curses.', '17df4ce3-289a-4920-b96e-65f64932b9c1', 'Blessings & Curses', 1, '2015-07-15 17:20:13'),
(6, 1, 'Guidance on modesty and decency in attire.', '04c7f6b3-17df-4f69-9a1a-bec6913a4b0f', 'Christian Clothing', 1, '2015-07-14 12:15:12'),
(7, 1, 'Roles and positions held within the structure of the church.', '54df67b4-fd6c-4bb3-bc8a-81b4a52c1843', 'Christian Offices', 1, '2015-07-15 18:12:40'),
(8, 1, 'The consideration of the table of the Lord.', '5dfb8c11-6493-4bb3-96c1-f3a6b5c5f9f0', 'Communion', 1, '2015-01-24 03:02:47'),
(9, 1, '', '3dfdc8b8-6549-40d8-a8f1-65fa96329ab2', 'Conditional Security', 1, '2015-07-15 17:52:41'),
(10, 1, 'The correct understanding of relationships and courtship.', '755ae009-f9fa-4f69-8b5b-9c2b46a6cb85', 'Dating', 1, '2015-07-14 13:59:28'),
(11, 1, 'Exploring dietary rules and guidance.', '755ae009-8f6d-4fb6-9b5b-c6a6cb85f9f0', 'Dietary Guidance', 1, '2015-07-14 06:50:49'),
(12, 1, 'Understanding chastening or discipline.', 'e84a2c7a-f234-44c2-87b7-ff8b46d2f6e7', 'Discipline', 1, '2015-07-17 13:54:50'),
(13, 1, 'Exploring homeschooling.', 'f2346bc8-04c7-4f69-9a1a-bec6913a4b0f', 'Education', 1, '2015-07-14 11:53:11'),
(14, 1, '', '5dfb8c11-6493-4769-9a1b-bec6a6c5f7d5', 'Effective Prayer', 1, '2015-07-14 06:04:18'),
(15, 1, 'Exploring family size from a Christian perspective.', 'c3dfb8e7-d234-473b-8af1-f67394c5b1d3', 'Family Planning', 1, '2015-02-01 14:08:35'),
(16, 1, 'Exploring the observance of Sabbath or Sunday worship.', 'd2346fd5-e84a-43c6-94de-af432c2f0ab1', 'First Day', 1, '2015-07-17 13:36:30'),
(17, 1, 'Exploring the implications of insincere praise or flattery.', '39bcf6b7-44df-4077-8af1-81b4a5c63f19', 'Flattery', 1, '2015-07-14 06:01:43'),
(18, 1, 'Exploring the doctrine of free will.', '39bcf6b7-44df-473b-973e-af564b2f0ab1', 'Free Will', 1, '2015-07-14 12:59:51'),
(19, 1, 'Understanding God\'s righteous judgment against sin.', '5dfb8c11-6fd6-473b-973e-af564b2f0ab1', 'God\'s Judgement', 1, '2015-07-16 17:10:24'),
(20, 1, 'The unmerited favour and love given by God.', 'e84a2c7a-467b-46a8-973e-ff8b46d2f6e7', 'Grace', 1, '2015-07-14 05:06:40'),
(21, 1, 'Exploring the concept of church gatherings in homes.', 'c3dfb8e7-4ea8-4925-96c1-f3a621a4b9b0', 'Home Church', 1, '2015-07-17 13:18:40'),
(22, 1, 'Reflecting on God\'s immutability, His unchanging nature.', '44df4ce6-5e9f-4077-8af1-81b4a5c63f19', 'Immutability', 1, '2015-07-14 13:05:39'),
(23, 1, 'Exploring the divine nature of Jesus Christ.', '1a0ad6e8-35fa-4f2c-889f-9ca2b057bbf0', 'Jesus Christ\'s Deity', 1, '2015-01-16 14:23:26'),
(24, 1, 'Exploring the reality that Jesus Christ came in the flesh.', 'e84a2c7a-f234-43c6-94de-af432c2f0ab1', 'Jesus Christ\'s Humanity', 1, '2015-07-14 16:17:54'),
(25, 1, 'Exploring the biblical view of leadership.', '6493a897-7e2a-4bb3-96c1-f3a6b5c5f9f0', 'Leadership', 1, '2015-07-14 13:51:25'),
(26, 1, 'Exploring the essence of life.', '04c7f6b3-17df-4920-b96e-65f64932b9c1', 'What is Life', 1, '2015-12-03 23:26:31'),
(27, 1, 'Exploring what gives one a long life.', '17df4ce3-289a-437b-88d1-9c2b057ba1a0', 'Longevity', 1, '2015-07-14 12:32:43'),
(28, 1, 'Exploring the roles and responsibilities of men.', 'c3dfb8e7-d234-46a8-96c5-f3a6a6c5f9f0', 'Man\'s Role', 1, '2015-07-14 10:41:41'),
(29, 1, 'Beliefs and teachings on the sacrament of marriage.', 'b5fb8c11-dfb7-4fb6-aa4b-e982a12d3e9c', 'Marriage', 1, '2015-07-14 10:24:11'),
(30, 1, 'Understanding the destructive and harmful influence of music.', '44df4ce6-5dfb-473b-973e-af564b2f0ab1', 'Music\'s Influence', 1, '2015-01-18 12:16:37'),
(31, 1, 'The correct motive to avoid company or fellowship.', '755ae009-8f6d-4769-9a1b-bec6a6c5f7d5', 'No Fellowship', 1, '2015-07-14 06:21:42'),
(32, 1, 'Understanding biblical goodness.', '6493a897-755a-4769-9a1b-bec6a6c5f7d5', 'No One is Good', 1, '2015-07-14 13:34:50'),
(33, 1, 'Exploring the concept of loving your enemy', '289ad6e8-39bc-473b-973e-af564b2f0ab1', 'Nonresistance', 1, '2017-10-09 05:27:08'),
(34, 1, '', '17df4ce3-289a-4077-8af1-81b4a5c63f19', 'Not Under the Law', 1, '2015-12-21 14:39:14'),
(35, 1, 'The importance of obeying God\'s laws.', 'd2346fd5-e84a-46a8-96c5-f3a6a6c5f9f0', 'Obey God\'s Commandments', 1, '2015-07-14 11:13:47'),
(36, 1, 'The duty to follow government laws.', '755ae009-8f6d-4b6c-8b7e-ff8b12d3e9c0', 'Obey Government Laws', 1, '2015-01-24 03:11:06'),
(37, 1, 'Grasping the omnipotence, the all-powerful nature of God.', '289ad6e8-2dfb-437b-88d1-9c2b057ba1a0', 'Omnipotence', 1, '2015-07-14 05:57:02'),
(38, 1, 'Acknowledging that God observes all, nothing is hidden from Him and His presence everywhere.', '17df4ce3-5db0-4f69-9b7e-87d2b5c63b19', 'Omnipresence', 1, '2015-07-14 05:49:52'),
(39, 1, 'Understanding God\'s omniscience, His all-knowing nature.', '04c7f6b3-14d8-44c2-89e4-57f6492a2e1c', 'Omniscience', 1, '2015-07-14 05:35:11'),
(40, 1, 'Understanding God\'s intended structure and order for the home.', '289ad6e8-39bc-4920-b96e-65f64932b9c1', 'Orderly Home', 1, '2015-07-14 12:50:03'),
(41, 1, '', 'a1d263b6-b5fb-473b-8af1-f67394c5b1d3', 'Prince of this World', 1, '2015-07-14 14:06:16'),
(42, 1, 'Exploring the doctrine of divine providence.', 'f2346bc8-1de2-43c6-94de-af432c2f0ab1', 'Providence', 1, '2015-07-14 16:40:53'),
(43, 1, 'The process of adopting a new mindset.', 'a1d263b6-b5fb-4fb6-9b5b-c6a6cb85f9f0', 'Renewing of the Mind', 1, '2015-01-25 09:17:28'),
(44, 1, 'The call for repentance as a key message.', '44df4ce6-5dfb-4bb3-96c1-f3a6b5c5f9f0', 'Repentance', 1, '2015-01-23 16:10:21'),
(45, 1, 'Salvation through belief and trust in Jesus Christ.', '41df4ce6-efd0-4077-85ef-78f3b5c62f19', 'Saved by Faith', 1, '2017-10-09 05:27:31'),
(46, 1, 'Exploring the biblical perspective on homosexuality.', 'd2346fd5-3fe2-473b-8bf4-f67394c5b1d3', 'Sodomy', 1, '2015-02-01 14:25:22'),
(47, 1, 'Exploring the prophecies concerning Jesus.', '289ad6e8-39bc-4077-8af1-81b4a5c63f19', 'Spirit of  Prophecy', 1, '2015-07-15 17:33:42'),
(48, 1, 'Spiritual gifts as recognized and utilized in the church.', '6493a897-fefb-4d3b-a4b9-96c5ab5932a3', 'Spiritual Gifts', 1, '2015-07-16 17:35:10'),
(49, 1, 'Exploring judgement and discernment.', '8f6d2789-a1d2-4fb6-9b5b-c6a6cb85f9f0', 'Spiritual Judgement', 1, '2015-01-24 03:35:28'),
(50, 1, 'Exploring the concept of spiritual rebirth.', '8f6d2789-a1d2-4925-92e8-df7c2b78c649', 'Spiritual Rebirth', 1, '2015-01-24 03:20:24'),
(51, 1, 'Understanding temptation and strategies for overcoming it.', '04c7f6b3-17df-437b-88d1-9c2b057ba1a0', 'Temptation', 1, '2015-07-15 17:08:23'),
(52, 1, 'Exploring the use of wine.', '9eb76f94-32c7-4b6c-9a1a-bec6913a4b0f', 'Wine ', 1, '2015-07-14 10:03:17'),
(53, 1, 'Exploring what leads to or fosters wisdom.', 'f2346bc8-04c7-437b-88d1-9c2b057ba1a0', 'Wisdom Cause', 1, '2015-07-14 05:18:55'),
(54, 1, 'Understanding the outcomes or fruits of wisdom.', 'd2346fd5-e84a-44c2-87b7-ff8b46d2f6e7', 'Wisdom Fruit', 1, '2015-07-14 15:59:50'),
(55, 1, 'Exploring the origin and source of wisdom.', 'c3dfb8e7-d234-43c6-94de-af432c2f0ab1', 'Wisdom Origin', 1, '2015-07-14 15:39:01'),
(56, 1, 'Understanding the importance and worth of wisdom.', 'e84a2c7a-f234-4f69-9a1a-bec6913a4b0f', 'Wisdom Value', 1, '2015-07-14 11:45:23'),
(57, 1, 'Exploring the roles and responsibilities of women.', 'b5fb8c11-c3df-473b-8af1-f67394c5b1d3', 'Woman\'s Role', 1, '2015-07-16 18:01:45'),
(58, 1, 'Exploring God\'s spoken word.', '20bcf6b7-48db-4c20-b6cf-49d032f15c41', 'Word of God', 1, '2015-01-16 13:39:05'),
(59, 1, 'Exploring the concept of worldly wisdom.', 'b5fb8c11-c3df-46a8-96c5-f3a6a6c5f9f0', 'Worldly Wisdom', 1, '2015-01-25 10:15:36');

CREATE INDEX idx_#__getbible_tagged_verse_on_linker_and_tag
ON #__getbible_tagged_verse(linker, tag);

CREATE INDEX idx_#__getbible_verse_on_verse_and_abbreviation
ON #__getbible_verse(verse, abbreviation);

CREATE INDEX idx_#__getbible_book_on_nr_and_abbreviation
ON #__getbible_book(nr, abbreviation);

CREATE INDEX idx_#__getbible_tag_on_guid_and_published
ON #__getbible_tag(guid, published);


