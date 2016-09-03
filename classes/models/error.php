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

/**
 * Class error
 *
 * @category Class
 * @package  local_evaluationcalendar\models
 */
class error extends base_model {
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    static $types = array(
            'status_code' => 'int',
            'message' => 'string',
            'description' => 'string'
    );

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     *
     * @var string[]
     */
    static $attributeMap = array(
            'status_code' => 'code',
            'message' => 'message',
            'description' => 'description'
    );

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    static $setters = array(
            'status_code' => 'set_status_code',
            'message' => 'set_message',
            'description' => 'set_description'
    );

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    static $getters = array(
            'status_code' => 'get_status_code',
            'message' => 'get_message',
            'description' => 'get_description'
    );

    /**
     * $status_code HTTP error code
     *
     * @var int
     */
    protected $status_code;

    /**
     * $message Error message
     *
     * @var string
     */
    protected $message;

    /**
     * $message Error more detailed message
     *
     * @var string
     */
    protected $description;

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property value to initialize the model
     */
    public function __construct(array $data = null) {

        if ($data != null) {
            $this->status_code = $data["status_code"];
            $this->message = $data["message"];
            $this->description = $data["description"];
        }
    }

    /**
     * Get array of property to type mappings. Used for (de)serialization
     *
     * @return string[]
     */
    static function types() {
        return self::$types;
    }

    /**
     * Get array of attributes where the key is the local name, and the value is the original name
     *
     * @return string[]
     */
    static function attributeMap() {
        return self::$attributeMap;
    }

    /**
     * Get array of attributes to setter functions (for deserialization of responses)
     *
     * @return string[]
     */
    static function setters() {
        return self::$setters;
    }

    /**
     * Get array of attributes to getter functions (for serialization of requests)
     *
     * @return string[]
     */
    static function getters() {
        return self::$getters;
    }

    /**
     * @return int get StatusCode
     */
    public function get_status_code() {
        return $this->status_code;
    }

    /**
     * @param int $status_code
     */
    public function set_status_code($status_code) {
        $this->status_code = $status_code;
    }

    /**
     * @return string get Message
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function set_message($message) {
        $this->message = $message;
    }

    /**
     * @return string get Description
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function set_description($description) {
        $this->description = $description;
    }


}