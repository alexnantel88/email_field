<?php

	require_once(TOOLKIT . '/fields/field.input.php');

	Class fieldEmail extends fieldInput {

		public function __construct(){
			parent::__construct();
			$this->entryQueryFieldAdapter = new EntryQueryFieldAdapter($this);

			$this->_name = __('Email');
			$this->_required = true;

			$this->set('required', 'no');
		}

	/*-------------------------------------------------------------------------
		Setup:
	-------------------------------------------------------------------------*/

		public function createTable(){
			return Symphony::Database()
				->create('tbl_entries_data_' . $this->get('id'))
				->ifNotExists()
				->fields([
					'id' => [
						'type' => 'int(11)',
						'auto' => true,
					],
					'entry_id' => 'int(11)',
					'value' => [
						'type' => 'varchar(255)',
						'null' => true,
					],
				])
				->keys([
					'id' => 'primary',
					'entry_id' => 'key',
					'value' => 'key',
				])
				->execute()
				->success();
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		private function __applyValidationRule($data) {
			include(TOOLKIT . '/util.validators.php');
			$rule = (isset($validators['email'])
				? $validators['email']
				: '/^\w(?:\.?[\w%+-]+)*@\w(?:[\w-]*\.)+?[a-z]{2,}$/i');

			return General::validateString($data, $rule);
		}


	/*-------------------------------------------------------------------------
		Settings:
	-------------------------------------------------------------------------*/

		public function displaySettingsPanel(XMLElement &$wrapper, $errors = null) {
			parent::displaySettingsPanel($wrapper, $errors);

			// Hide Validator field
			if(count($wrapper->getChildren()) == 7) {
				$wrapper->removeChildAt(4);
				$wrapper->removeChildAt(5);
			} else {
				$wrapper->removeChildAt(3);
				$wrapper->removeChildAt(4);
			}
		}

		public function commit() {
			// Bypass current parent (fieldInput)
			// use the default implementation from the abstract class
			if(!Field::commit()) {
				return false;
			}

			$id = $this->get('id');
			if($id === false) {
				return false;
			}

			$fields = array();
			$fields['field_id'] = $id;

			return FieldManager::saveSettings($id, $fields);
		}

	/*-------------------------------------------------------------------------
		Publish:
	-------------------------------------------------------------------------*/

		public function checkPostFieldData($data, &$message, $entry_id = null){

			$message = null;

			if($this->get('required') == 'yes' && strlen($data) == 0){
				$message = __("'%s' is a required field.", array($this->get('label')));
				return self::__MISSING_FIELDS__;
			}

			if(!$this->__applyValidationRule($data)){
				$message = __("'%s' is not a valid email address.", array($this->get('label')));
				return self::__INVALID_FIELDS__;
			}

			return self::__OK__;

		}

		public function processRawFieldData($data, &$status, &$message = null, $simulate = false, $entry_id = null) {
			$status = self::__OK__;

			if (strlen(trim($data)) == 0) return array();

			$result = array(
				'value' => $data
			);

			return $result;
		}

	/*-------------------------------------------------------------------------
		Filtering:
	-------------------------------------------------------------------------*/

		public function buildDSRetrievalSQL($data, &$joins, &$where, $andOperation = false) {
			$field_id = $this->get('id');

			if (self::isFilterRegex($data[0])) {
				$this->buildRegexSQL($data[0], array('value'), $joins, $where);
			}
			elseif ($andOperation) {
				foreach ($data as $value) {
					$this->_key++;
					$value = $this->cleanValue($value);
					$joins .= "
						LEFT JOIN
							`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
							ON (e.id = t{$field_id}_{$this->_key}.entry_id)
					";
					$where .= "
						AND t{$field_id}_{$this->_key}.value = '{$value}'
					";
				}
			}
			else {
				if (!is_array($data)) $data = array($data);

				foreach ($data as &$value) {
					$value = $this->cleanValue($value);
				}

				$this->_key++;
				$data = implode("', '", $data);
				$joins .= "
					LEFT JOIN
						`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
						ON (e.id = t{$field_id}_{$this->_key}.entry_id)
				";
				$where .= "
					AND t{$field_id}_{$this->_key}.value IN ('{$data}')
				";
			}

			return true;
		}

	/*-------------------------------------------------------------------------
		Output:
	-------------------------------------------------------------------------*/

		public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = null, $entry_id = null) {
			$value = $data['value'];

			if($encode === true) $value = General::sanitize($value);

			$wrapper->appendChild(
				new XMLElement(
					$this->get('element_name'), $value, array('hash' => md5($data['value']))
				)
			);
		}

	}
