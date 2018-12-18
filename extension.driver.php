<?php

	Class extension_Email_Field extends Extension{

		public function uninstall(){
			return Symphony::Database()
				->drop('tbl_fields_email')
				->ifExists()
				->execute()
				->success();
		}

		public function install(){
			return Symphony::Database()
				->create('tbl_fields_email')
				->ifNotExists()
				->fields([
					'id' => [
						'type' => 'int(11)',
						'auto' => true,
					],
					'field_id' => 'int(11)',
				])
				->keys([
					'id' => 'primary',
					'field_id' => 'key',
				])
				->execute()
				->success();
		}
	}
