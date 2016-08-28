<?php
// This file is part of a local Moodle plugin
//
// You can redistribute it and/or modify it under the terms of the  GNU General Public License 
// as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
// This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
// without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// See the GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License along with Moodle. 
// If not, see <http://www.gnu.org/licenses/>.

/**
 * [File Documentation]
 *
 * @package   local_evaluationcalendar\models
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_evaluationcalendar\models;

use ArrayAccess;
use local_evaluationcalendar\api_object_serializer;

/**
 * Class base_model
 *
 * @category Class
 * @package  local_evaluationcalendar\models
 */
abstract class base_model implements ArrayAccess {

    /**
     * @param $array            base_model[]
     * @param $param            string
     * @param $comparison_value string
     * @return base_model|null
     */
    public static function select_instance_from_array($array, $param, $comparison_value) {
        $instance = null;
        $getter = evaluation_type::$getters[$param];
        if (!is_null($getter)) {
            foreach ($array as $element) {
                if ($element->$getter() === $comparison_value) {
                    $instance = $element;
                    break;
                }
            }
        }
        return $instance;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset An offset to check for.
     * @return boolean true on success or false on failure.
     *                      The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset) {
        return isset($this->$offset);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset The offset to retrieve.
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset) {
        return $this->$offset;
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value  The value to set.
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset) {
        unset($this->$offset);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString() {
        if (defined('JSON_PRETTY_PRINT')) {
            return json_encode(api_object_serializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
        } else {
            return json_encode(api_object_serializer::sanitizeForSerialization($this));
        }
    }
}