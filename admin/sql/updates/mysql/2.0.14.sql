ALTER TABLE `#__getbible_linker` ADD `public_notes` TINYINT(1) NOT NULL DEFAULT 0 AFTER `name`;

ALTER TABLE `#__getbible_linker` ADD `public_tagged_verses` TINYINT(1) NOT NULL DEFAULT 0 AFTER `public_notes`;
