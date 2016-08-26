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

use local_evaluationcalendar\api_exception;

/**
 * Class evaluation
 *
 * @category Class
 * @package  local_evaluationcalendar\models
 */
class evaluation extends base_model {

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    static $types = array(
            'id' => 'string',
            'date_begin' => 'string',
            'date_end' => 'string',
            'description' => 'string',
            'local' => 'string',
            'room_type' => 'string',
            'room' => 'string',
            'evaluation_type_id' => 'string',
            'course_id' => 'int',
            'siges_code' => 'int',
            'calendar_id' => 'string',
            'user_id' => 'string',
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
            'date_begin' => 'dataInicio',
            'date_end' => 'dataFim',
            'description' => 'descricao',
            'local' => 'local',
            'room_type' => 'tipoSala',
            'room' => 'sala',
            'evaluation_type_id' => 'idTipoAvaliacao',
            'course_id' => 'idUnidadeCurricular',
            'siges_code' => 'codigoSiges',
            'calendar_id' => 'idCalendario',
            'user_id' => 'idUtilizador',
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
            'date_begin' => 'set_date_begin',
            'date_end' => 'set_date_end',
            'description' => 'set_description',
            'local' => 'set_local',
            'room_type' => 'set_room_type',
            'room' => 'set_room',
            'evaluation_type_id' => 'set_evaluation_type_id',
            'course_id' => 'set_course_id',
            'siges_code' => 'set_siges_code',
            'calendar_id' => 'set_calendar_id',
            'user_id' => 'set_user_id',
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
            'date_begin' => 'get_date_begin',
            'date_end' => 'get_date_end',
            'description' => 'get_description',
            'local' => 'get_local',
            'room_type' => 'get_room_type',
            'room' => 'get_room',
            'evaluation_type_id' => 'get_evaluation_type_id',
            'course_id' => 'get_course_id',
            'siges_code' => 'get_siges_code',
            'calendar_id' => 'get_calendar_id',
            'user_id' => 'get_user_id',
            'created_at' => 'get_created_at',
            'updated_at' => 'get_updated_at'
    );

    /**
     * $id Evaluation id.
     *
     * @var string
     */
    protected $id;

    /**
     * $date_begin Evaluation begin date.
     *
     * @var string
     */
    protected $date_begin;

    /**
     * $date_end Evaluation end date.
     *
     * @var string
     */
    protected $date_end;

    /**
     * $description Evaluation description.
     *
     * @var string
     */
    protected $description;

    /**
     * $local Evaluation local (SALADEAULA|NAOSEAPLICA|OUTROLOCAL).
     *
     * @var string
     */
    protected $local;

    /**
     * $room_type Evaluation room type description.
     *
     * @var string
     */
    protected $room_type;

    /**
     * $evaluation_type_id Evaluation room.
     *
     * @var string
     */
    protected $room;

    /**
     * $evaluation_type_id Evaluation local id.
     *
     * @var string
     */
    protected $evaluation_type_id;

    /**
     * $course_id course id.
     *
     * @var int
     */
    protected $course_id;

    /**
     * $siges_code Siges Code (ex: 9119102).
     *
     * @var int
     */
    protected $siges_code;

    /**
     * $calendar_id Calendar id.
     *
     * @var string
     */
    protected $calendar_id;

    /**
     * $calendar_id User id.
     *
     * @var string
     */
    protected $user_id;

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
     * @param mixed[] $data Associated array of property value to initialize the model
     */
    public function __construct(array $data = null) {

        if ($data != null) {
            $this->id = $data["id"];
            $this->date_begin = $data["date_begin"];
            $this->date_end = $data["date_end"];
            $this->description = $data["description"];
            $this->local = $data["local"];
            $this->room_type = $data["room_type"];
            $this->room = $data["room"];
            $this->evaluation_type_id = $data["evaluation_type_id"];
            $this->course_id = $data["course_id"];
            $this->siges_code = $data["siges_code"];
            $this->user_id = $data["user_id"];
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
     * @param $array            evaluation[]
     * @param $param            string
     * @param $comparison_value string
     * @return evaluation|null
     */
    public static function select_instance_from_array($array, $param, $comparison_value) {
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
    public function get_date_begin() {
        return $this->date_begin;
    }

    /**
     * @param string $date_begin
     */
    public function set_date_begin($date_begin) {
        $this->date_begin = $date_begin;
    }

    /**
     * @return string
     */
    public function get_date_end() {
        return $this->date_end;
    }

    /**
     * @param string $date_end
     */
    public function set_date_end($date_end) {
        $this->date_end = $date_end;
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
    public function get_local() {
        return $this->local;
    }

    /**
     * @param string $local
     * @throws api_exception When $local different then allowed values
     */
    public function set_local($local) {
        $allowed_values = array("SALADEAULA", "NAOSEAPLICA", "OUTROLOCAL");
        if (!in_array($local, $allowed_values)) {
            throw new api_exception("Invalid value for 'local', must be one of 'SALADEAULA', 'NAOSEAPLICA', 'OUTROLOCAL'");
        }
        $this->local = $local;
    }

    /**
     * @return string
     */
    public function get_room() {
        return $this->room;
    }

    /**
     * @param string $room
     */
    public function set_room($room) {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function get_room_type() {
        return $this->room_type;
    }

    /**
     * @param string $room_type
     */
    public function set_room_type($room_type) {
        $this->room_type = $room_type;
    }

    /**
     * @return string
     */
    public function get_evaluation_type_id() {
        return $this->evaluation_type_id;
    }

    /**
     * @param string $evaluation_type_id
     */
    public function set_evaluation_type_id($evaluation_type_id) {
        $this->evaluation_type_id = $evaluation_type_id;
    }

    /**
     * @return int
     */
    public function get_course_id() {
        return $this->course_id;
    }

    /**
     * @param int $course_id
     */
    public function set_course_id($course_id) {
        $this->course_id = $course_id;
    }

    /**
     * @return int
     */
    public function get_siges_code() {
        return $this->siges_code;
    }

    /**
     * @param int $siges_code
     */
    public function set_siges_code($siges_code) {
        $this->siges_code = $siges_code;
    }

    /**
     * @return string
     */
    public function get_calendar_id() {
        return $this->calendar_id;
    }

    /**
     * @param string $calendar_id
     */
    public function set_calendar_id($calendar_id) {
        $this->calendar_id = $calendar_id;
    }

    /**
     * @return string
     */
    public function get_user_id() {
        return $this->user_id;
    }

    /**
     * @param string $user_id
     */
    public function set_user_id($user_id) {
        $this->user_id = $user_id;
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