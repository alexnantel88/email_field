<?php

	Class extension_Email_Field extends Extension{

		public function uninstall(){
			Symphony::Database()->query("DROP TABLE `tbl_fields_email`");
		}

		public function install(){
			return Symphony::Database()->query("
				CREATE TABLE `tbl_fields_email` (
					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
					`field_id` INT(11) UNSIGNED NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}
	}
