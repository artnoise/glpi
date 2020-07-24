<?php
/**
 * ---------------------------------------------------------------------
 * GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2015-2020 Teclib' and contributors.
 *
 * http://glpi-project.org
 *
 * based on GLPI - Gestionnaire Libre de Parc Informatique
 * Copyright (C) 2003-2014 by the INDEPNET Development Team.
 *
 * ---------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of GLPI.
 *
 * GLPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GLPI. If not, see <http://www.gnu.org/licenses/>.
 * ---------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access this file directly");
}

class DomainRecordType extends CommonDropdown
{
   static $rightname = 'dropdown';

   static public $knowtypes = [
      [
         'id'        => 1,
         'name'      => 'A',
         'comment'   => 'Host address',
         'fields'    => [],
      ], [
         'id'        => 2,
         'name'      => 'AAAA',
         'comment'   => 'IPv6 host address',
         'fields'    => [],
      ], [
         'id'        => 3,
         'name'      => 'ALIAS',
         'comment'   => 'Auto resolved alias',
         'fields'    => [],
      ], [
         'id'        => 4,
         'name'      => 'CNAME',
         'comment'   => 'Canonical name for an alias',
         'fields'    => [],
      ], [
         'id'        => 5,
         'name'      => 'MX',
         'comment'   => 'Mail eXchange',
         'fields'    => [
            [
               'label'       => 'Priority',
               'placeholder' => 'eg. 10',
            ],
            [
               'label'       => 'Server',
               'placeholder' => 'eg. mail.example.com',
            ],
         ],
      ], [
         'id'        => 6,
         'name'      => 'NS',
         'comment'   => 'Name Server',
         'fields'    => [],
      ], [
         'id'        => 7,
         'name'      => 'PTR',
         'comment'   => 'Pointer',
         'fields'    => [],
      ], [
         'id'        => 8,
         'name'      => 'SOA',
         'comment'   => 'Start Of Authority',
         'fields'    => [
            [
               'label'       => 'Primary name server',
               'placeholder' => 'eg. ns1.example.com',
            ],
            [
               'label'       => 'Primary contact',
               'placeholder' => 'eg. admin.example.com',
            ],
            [
               'label'       => 'Serial',
               'placeholder' => 'eg. 2020010101',
            ],
            [
               'label'       => 'Zone refresh timer',
               'placeholder' => 'eg. 86400',
            ],
            [
               'label'       => 'Failed refresh retry timer',
               'placeholder' => 'eg. 7200',
            ],
            [
               'label'       => 'Zone expiry timer',
               'placeholder' => 'eg. 1209600',
            ],
            [
               'label'       => 'Minimum TTL',
               'placeholder' => 'eg. 300',
            ],
         ],
      ], [
         'id'        => 9,
         'name'      => 'SRV',
         'comment'   => 'Location of service',
         'fields'    => [
            [
               'label'       => 'Priority',
               'placeholder' => 'eg. 0',
            ],
            [
               'label'       => 'Weight',
               'placeholder' => 'eg. 10',
            ],
            [
               'label'       => 'Port',
               'placeholder' => 'eg. 5060',
            ],
            [
               'label'       => 'Target',
               'placeholder' => 'eg. sip.example.com',
            ],
         ],
      ], [
         'id'        => 10,
         'name'      => 'TXT',
         'comment'   => 'Descriptive text',
         'fields'    => [
            [
               'label'       => 'TXT record data',
               'placeholder' => 'Your TXT record data',
               'quote_value' => true,
            ],
         ],
      ], [
         'id'        => 11,
         'name'      => 'CAA',
         'comment'   => 'Certification Authority Authorization',
         'fields'    => [
            [
               'label'       => 'Flag',
               'placeholder' => 'eg. 0',
            ],
            [
               'label'       => 'Tag',
               'placeholder' => 'eg. issue',
            ],
            [
               'label'       => 'Value',
               'placeholder' => 'eg. letsencrypt.org',
               'quote_value' => true,
            ],
         ],
      ]
   ];


   function getAdditionalFields() {
      return [
         [
            'name'  => 'fields',
            'label' => __('Fields'),
            'type'  => 'fields',
         ]
      ];
   }

   public function displaySpecificTypeField($ID, $field = []) {
      $field_name  = $field['name'];
      $field_type  = $field['type'];
      $field_value = $this->fields[$field_name];

      switch ($field_type) {
         case 'fields':
            $printable = json_encode(json_decode($field_value), JSON_PRETTY_PRINT);
            echo '<textarea name="' . $field_name . '" cols="75" rows="25">' . $printable . '</textarea >';
            break;
      }
   }

   public function prepareInputForAdd($input) {
      if (!array_key_exists('fields', $input)) {
         $input['fields'] = '[]';
      } else {
         $input['fields'] = Toolbox::cleanNewLines($input['fields']);
      }

      if (!$this->validateFieldsDescriptor($input['fields'])) {
         return false;
      }

      return parent::prepareInputForAdd($input);
   }

   public function prepareInputForUpdate($input) {
      if (array_key_exists('fields', $input)) {
         $input['fields'] = Toolbox::cleanNewLines($input['fields']);
         if (!$this->validateFieldsDescriptor($input['fields'])) {
            return false;
         }
      }

      return parent::prepareInputForUpdate($input);
   }

   /**
    * Validate fields descriptor.
    *
    * @param string $fields_str  Value of "fields" field (should be a JSON encoded string).
    *
    * @return bool
    */
   private function validateFieldsDescriptor($fields_str): bool {
      if (!is_string($fields_str)) {
         Session::addMessageAfterRedirect(__('Invalid JSON used to define fields.'), true, ERROR);
         return false;
      }

      $fields = json_decode($fields_str, true);
      if (json_last_error() !== JSON_ERROR_NONE) {
         $fields_str = stripslashes(preg_replace('/(\\\r|\\\n)/', '', $fields_str));
         $fields = json_decode($fields_str, true);
      }
      if (json_last_error() !== JSON_ERROR_NONE || !is_array($fields)) {
         Session::addMessageAfterRedirect(__('Invalid JSON used to define fields.'), true, ERROR);
         return false;
      }

      foreach ($fields as $field) {
         if (!is_array($field) || !array_key_exists('label', $field) || !is_string($field['label'])
             || (array_key_exists('placeholder', $field) && !is_string($field['placeholder']))
             || (array_key_exists('quote_value', $field) && !is_bool($field['quote_value']))
             || count(array_diff(array_keys($field), ['label', 'placeholder', 'quote_value'])) > 0) {
            Session::addMessageAfterRedirect(
               __('Valid field descriptor properties are: label (string, mandatory), placeholder (string, optionnal), quote_value (boolean, optional).'),
               true,
               ERROR
            );
            return false;
         }
      }

      return true;
   }

   static function getTypeName($nb = 0) {
      return _n('Record type', 'Records types', $nb);
   }

   public static function getDefaults() {
      return array_map(
         function($e) {
            $e['is_recursive'] = 1;
            $e['fields'] = json_encode($e['fields']);
            return $e;
         },
         self::$knowtypes
      );
   }

   /**
    * Display ajax form used to fill record data.
    *
    * @param string $input_id    Id of input used to get/store record data.
    */
   function showDataAjaxForm(string $input_id) {
      $rand = mt_rand();

      echo '<form id="domain_record_data' . $rand . '">';
      echo '<table class="tab_cadre_fixe">';

      $fields = json_decode($this->fields['fields'] ?? '[]', true);
      if (empty($fields)) {
         $fields = [
            [
               'label' => __('Data'),
            ],
         ];
      }

      foreach ($fields as $index => $field) {
         $placeholder = Html::entities_deep($field['placeholder'] ?? '');
         $quote_value = $field['quote_value'] ?? false;

         echo '<tr class="tab_bg_1">';
         echo '<td>' . $field['label'] . '</td>';
         echo '<td>';
         echo '<input data-index="' . $index . '" '
            . 'placeholder="' . $placeholder . '" '
            . 'data-quote-value="' . ($quote_value ? 'true' : 'false') . '" '
            . (!$quote_value ? 'pattern="[^\s]+" ' : '') // prevent usage of spaces in unquoted values
            . ' />';
         echo '</td>';
         echo '</tr>';
      }

      echo '<tr class="tab_bg_2">';
      echo '<td colspan="2" class="right">';
      echo Html::submit('<i class="fas fa-save"></i> ' . _x('button', 'Save'));
      echo '</td>';
      echo '</tr>';

      echo '</table>';
      echo '</form>';

      $js = <<<JAVASCRIPT
         $(
            function () {
               var form = $('#domain_record_data{$rand}');

               // Put existing data into fields
               var data_to_copy = $('#{$input_id}').val();
               form.find('input').each(
                  function () {
                     var endoffset = 0;
                     if ($(this).data('quote-value')) {
                        // Search for closing quote (quote inside value are escaped by a \)
                        do {
                           endoffset = endoffset + 1; // move to next char (ignore opening or escaped quote)
                           endoffset = data_to_copy.indexOf('" ', endoffset);
                        } while (endoffset !== -1 && data_to_copy.charAt(endoffset - 1) == '\\\');

                        if (endoffset !== -1) {
                           endoffset += 1; // capture closing quote
                        }
                     } else {
                        endoffset = data_to_copy.indexOf(' ');
                     }

                     if (endoffset === -1) {
                        endoffset = data_to_copy.length; // get whole value if no separator found
                     }

                     var value = data_to_copy.substring(0, endoffset).trim();
                     if ($(this).data('quote-value')) {
                        value = value.replace(/^"/, '').replace(/"$/, ''); // trim surrounding quotes
                        value = value.replace('\\\"', '"'); // unescape quotes
                     }
                     $(this).val(value);

                     // "endoffset + 1" to strip also ' ' separator char
                     data_to_copy = data_to_copy.substring(endoffset + 1);
                  }
               );

               // Copy values into data input on submit
               form.on(
                  'submit',
                  function(event) {
                     event.preventDefault();

                     var data_tokens = [];
                     $(this).find('input').each(
                        function () {
                           var value = $(this).val();
                           if ($(this).data('quote-value') && !value.match('/^".*"$/')) {
                              value = '"' + value.replace('"', '\\\"') + '"';
                           }
                           data_tokens.push(value);
                        }
                     );

                     $('#{$input_id}').val(data_tokens.join(' '));
                  }
               );
            }
         );
JAVASCRIPT;
      echo Html::scriptBlock($js);
   }
}
