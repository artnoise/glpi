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

namespace tests\units\Glpi\System\Requirement;

class PhpVersion extends \GLPITestCase {

   protected function versionsProvider() {
      return [
         [
            'min_version' => GLPI_MIN_PHP,
            'max_version' => null,
            'validated'   => true,
            'messages'    => ['PHP version is >= ' . GLPI_MIN_PHP . '.']
         ],
         [
            'min_version' => '20.7',
            'max_version' => null,
            'validated'   => false,
            'messages'    => ['PHP version must be >= 20.7.']
         ],
         [
            'min_version' => null,
            'max_version' => '20.7',
            'validated'   => true,
            'messages'    => ['PHP version is < 20.7.']
         ],
         [
            'min_version' => null,
            'max_version' => GLPI_MIN_PHP,
            'validated'   => false,
            'messages'    => ['PHP version must be < ' . GLPI_MIN_PHP . '.']
         ],
         [
            'min_version' => GLPI_MIN_PHP,
            'max_version' => '20.7',
            'validated'   => true,
            'messages'    => ['PHP version is >= ' . GLPI_MIN_PHP . ' and < 20.7.']
         ],
         [
            'min_version' => '1.0',
            'max_version' => GLPI_MIN_PHP,
            'validated'   => false,
            'messages'    => ['PHP version must be >= 1.0 and < ' . GLPI_MIN_PHP . '.']
         ],
         [
            'min_version' => '20.7',
            'max_version' => '21.5',
            'validated'   => false,
            'messages'    => ['PHP version must be >= 20.7 and < 21.5.']
         ],
      ];
   }

   /**
    * @dataProvider versionsProvider
    */
   public function testCheck($min_version, $max_version, bool $validated, array $messages) {
      $this->newTestedInstance($min_version, $max_version);
      $this->boolean($this->testedInstance->isValidated())->isEqualTo($validated);
      $this->array($this->testedInstance->getValidationMessages())
         ->isEqualTo($messages);
   }

   public function testInvalidParameters() {
      $this->newTestedInstance(null, null);
      $this->exception(
         function() {
            $this->testedInstance->isValidated();
         }
      )->isInstanceOf(\LogicException::class)
       ->message->contains('Either min or max versions must be defined');
   }
}
