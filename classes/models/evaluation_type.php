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
 * Class evaluation_type
 *
 * @category Class
 * @package  local_evaluationcalendar\models
 */
class evaluation_type extends base_model {

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    static $types = array(
            'id' => 'string',
            'description' => 'string',
            'abbreviation' => 'string',
            'created_at' => 'DateTime',
            'updated_at' => 'DateTime'
    );

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     *
     * @var string[]
     */
    static $attributeMap = array(
            'id' => 'id',
            'description' => 'descricao',
            'abbreviation' => 'abreviatura',
            'created_at' => 'createdAt',
            'updated_at' => 'updatedAt'
    );

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    static $setters = array(
            'id' => 'set_id',
            'description' => 'set_description',
            'abbreviation' => 'set_abbreviation',
            'created_at' => 'set_created_at',
            'updated_at' => 'set_updated_at'
    );

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    static $getters = array(
            'id' => 'get_id',
            'description' => 'get_description',
            'abbreviation' => 'get_abbreviation',
            'created_at' => 'get_created_at',
            'updated_at' => 'get_updated_at'
    );

    /**
     * $id Evaluation type id.
     *
     * @var string
     */
    protected $id;

    /**
     * $description Evaluation type description.
     *
     * @var string
     */
    protected $description;

    /**
     * $abbreviation Evaluation type abbreviation.
     *
     * @var string
     */
    protected $abbreviation;

    /**
     * $updated_at Datetime of creation
     *
     * @var \DateTime
     */
    protected $created_at;

    /**
     * $updated_at Datetime of last update
     *
     * @var \DateTime
     */
    protected $updated_at;

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property value initializing the model
     */
    public function __construct(array $data = null) {
        if ($data != null) {
            $this->id = $data["id"];
            $this->description = $data["description"];
            $this->abbreviation = $data["abbreviation"];
            $this->created_at = $data["created_at"];
            $this->updated_at = $data["updated_at"];
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
    static function setters(){
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
     * @param $array            evaluation_type[]
     * @param $param            string
     * @param $comparison_value string
     * @return evaluation_type|null
     */
    static function select_instance_from_array($array, $param, $comparison_value) {
        return parent::select_instance_from_array($array, $param, $comparison_value);
    }

    /**
     * @return string
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function set_id($id) {
        $this->id = $id;
    }

    /**
     * @return string
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

    /**
     * @return string
     */
    public function get_abbreviation() {
        return $this->abbreviation;
    }

    /**
     * @param string $abbreviation
     */
    public function set_abbreviation($abbreviation) {
        $this->abbreviation = $abbreviation;
    }

    /**
     * @return \DateTime
     */
    public function get_created_at() {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function set_created_at($created_at) {
        $this->created_at = $created_at;
    }

    /**
     * @return \DateTime
     */
    public function get_updated_at() {
        return $this->updated_at;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function set_updated_at($updated_at) {
        $this->updated_at = $updated_at;
    }
}