<?php

	Class extension_Email_Field extends Extension{
		public function uninstall(){
			Symphony::Database()->query("DROP TABLE `tbl_fields_email`");
		}

		public function install(){
			return Symphony::Database()->query("
				CREATE TABLE `tbl_fields_email` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`field_id` int(11) unsigned NOT NULL,
					PRIMARY KEY  (`id`),
					KEY `field_id` (`field_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}
	}
