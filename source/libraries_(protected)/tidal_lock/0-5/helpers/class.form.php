<?php

	class form_TL {

		private $style = 'horizontal'; // alternate styles are "stacked", "inline" and "no-label"

		public function styleForm($style) {
			$this->style = $style;
		}

		public function input($type, $name, $value = null, $mandatory = false, $labelPlaceholder = null, $class = null, $options = null, $maxlength = null, $otherAttributes = null, $truncateLabel = null, $eventHandlers = null) {

			global $tl;
			global $parser;
			global $localization_manager;

			if (!$type) {
				$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No element type specified.\n";
				return false;
			}
			
			if ($labelPlaceholder) {
				$result = explode('|', $labelPlaceholder);
				if (isset($result[0])) $label = $result[0];
				if (isset($result[1])) $placeholder = $result[1];
			}
			
			if (!isset($label)) $label = null;
			if (!isset($placeholder)) $placeholder = null;

			if (($this->style == 'inline' || $this->style == 'no-label') && !$placeholder) $placeholder = $label;
			
			switch($type) {
				/* ------------------------- */
					case 'text':
					case 'password':
					case 'hidden':
					case 'number':
					case 'decimal':
					case 'latitude':
					case 'longitude':
					case 'number_without_spin_buttons':
					case 'url':
					case 'email':
					case 'tel':
					case 'title':
					case 'readonly':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($type == 'number' && $value && !is_numeric($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Number field must contain a numeric value.\n";
								return false;
							}
							if ($type == 'email' && $value && substr_count($value, '@') < 1 && substr_count($value, '.') < 1) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Email value is invalid.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							if ($type == 'number_without_spin_buttons' || $type == 'title') $field = "<input type='text' name='" . $name . "' id='" . $name . "'";
							elseif ($type == 'decimal') $field = "<input type='number' " . (!@$otherAttributes['step'] ? "step='.01' " : false) . "name='" . $name . "' id='" . $name . "'";
							elseif ($type == 'latitude' || $type == 'longitude') $field = "<input type='number' " . (!@$otherAttributes['step'] ? "step='.0001' " : false) . "name='" . $name . "' id='" . $name . "'";
							else $field = "<input type='" . $type . "' name='" . $name . "' id='" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							if ($type == 'title') $field .= " class='" . trim('autoCapitalize ' . $class) . "'";
							elseif ($class) $field .= " class='" . $class . "'";
							if ($maxlength) $field .= " maxlength='" . $maxlength . "'";
							if ($value) $field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($type == 'number') {
								$eventHandlers['onChange'] = "if (isNaN(this.value)) this.value = 0; " . $eventHandlers['onChange'];
								if (!is_null(@$otherAttributes['max'])) $eventHandlers['onChange'] = "if (this.value && this.value > " . floatval($otherAttributes['max']) . ") { this.value = " . floatval($otherAttributes['max']) . "; } " . $eventHandlers['onChange'];
								if (!is_null(@$otherAttributes['min'])) $eventHandlers['onChange'] = "if (this.value && this.value < " . floatval($otherAttributes['min']) . ") { this.value = " . floatval($otherAttributes['min']) . "; } " . $eventHandlers['onChange'];
							}
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							if ($type == 'readonly') $field .= " readonly";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'textarea':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<textarea name='" . $name . "' id='" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($maxlength) $field .= " maxlength='" . $maxlength . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= ">" . $value . "</textarea>";
							return $field;
							break;
				/* ------------------------- */
					case 'uneditable':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if (is_null($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): No value specified.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
						// return
							$field = "<span";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							$field .= ">";
							$field .= "<span name='" . $name . "_visible' id='" . $name . "_visible'>". htmlspecialchars($value, ENT_QUOTES) . "</span>";
							$field .= "<input type='hidden' name='" . $name . "' id='" . $name . "'";
							$field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							$field .= " />";
							$field .= "</span>";
							return $field;
							break;
				/* ------------------------- */
					case 'uneditable_static':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if (is_null($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): No value specified.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
						// return
							$field = "<div class='form-control-static";
							if ($class) $field .= " " . $class;
							$field .= "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							$field .= ">";
							$field .= "<span name='" . $name . "_visible' id='" . $name . "_visible'>". $value . "</span>";
							$field .= "<input type='hidden' name='" . $name . "' id='" . $name . "'";
							$field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							$field .= " />";
							$field .= "</div>";
							return $field;
							break;
				/* ------------------------- */
					case 'search':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<input type='search' name='" . $name . "' id='" . $name . "' results='0'";
							$field .= " placeholder='" . ($placeholder ? htmlspecialchars($placeholder, ENT_QUOTES) : "Search") . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($maxlength) $field .= " maxlength='" . $maxlength . "'";
							if ($value) $field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'password_with_preview':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<div id='password_preview_close_" . $name . "'><div class='input-group'><div class='input-group-addon'><a href='' onClick=" . '"' . "document.getElementById('password_preview_close_" . $name . "').className='hidden'; document.getElementById('password_preview_open_" . $name . "').className='visible'; document.getElementById('preview_" . $name . "').value=document.getElementById('" . $name . "').value; return false;" . '"' . "><span class='glyphicon glyphicon-eye-close' aria-hidden='true'></span></a></div>";
							$field .= "<input type='password' name='" . $name . "' id='" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($maxlength) $field .= " maxlength='" . $maxlength . "'";
							if ($value) $field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " /></div></div>";

							$field .= "<div id='password_preview_open_" . $name . "' class='hidden'><div class='input-group'><div class='input-group-addon'><a href='' onClick=" . '"' . "document.getElementById('password_preview_close_" . $name . "').className='visible'; document.getElementById('password_preview_open_" . $name . "').className='hidden'; return false;" . '"' . "><span class='glyphicon glyphicon-eye-open' aria-hidden='true'></span></a></div>";
							$field .= "<input type='text' name='preview_" . $name . "' id='preview_" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($maxlength) $field .= " maxlength='" . $maxlength . "'";
							if ($value) $field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " disabled /></div></div>";
							return $field;
							break;
				/* ------------------------- */
					case 'password_with_health_meter':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<div><input type='password' name='" . $name . "' id='" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($maxlength) $field .= " maxlength='" . $maxlength . "'";
							if ($value) $field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " /></div>";
							$field .= "<div id='healthMeterContainer' class='hidden'><div id='healthMeter' class='" . $class . "'></div></div>";
							return $field;
							break;
				/* ------------------------- */
						case 'select':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<select name='" . $name;
							if (!is_null(@$otherAttributes['multiple'])) $field .= "[]";
							$field .= "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory && count($options)) $field .= " required";
							$field .= " />";
							if ($placeholder) {
								$field .= "<option value=''>" . $placeholder . "</option>";
								$field .= "<option value=''>--</option>";
							}
							elseif (!$mandatory) $field .= "<option value=''></option>";
							if (count($options)) {
								foreach ($options as $optionValue => $optionLabel) {
									$field .= "<option value='" . $optionValue . "'";
									if ($value && is_array($value)) {
										foreach ($value as $id=>$matchValue) {
											if ($optionValue == $id) $field .= " selected";
										}
									}
									elseif ($value) {
										if ($optionValue == $value) $field .= " selected";
									}
									$field .= ">";
									if ($truncateLabel && $truncateLabel < strlen($optionLabel) - 3) $optionLabel = substr($optionLabel, 0, $truncateLabel) . '...';
									$field .= $optionLabel;
									$field .= "</option>";
								}
							}
							$field .= "</select>";
							return $field;
							break;
				/* ------------------------- */
						case 'yesno_bootstrap_switch':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$field = '';
							$value = floatval(@$value);
						// return
							$field .= "<input type='checkbox' name='" . $name . "' id='" . $name . "' class='checkboxSwitch'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if (!@$otherAttributes['data-on-text'] && !@$otherAttributes['data-off-text']) $field .= " data-on-text='YES' data-off-text='NO'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($value) $field .= " checked";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'percentage':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($value && !is_numeric($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Percentage must be a numeric value.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if (!$eventHandlers) $eventHandlers = array();
							$eventHandlers['onChange'] = "if (isNaN(this.value)) { this.value = 0; } if (parseInt(this.value) > 100) { this.value = 100; } if (parseInt(this.value) < 0) { this.value = 0; } " . @$eventHandlers['onChange'];
						// return
							$field = "<input type='number' name='" . $name . "' id='" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($value) $field .= " value='" . floatval($value) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'percentage_bootstrap':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($value && !is_numeric($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Percentage must be a numeric value.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if (!$eventHandlers) $eventHandlers = array();
							$eventHandlers['onChange'] = "if (isNaN(this.value)) { this.value = 0; } if (parseInt(this.value) > 100) { this.value = 100; } if (parseInt(this.value) < 0) { this.value = 0; } " . @$eventHandlers['onChange'];
						// return
							$field = "<div class='input-group'>";
							$field .= "<input type='text' name='" . $name . "' id='" . $name . "'";
							if ($placeholder) $field .= " placeholder='" . htmlspecialchars($placeholder, ENT_QUOTES) . "'";
							$field .= " class='" . trim('form-control ' . $class) . "'";
							if ($value) $field .= " value='" . floatval($value) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							$field .= "<span class='input-group-addon'>%</span></div>";
							return $field;
							break;
				/* ------------------------- */
					case 'currency_select':
					case 'currency_select_short':
						// validate
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
							global $localization_manager;
							if (!$localization_manager->currencies) {
								$localization_manager = new localization_manager_TL();
								$localization_manager->populateCurrencies();
							}
							if (!@$options) {
								$options = $localization_manager->currencies;
								if ($type == 'currency_select_short') ksort($options);
							}

						// initialize
							if (!$name) $name = 'currency';
						// return
							$field = "<select name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= ">";
							if ($label && !$mandatory) {
								$field .= "<option value=''>" . $label . "</option>";
								$field .= "<option value=''>--</option>";
							}
							foreach ($options as $currencyID => $currency) {
								$field .= "<option value='" . $currencyID . "'";
								if ($value && $currencyID == $value) $field .= " selected";
								$field .= ">";
								if ($type == 'currency_select') {
									if ($truncateLabel > strlen($currency) - 3) $currency = substr($currency, 0, $truncateLabel) . '...';
									$field .= $currency;
								}
								else $field .= $currencyID;
								$field .= "</option>";
							}
							$field .= "</select>";
							return $field;
							break;
				/* ------------------------- */
					case 'currency_number':
						// validate
							if (@$value && !is_array($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Value must be an array containing amount and/or currency.\n";
								return false;
							}
							if (@$value['amount']) $value['amount'] = str_replace(',', '', $value['amount']);
							if (@$value['amount'] && !is_numeric(@$value['amount'])) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Amount must be a numeric value.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if (!$name) $name = 'amount';
							if (!$eventHandlers) $eventHandlers = array();
							$eventHandlers['onChange'] = trim("this.value = this.value.replace(/,/g, " . '""' . "); if (isNaN(this.value)) { this.value = " . '"0.00"' . "; } " . @$eventHandlers['onChange']);
							if (!@$otherAttributes['step']) $otherAttributes['step'] = '.01';
							if (!is_null(@$otherAttributes['max'])) $eventHandlers['onChange'] = "if (this.value && this.value > " . floatval($otherAttributes['max']) . ") { this.value = " . '"' . number_format($otherAttributes['max'], 2) . '"' . "; } " . @$eventHandlers['onChange'];
							if (!is_null(@$otherAttributes['min'])) $eventHandlers['onChange'] = "if (this.value && this.value < " . floatval($otherAttributes['min']) . ") { this.value = " . '"' . number_format($otherAttributes['min'], 2) . '"' . "; } " . @$eventHandlers['onChange'];
							if (@$otherAttributes['display_currency'] && @$value['currency']) {
								$displaySymbol = true;
								unset($otherAttributes['display_currency']);

								global $localization_manager;
								$localization_manager = new localization_manager_TL();
								$localization_manager->populateCurrencies($value['currency']);
							}
						// return
							if (!@$displaySymbol) $field = $this->input('number', $name, (floatval(@$value['amount']) ? number_format(floatval(@$value['amount']), 2) : '0.00'), $mandatory, $labelPlaceholder, $class, null, $maxlength, $otherAttributes, $truncateLabel, $eventHandlers);
							else {
								$field = "<div class='input-group'>";
								if ($localization_manager->currency_symbols[@$value['currency']]) $field .= "<span class='input-group-addon'>" . $localization_manager->currency_symbols[$value['currency']] . "</span>";
								$field .= $this->input('number', $name, floatval(@$value['amount']), $mandatory, $labelPlaceholder, trim('form-control ' . $class), null, $maxlength, $otherAttributes, $truncateLabel, $eventHandlers);
								$field .= "<span class='input-group-addon'>" . $value['currency'] . "</span>";
								$field .= "</div>";
							}
							return $field;
							break;
				/* ------------------------- */
					case 'radio':
					case 'radio_stacked':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if (count($options) < 1) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): No options to display as radio buttons.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if ($type == 'radio') $field = '<br />';
							elseif ($type == 'radio_stacked') $field = '';
							$increment = 0;
						// return
							foreach ($options as $optionValue => $optionLabel) {
								if ($type == 'radio_stacked') $field .= "<div class='radio'>";
								if ($type == 'radio_stacked') $field .= "<label>";
								else $field .= "<label class='radio-inline'>";
								$field .= "<input type='radio' name='" . $name . "' id='" . $name . "_" . $increment . "'";
								$field .= " value='" . $optionValue . "'";
								if ($value == $optionValue) $field .= " checked";
								if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
								if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
								if ($mandatory) $field .= " required";
								$field .= " /> ";
								$field .= $optionLabel;
								$field .= "</label>";
								if ($type == 'radio_stacked') $field .= "</div>";
								$increment++;
							}
							return $field;
							break;
				/* ------------------------- */
					case 'checkbox':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$field = '';
						// return
							$field .= "<input type='checkbox' name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($value) $field .= " checked='checked'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " /> " . $label;
							return $field;
							break;
				/* ------------------------- */
					case 'checkbox_stacked_bootstrap':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$field = '';
						// return
							$field .= "<div class='checkbox'><label>";
							$field .= "<input type='checkbox' name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($value) $field .= " checked='checked'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " /> " . $label;
							$field .= "</label></div>";
							return $field;
							break;
				/* ------------------------- */
					case 'date':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$dateRegex = "^[0-9]{4}-(((0[13578]|(10|12))-(0[1-9]|[1-2][0-9]|3[0-1]))|(02-(0[1-9]|[1-2][0-9]))|((0[469]|11)-(0[1-9]|[1-2][0-9]|30)))$";
						// return
							$field = "<input type='date' name='" . $name . "' id='" . $name . "' placeholder='YYYY-MM-DD' pattern='" . $dateRegex . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($value && $value != '0000-00-00') $field .= " value='" . $value . "' ";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'hoursminutes':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$dateRegex = "^((([0]?[1-9]|1[0-2])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])?( )?(AM|am|aM|Am|PM|pm|pM|Pm))|(([0]?[0-9]|1[0-9]|2[0-3])(:|\.)[0-5][0-9]((:|\.)[0-5][0-9])?))$";
						// return
							$field = "<input type='text' name='" . $name . "' id='" . $name . "' class='" . trim("input-small " . $class) . "' placeholder='HH:MM PM' pattern='" . $dateRegex . "'";
							if ($value) $field .= " value='" . $value . "' ";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'datetime_with_picker':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<div id='" . $name . "' class='input-group date form_datetime' data-date-format='yyyy-mm-dd hh:ii:ss' data-link-format='yyyy-mm-dd hh:ii:ss' data-link-field='" . $name . "'>\n";
							$field .= "<input type='text' id='" . $name . "' name='" . $name . "' maxlength='19'";
							if ($class) $field .= " class='" . $class . "'";
							if ($value) $field .= " value='" . htmlspecialchars($value, ENT_QUOTES) . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							$field .= "<span class='input-group-addon'>&nbsp;<span class='glyphicon glyphicon-calendar'></span></span>";
							$field .= "</div>";
							return $field;
							break;
				/* ------------------------- */
					case 'year':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($value && !is_numeric($value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Year must be a numeric value.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$dateRegex = "^[0-9]{4}$";
							if (!$eventHandlers && (!is_null(@$otherAttributes['max']) || !is_null(@$otherAttributes['min']))) $eventHandlers = array();
							if (!is_null(@$otherAttributes['max'])) $eventHandlers['onChange'] = "if (this.value && this.value > " . floatval($otherAttributes['max']) . ") this.value = " . intval($otherAttributes['max']) . "; " . @$eventHandlers['onChange'];
							if (!is_null(@$otherAttributes['min'])) $eventHandlers['onChange'] = "if (this.value && this.value < " . floatval($otherAttributes['min']) . ") this.value = " . intval($otherAttributes['min']) . "; " . @$eventHandlers['onChange'];
						// return
							$field = "<input type='number' name='" . $name . "' id='" . $name . "' class='input-mini' placeholder='YYYY' pattern='" . $dateRegex . "' maxlength='4'";
							if (floatval($value)) $field .= " value='" . floatval($value) . "' ";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'timezone':
						// validate
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if (!$name) $name = 'timezone';
							$timezones = array();
							$timestamp = time();
							foreach(timezone_identifiers_list() as $key => $zone) {
								date_default_timezone_set($zone);
								$timezones[$key]['zone'] = $zone;
								$timezones[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
							}
						// return
							$field = "<select name='" . $name;
							if (!is_null(@$otherAttributes['multiple'])) $field .= "[]";
							$field .= "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							if ($placeholder) {
								$field .= "<option value=''>" . $placeholder . "</option>";
								$field .= "<option value=''>--</option>";
							}
							elseif (!$mandatory) $field .= "<option value=''></option>";
							foreach($timezones as $t) {
								$field .= "<option value='" . $t['zone'] . "'";
								if ($value && is_array($value)) {
									foreach ($value as $matchValue) {
										if ($t['zone'] == $matchValue) $field .= " selected";
									}
								}
								elseif ($value) {
									if ($t['zone'] == $value) $field .= " selected";
								}
								$field .= ">";
								$field .= $t['zone'] . " (" . $t['diff_from_GMT'] . ")";
								$field .= "</option>";
							}
							$field .= "</select>";
							return $field;
							break;
				/* ------------------------- */
					case 'file':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<input type='file' name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							@$eventHandlers['onChange'] .= 'if (this.files[0]) { document.getElementById("' . $name . '_filesize").value = this.files[0].size; } ';
							$eventHandlers['onChange'] .= 'if (this.files[0]) { document.getElementById("' . $name . '_mime").value = this.files[0].type; } ';
							$eventHandlers['onChange'] .= 'var _URL = window.URL || window.webkitURL; ';
							$eventHandlers['onChange'] .= 'var file, img; ';
							$eventHandlers['onChange'] .= 'if ((file = document.getElementById("' . $name . '").files[0])) { ';
							$eventHandlers['onChange'] .= 'img = new Image(); ';
							$eventHandlers['onChange'] .= 'img.onload = function() { ';
							$eventHandlers['onChange'] .= 'if (document.getElementById("' . $name . '_width")) document.getElementById("' . $name . '_width").value = img.width; ';
							$eventHandlers['onChange'] .= 'if (document.getElementById("' . $name . '_height")) document.getElementById("' . $name . '_height").value = img.height; ';
							$eventHandlers['onChange'] .= '}; ';
							$eventHandlers['onChange'] .= 'img.src = _URL.createObjectURL(file); ';
							$eventHandlers['onChange'] .= '}';
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							$field .= "<input type='hidden' name='" . $name . "_filesize' id='" . $name . "_filesize' />";
							$field .= "<input type='hidden' name='" . $name . "_mime' id='" . $name . "_mime' />";
							$field .= "<input type='hidden' name='" . $name . "_width' id='" . $name . "_width' />";
							$field .= "<input type='hidden' name='" . $name . "_height' id='" . $name . "_height' />";
							return $field;
							break;
				/* ------------------------- */
					case 'file_dropzone':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if (!@$otherAttributes['post_path']) {
								$otherAttributes['post_path'] = '/includes/controllers/ajax/process_upload.php';
							}
							if (!@$otherAttributes['destination_path']) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): no destination path found.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$dropzone = new drag_and_drop_widget_TL();
							$dropzone->initialize([
								'id' => $name,
								'message' => @$otherAttributes['message'],
								'post_path' => $otherAttributes['post_path'],
								'destination_path' => $otherAttributes['destination_path'],
								'upload_multiple' => @$otherAttributes['upload_multiple'],
								'parallel_uploads' => @$otherAttributes['parallel_uploads'],
								'max_files' => @$otherAttributes['max_files'],
								'thumbnail_width' => @$otherAttributes['thumbnail_width'],
								'thumbnail_height' => @$otherAttributes['thumbnail_height'],
								'events' => @$otherAttributes['events'],
								'acceptable_mime_types' => @$otherAttributes['acceptable_mime_types']
							]);
							$field = $dropzone->html;
							$tl->page['javascript'] .= $dropzone->js;
							return $field;
							break;
				/* ------------------------- */
					case 'range':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
							$max = @$otherAttributes['max'];
							unset($otherAttributes['max']);
							if (!$max) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Range requires a maximum value.\n";
								return false;
							}
						// return
							$field = "<input type='range' name='" . $name . "' id='" . $name . "' max='" . $max . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'country':
						// validate
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
							if (!$options) {
								if ($localization_manager->countries) $options = $localization_manager->countries;
								else {
									$localization_manager = new localization_manager_TL();
									$localization_manager->populateCountries();
									$options = $localization_manager->countries;
								}
							}
						// initialize
							if (!$name) $name = 'country';
						// return
							$field = "<select name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= ">";
							if ($label && !$mandatory) {
								$field .= "<option value=''>" . $label . "</option>";
								$field .= "<option value=''>--</option>";
							}
							foreach ($options as $countryID => $country) {
								$field .= "<option value='" . $countryID . "'";
								if ($value && $countryID == $value) $field .= " selected";
								$field .= ">";
								if ($truncateLabel > strlen($country) - 3) $country = substr($country, 0, $truncateLabel) . '...';
								$field .= $country;
								$field .= "</option>";
							}
							$field .= "</select>";
							return $field;
							break;
				/* ------------------------- */
					case 'region':
						// validate
							if (@$value && !is_array(@$value)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Value must be an array.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if (!$name) $name = $type;
							if (@$otherAttributes['link-to']) {
								$linkTo = $otherAttributes['link-to'];
								unset($otherAttributes['link-to']);
								if (!file_exists(__DIR__ . '/../../../../wwwroot/includes/controllers/ajax/retrieve_regions.php')) {
									$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): Unable to locate AJAX destination.\n";
									return false;
								}
							}
							if (@$value['country_id']) {
								$localization_manager = new localization_manager_TL();
								$localization_manager->populateRegions($value['country_id']);
								if (count(@$localization_manager->regions[$value['country_id']]['regions'])) {
									$options = array();
									foreach ($localization_manager->regions[$value['country_id']]['regions'] as $region) {
										$options[$region['region_id']] = $region['region'];
									}
								}
								$subdivision = ucwords(@$localization_manager->regions[$value['country_id']]['region_type']);
							}
							if (!@$subdivision) $subdivision = "Region";
						// return
							$field = "<div id='regionContainer_" . $name . "'>";
							if (!@$options) $field .= $this->input('text', $name . '_other', @$value['region_other'], $mandatory, $labelPlaceholder, $class, null, $maxlength, $otherAttributes, $truncateLabel, $eventHandlers);
							else $field .= $this->input('select', $name . '_id', @$value['region_id'], $mandatory, $labelPlaceholder, $class, $options, $maxlength, $otherAttributes, $truncateLabel, $eventHandlers);
							$field .= "</div>";

							if (@$linkTo) {
								$tl->page['javascript'] .= "// update region selector label\n";
								$tl->page['javascript'] .= "  if ($('#formLabel_" . $name . "').length) document.getElementById('formLabel_" . $name . "').innerHTML = " . '"' . $subdivision . '"' . ";\n";
								$tl->page['javascript'] .= "// connect region selector with country selector\n";
								$tl->page['javascript'] .= "  $('#" . $linkTo . "').change(function() {\n";
								$tl->page['javascript'] .= "    $.ajax({\n";
								$tl->page['javascript'] .= "      type: 'POST',\n";
								$tl->page['javascript'] .= "      url: '/includes/controllers/ajax/retrieve_regions.php',\n";
								$tl->page['javascript'] .= "      data: {\n";
								$tl->page['javascript'] .= "        country_id : document.getElementById('" . $linkTo . "').value,\n";
								$tl->page['javascript'] .= "        region_id : " . intval($value['region_id']) . ",\n";
								$tl->page['javascript'] .= "        region_other : " . '"' . ($value['region_other'] ? $value['region_other'] : null) . '"' . ",\n";
								$tl->page['javascript'] .= "        name : " . '"' . $name . '"' . ",\n";
								$tl->page['javascript'] .= "        class : " . '"' . ($class ? $class : null) . '"' . ",\n";
								$tl->page['javascript'] .= "        labelPlaceholder : " . '"' . ($labelPlaceholder ? $labelPlaceholder : null) . '"' . ",\n";
								$tl->page['javascript'] .= "        mandatory : " . '"' . ($mandatory ? true : false) . '"' . ",\n";
								$tl->page['javascript'] .= "        maxlength : " . '"' . ($maxlength ? floatval($maxlength) : null) . '"' . ",\n";
								$tl->page['javascript'] .= "        truncateLabel : " . '"' . ($truncateLabel ? floatval($truncateLabel) : null) . '"' . "\n";
								$tl->page['javascript'] .= "      },\n";
								$tl->page['javascript'] .= "      success: function(data) {\n";
								$tl->page['javascript'] .= "        document.getElementById('regionContainer_" . $name . "').innerHTML = data;\n";
								$tl->page['javascript'] .= "        if ($('#formLabel_" . $name . "').length) document.getElementById('formLabel_" . $name . "').innerHTML = document.getElementById('regionType').value;\n";
								$tl->page['javascript'] .= "      },\n";
								$tl->page['javascript'] .= "      error: function(errorData) {\n";
								$tl->page['javascript'] .= "        console += errorData + " . '"\n"' . ";\n";
								$tl->page['javascript'] .= "      }\n";
								$tl->page['javascript'] .= "	});\n";
								$tl->page['javascript'] .= "  });\n\n";
							}

							return $field;
							break;
				/* ------------------------- */
					case 'latlongmap':
						// initialize
							if (!$name) $name = 'latlongmap';
							if (!trim($value)) $value = '0,0';
							$coords = explode(',', $value);
							$lat = floatval($coords[0]);
							$long = floatval($coords[1]);
							$granularity = (@$otherAttributes['step'] ? strlen(substr(strrchr(@$otherAttributes['step'], "."), 1)) : 4);
							$canvas = $name . "_mapcanvas";
							$map = $name . "_mapobject";
						// return
							$field = "<div class='row'>";
							$field .= "<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'><div id='" . $canvas . "' style='width: 100%; height: 400px; margin-bottom: 15px;' class='img-rounded'>Loading map...</div></div>";
							$field .= "</div>";
							$field .= "<div class='row'>";
							$field .= "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>" . $this->input('latitude', $name . "_latitude", @$lat, false, "|Latitude", 'form-control', null, null, @$otherAttributes['step']) . "</div>";
							$field .= "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>" . $this->input('longitude', $name . "_longitude", @$long, false, "|Longitude", 'form-control', null, null, @$otherAttributes['step']) . "</div>";
							$field .= "<div class='col-lg-4 col-md-4 col-sm-4 col-xs-4'>" . $this->input('button', "reset_button", null, false, "Reset", 'btn btn-default btn-block', null, null, null, null, array('onClick'=>'window["' . $map . '"].setCenter(new google.maps.LatLng(' . $value . '));')) . "</div>";
							$field .= "</div>";

							$tl->page['javascript'] .= "// initialize latlongmap\n";
							$tl->page['javascript'] .= "  $(document).ready(function() {\n";
							$tl->page['javascript'] .= "    var myLatlng = new google.maps.LatLng(" . $value . ");\n";
							$tl->page['javascript'] .= "    var myOptions = {\n";
					 		$tl->page['javascript'] .= "      zoom: 2,\n";
					 		$tl->page['javascript'] .= "      zoomControl: true,\n";
							$tl->page['javascript'] .= "      mapTypeControl: true,\n";
							$tl->page['javascript'] .= "      scaleControl: true,\n";
							$tl->page['javascript'] .= "      streetViewControl: true,\n";
							$tl->page['javascript'] .= "      rotateControl: true,\n";
							$tl->page['javascript'] .= "      center: myLatlng,\n";
							$tl->page['javascript'] .= "      mapTypeId: google.maps.MapTypeId.TERRAIN\n";
							$tl->page['javascript'] .= "    };\n\n";
							$tl->page['javascript'] .= "    window['" . $map . "'] = new google.maps.Map(document.getElementById('" . $canvas . "'), myOptions);\n\n";
							$tl->page['javascript'] .= "    var marker_crosshair = new google.maps.Marker({ map: window['" . $map . "'], icon: '/resources/img/icons/crosshair.gif' });\n";
							$tl->page['javascript'] .= "    marker_crosshair.bindTo('position', window['" . $map . "'], 'center');\n";
							$tl->page['javascript'] .= "    google.maps.event.addListener(window['" . $map . "'], 'center_changed', function() {\n";
							$tl->page['javascript'] .= "      mapCenter = window['" . $map . "'].getCenter();\n";
							$tl->page['javascript'] .= "      document.getElementById('" . $name . "_latitude').value = Math.round(mapCenter.lat() * " . (pow(10, floatval($granularity))) . ") / " . (pow(10, floatval($granularity))) . ";\n";
							$tl->page['javascript'] .= "      document.getElementById('" . $name . "_longitude').value = Math.round(mapCenter.lng() * " . (pow(10, floatval($granularity))) . ") / " . (pow(10, floatval($granularity))) . ";\n";
							$tl->page['javascript'] .= "    });\n";
							$tl->page['javascript'] .= "  });\n\n";

							return $field;
							break;
				/* ------------------------- */
					case 'language':
					case 'language_common':
						// validate
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							if (!$options) {
								$localization_manager = new localization_manager_TL();
								if ($type == 'language_common') $localization_manager->populateLanguages(true);
								else $localization_manager->populateLanguages();
								$options = $localization_manager->languages;
							}
							if (!$name) $name = 'language';
						// return
							$field = "<select name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							if ($mandatory) $field .= " required";
							$field .= ">";
							if ($label && !$mandatory) {
								$field .= "<option value=''>" . $label . "</option>";
								$field .= "<option value=''>--</option>";
							}
							foreach ($options as $languageID => $language) {
								$field .= "<option value='" . $languageID . "'";
								if ($value && $languageID == $value) $field .= " selected";
								$field .= ">";
								if ($truncateLabel > strlen($language) - 3) $language = substr($language, 0, $truncateLabel) . '...';
								$field .= $language;
								$field .= "</option>";
							}
							$field .= "</select>";
							return $field;
							break;
				/* ------------------------- */
					case 'multipicker':
						// validate
							if (!$name) {
								$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No id/name specified for " . $type . " field.\n";
								return false;
							}
							if (count($options) < 1) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): No options to display in multipicker.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// initialize
							$valueDelimiter = ';';
							$increment = 0;
						// return
							$field = "<div style='padding: 4px; border: 1px solid #ddd; overflow: scroll; overflow-x: hidden;'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							$field .= ">";
							foreach ($options as $optionValue => $optionLabel) {
								if (substr_count($value, $optionValue)) {
									$field .= "<a href='javascript:void(0)' id='multipickerLink_" . $increment . "' style='font-weight: bold; text-decoration: none;' onClick='if (document.getElementById(" . '"multipickerLink_' . $increment . '"' . ").style.fontWeight == " . '"normal"' . ") { document.getElementById(" . '"multipickerLink_' . $increment . '"' . ").style.fontWeight = " . '"bold"' . "; } else { document.getElementById(" . '"multipickerLink_' . $increment . '"' . ").style.fontWeight = " . '"normal"' . "; } if (document.getElementById(" . '"' . $name . '"' . ").value.indexOf(" . '"' . $optionValue . '"' . ") > -1) { document.getElementById(" . '"' . $name . '"' . ").value = document.getElementById(" . '"' . $name . '"' . ").value.replace(" . '"' . $valueDelimiter . $optionValue . '"' . ", " . '""' . "); document.getElementById(" . '"' . $name . '"' . ").value = document.getElementById(" . '"' . $name . '"' . ").value.replace(" . '"' . $optionValue . '"' . ", " . '""' . "); } else { if (!document.getElementById(" . '"' . $name . '"' . ").value) { document.getElementById(" . '"' . $name . '"' . ").value = " . '"' . $optionValue . '"' . "; } else { document.getElementById(" . '"' . $name . '"' . ").value += " . '"' . $valueDelimiter . $optionValue . '"' . "; } } return false;'>";
									$field .= $optionLabel;
									$field .= "</a> &nbsp; ";
								}
								else {
									$field .= "<a href='javascript:void(0)' id='multipickerLink_" . $increment . "' style='font-weight: normal; text-decoration: none;' onClick='if (document.getElementById(" . '"multipickerLink_' . $increment . '"' . ").style.fontWeight == " . '"normal"' . ") { document.getElementById(" . '"multipickerLink_' . $increment . '"' . ").style.fontWeight = " . '"bold"' . "; } else { document.getElementById(" . '"multipickerLink_' . $increment . '"' . ").style.fontWeight = " . '"normal"' . "; } if (document.getElementById(" . '"' . $name . '"' . ").value.indexOf(" . '"' . $optionValue . '"' . ") > -1) { document.getElementById(" . '"' . $name . '"' . ").value = document.getElementById(" . '"' . $name . '"' . ").value.replace(" . '"' . $valueDelimiter . $optionValue . '"' . ", " . '""' . "); document.getElementById(" . '"' . $name . '"' . ").value = document.getElementById(" . '"' . $name . '"' . ").value.replace(" . '"' . $optionValue . '"' . ", " . '""' . "); } else { if (!document.getElementById(" . '"' . $name . '"' . ").value) { document.getElementById(" . '"' . $name . '"' . ").value = " . '"' . $optionValue . '"' . "; } else { document.getElementById(" . '"' . $name . '"' . ").value += " . '"' . $valueDelimiter . $optionValue . '"' . "; } } return false;'>";
									$field .= $optionLabel;
									$field .= "</a> &nbsp; ";
								}
								$increment++;
							}
							$field .= "</div>";
							$field .= "<input type='hidden' name='" . $name . "' id='" . $name . "' value='" . $value . "' />";
							return $field;
							break;
				/* ------------------------- */
					case 'button':
					case 'submit':
					case 'reset':
						// validate
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
							if (@$otherAttributes['spinner'] && !file_exists($otherAttributes['spinner'])) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): unable to locate spinner image.\n";
								return false;
							}
						// initialize
							if (!$label && $type == 'button') $label = 'Go';
							if (!$label && $type == 'submit') $label = 'Submit';
							if (@$otherAttributes['spinner']) {
								@$eventHandlers['onClick'] = trim('this.innerHTML=' . '"' . @$otherAttributes['spinner_text'] . '&nbsp;<img src=\"' . $otherAttributes['spinner'] .  '\" />&nbsp;"; ' . @$eventHandlers['onClick']);
								unset($otherAttributes['spinner']);
							}
						// return
							$field = "<button type='" . $type . "'";
							if ($name) $field .= " name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							$field .= ">";
							$field .= $label;
							$field .= "</button>";
							return $field;
							break;
				/* ------------------------- */
					case 'image':
						// validate
							if (!$otherAttributes['src']) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): No image for submit button.\n";
								return false;
							}
							if ($otherAttributes && !is_array($otherAttributes)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): otherAttributes must be an array.\n";
								return false;
							}
							if ($eventHandlers && !is_array($eventHandlers)) {
								$tl->page['console'] .= __FUNCTION__ . " (" . $name . "): eventHandlers must be an array.\n";
								return false;
							}
						// return
							$field = "<input type='image'";
							$field .= " src='" . $otherAttributes['src'] . "'";
							if ($name) $field .= " name='" . $name . "' id='" . $name . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'cancel_and_return':
						// initialize
							if (!$label) $label = 'Cancel';
						// return
							$field = "<input type='button'";
							if ($name) $field .= " name='" . $name . "' id='" . $name . "'";
							$field .= " value='" . htmlspecialchars($label, ENT_QUOTES) . "'";
							if ($class) $field .= " class='" . $class . "'";
							if ($otherAttributes && is_array($otherAttributes)) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
							@$eventHandlers['onClick'] .= "window.history.back(); return false;";
							foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
							$field .= " />";
							return $field;
							break;
				/* ------------------------- */
					case 'honeypot':
						// validate
							if (!$name) break; // no name
						// return
							$field = "<style>.hpt{display:none;}</style><div class='hpt'>";
							$field .= "<input type='text' name='" . $name . "' id='" . $name . "' />";
							$field .= "</div>";
							return $field;
							break;
				/* ------------------------- */
					case 'timer':
						// validate
							if (!$name) break; // no name
						// return
							$field = "<style>.hpt{display:none;}</style><div class='hpt'>";
							$field .= "<input type='text' name='" . $name . "' id='" . $name . "' value='" . time() . "' />";
							$field .= "</div>";
							return $field;
							break;
			}
			
		}
	
		public function row($type, $name, $value = null, $mandatory = false, $labelPlaceholder = null, $class = null, $options = null, $maxlength = null, $otherAttributes = null, $truncateLabel = null, $eventHandlers = null, $duplicateRows = null) {

			global $tl;
			global $operators;

			if (!$type) {
				$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No element type specified.\n";
				return false;
			}

			if ($labelPlaceholder) {
				$result = explode('|', $labelPlaceholder);
				if (isset($result[0])) $label = $result[0];
				if (isset($result[1])) $placeholder = $result[1];
			}
			
			if (!isset($label)) $label = null;
			if (!isset($placeholder)) $placeholder = null;
			
			if (floatval($truncateLabel)) $label = $parser->truncate($label, 'character', $truncateLabel, '', '', '');
			
			if ($type == 'checkbox' || $type == 'checkbox_stacked_bootstrap' || $type == 'button' || $type == 'submit' || $type == 'cancel' || $type == 'cancel_and_return') $label = null;

			$row = $this->rowStart($name, $label);
			$row .= "      " . $this->input($type, $name, $value, $mandatory, $labelPlaceholder, $class, $options, $maxlength, $otherAttributes, $truncateLabel, $eventHandlers) . "\n";
			
			if ($duplicateRows) {
				for ($counter = 1; $counter <= $duplicateRows; $counter++) {
					$row .= "      <div id='expandingRow_" . $counter . "'></div>\n";
				}
				
				$row .= "      <div id='rowLink'>\n";
				$row .= "        <a href='javascript:void(0)' onClick='addRow(" . $duplicateRows . "); return false;'>Add more...</a>\n";
				$row .= "        <input type='hidden' name='numberOfRows' id='numberOfRows' value='0' />\n";
				$row .= "      </div>\n";
				
				$row .= "      <script type='text/javascript'>\n";
				$row .= "        function addRow(maxRows) {\n";
				$row .= "          document.getElementById('numberOfRows').value++;\n";
				$row .= "          if (document.getElementById('numberOfRows').value > maxRows) alert('Maximum number of rows reached.');\n";
				$row .= "          else {\n";
				$row .= "            var field =\n";
				$row .= "              " . '"' . $this->input($type, $name, $value, $mandatory, $labelPlaceholder, $class, $options, $maxlength, $otherAttributes, $truncateLabel, $eventHandlers) . '"' . ";\n";
				$row .= "            document.getElementById('expandingRow_' + document.getElementById('numberOfRows').value).innerHTML = field.replace(" . '"' . $name . '", "' . $name . '"' . " + " . '"_"' . " + document.getElementById('numberOfRows').value);\n";
				$row .= "          }\n";
				$row .= "        }\n";
				$row .= "      </script>\n";
	
			}

			$row .= $this->rowEnd();

			return $row;

		}

		public function rowStart($name = null, $label = null, $truncate = false, $class = null, $stacked = true) {

			global $parser;

			if (!isset($name)) $name = null;
			if (!isset($label)) $label = null;
			
			if (floatval($truncate)) $label = $parser->truncate($label, 'character', $truncate, '', '', '');
			
			$row = "<!-- " . $name . " -->\n";
			$row .= "  <div id='" . trim('formContainer_' . $name, '_') . "' class='form-group" . ($class ? ' ' . $class : false) . "'>\n";

			if ($this->style == 'horizontal') {
				$row .= "    <label for='" . $name . "' id='" . trim('formLabel_' . $name, '_') . "' class='col-lg-3 col-md-3 col-sm-4 col-xs-12 control-label'>" . $label . "</label>\n";
				$row .= "    <div id='" . trim('formField_' . $name, '_') . "' class='col-lg-9 col-md-9 col-sm-8 col-xs-12'>\n";
			}
			elseif ($this->style == 'stacked' || $this->style == 'inline') {
				$row .= "    <label for='" . $name . "' id='" . trim('formLabel_' . $name, '_') . "' class='control-label'>" . $label . "</label>\n";
			}

			return $row;
			
		}
			
		public function rowEnd($stacked = true) {
		
			$row = null;

			if ($this->style == 'horizontal') $row .= "    </div>\n";

			$row .= "  </div>\n";
						
			return $row;
			
		}
		
		public function start($name = null, $action = null, $method = 'post', $class = null, $otherAttributes = null, $eventHandlers = null) {
			
			global $tl;

			if (!$this->style) {
				$tl->page['console'] .= __CLASS__ . "->" . __FUNCTION__ . ": No form style specified.\n";
				return false;
			}
			elseif ($this->style == 'horizontal') $class = trim('form-horizontal ' . $class);
			elseif ($this->style == 'inline') $class = trim('form-inline ' . $class);

			$field = "<form role='form'";
			if ($name) $field .= " name='" . $name . "' id='" . $name . "'";
			if ($action) $field .= " action='" . $action . "'";
			if ($method) $field .= " method='" . $method . "'";
			if ($class) $field .= " class='" . $class . "'";
			if ($otherAttributes) foreach ($otherAttributes as $attribute => $attributeValue) $field .= " " . $attribute . "='" . trim($attributeValue) . "'";
			if ($eventHandlers) foreach ($eventHandlers as $event => $action) $field .= " " . $event . "='" . trim($action) . "'";
			$field .= ">\n";
			if ($name) $field .= "<input type='hidden' name='formName' id='formName' value='" . $name . "' />";
			
			return $field;
			
		}
		
		public function end() {
			return "</form>\n";
		}
		
		public function paginate($currentPage, $numberOfPages, $urlStructure) {

			/* 	Note that URL structure must contain a # where the
				page number will appear */
			
			// Calculations
				if (floatval($currentPage) > 1) $backEventHandler = array('onClick'=>'document.location.href="' . str_replace('#', floatval($currentPage) - 1, $urlStructure) . '"; return false;');
				else $backEventHandler = null;
									
				if (floatval($currentPage) < $numberOfPages) $nextEventHandler = array('onClick'=>'document.location.href="' . str_replace('#', floatval($currentPage) + 1, $urlStructure) . '"; return false;');
				else $nextEventHandler = null;
									
				$allPages = array();
				for ($counter = 1; $counter <= $numberOfPages; $counter++) {
					$allPages[$counter] = $counter;
				}
				
			// Display								
				$paginate = "<!-- Pagination -->\n";
				$paginate .= "  <div id='pagination' class='row'>\n";
				
				/* Back */		$paginate .= "    <div class='col-lg-4 col-md-4 col-sm-3 col-xs-3'>" . $this->input('button', 'paginateButtonBack', null, false, '<', 'btn btn-default btn-block', null, null, null, null, @$backEventHandler) . "</div>\n";
				/* Select */	$paginate .= "    <div class='col-lg-4 col-md-4 col-sm-6 col-xs-6'>" . $this->input('select', 'selectPage', floatval($currentPage), true, null, 'form-control', $allPages, null, null, null, array('onChange'=>'document.location.href="' . str_replace('#', '" + this.value + "', $urlStructure) . '"; return false;')) . "</div>\n";
				/* Next */		$paginate .= "    <div class='col-lg-4 col-md-4 col-sm-3 col-xs-3'>" . $this->input('button', 'paginateButtonBack', null, false, '>', 'btn btn-default btn-block', null, null, null, null, @$nextEventHandler) . "</div>\n";
					
				$paginate .= "  </div>\n";

			// Return
				return $paginate;
			
		}
		
	}
	
/*
	Form Builder

	::	DESCRIPTION
	
		Creates form fields and, if necessary, enclosing CSS scaffording to
		optimize form creation.

	::	DEPENDENT ON
	
		operators_TL
	
	::	VERSION HISTORY

	::	LICENSE
	
		Copyright (C) Timothy Quinn / Tidal Lock / Consolidated Biro
		
		Permission is hereby granted, free of charge, to any person
		obtaining a copy of this software and associated documentation
		files (the "Software"), to deal in the Software without
		restriction, including without limitation the rights to use,
		copy, modify, merge, publish, distribute, sublicense, and/or
		sell copies of the Software, and to permit persons to whom the
		Software is furnished to do so, subject to the following
		conditions:
		
		The above copyright notice and this permission notice shall be
		included in all copies or substantial portions of the Software.
		THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
		EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
		OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
		NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
		HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
		WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
		FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
		OTHER DEALINGS IN THE SOFTWARE.
*/

?>
