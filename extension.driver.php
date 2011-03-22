<?php

	Class extension_Email_Field extends Extension{
	
		public function about(){
			return array('name' => 'Field: Email',
						 'version' => '1.1',
						 'release-date' => '2011-03-22',
						 'author' => array(
							'name' => 'Symphony Team',
							'website' => 'http://symphony-cms.com'
						)
				 	);
		}
		
		public function uninstall(){
			Symphony::Database()->query("DROP TABLE `tbl_fields_email`");
		}

		public function install(){
			return Symphony::Database()->query("CREATE TABLE `tbl_fields_email` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `field_id` int(11) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  KEY `field_id` (`field_id`)
			);");
		}
			
	}