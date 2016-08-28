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
 * Class calendar
 *
 * @category Class
 * @package  local_evaluationcalendar\models
 */
class calendar extends base_model {

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    static $types = array(
            'id' => 'string',
            'course_id' => 'int',
            'course_name' => 'string',
            'course_scheme_abbr' => 'string',
            'course_abbr' => 'string',
            'academic_organization' => 'string',
            'academic_year_id' => 'int',
            'academic_year' => 'string',
            'semester_id' => 'int',
            'semester' => 'string',
            'evaluation_epoch_id' => 'int',
            'evaluation_epoch' => 'string',
            'stage' => 'string',
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
            'course_id' => 'idCurso',
            'course_name' => 'nomeCurso',
            'course_scheme_abbr' => 'abreviaturaRegimeFrequencia',
            'course_abbr' => 'abrvCurso',
            'academic_organization' => 'unidadeOrganica',
            'academic_year_id' => 'idAnoLetivo',
            'academic_year' => 'anoLetivo',
            'semester_id' => 'idSemestre',
            'semester' => 'semestre',
            'evaluation_epoch_id' => 'idEpAval',
            'evaluation_epoch' => 'epocaAvaliacao',
            'stage' => 'estado',
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
            'course_id' => 'set_course_id',
            'course_name' => 'set_course_name',
            'course_scheme_abbr' => 'set_course_scheme_abbr',
            'course_abbr' => 'set_course_abbr',
            'academic_organization' => 'set_academic_organization',
            'academic_year_id' => 'set_academic_year_id',
            'academic_year' => 'set_academic_year',
            'semester_id' => 'set_semester_id',
            'semester' => 'set_semester',
            'evaluation_epoch_id' => 'set_evaluation_epoch_id',
            'evaluation_epoch' => 'set_evaluation_epoch',
            'stage' => 'set_stage',
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
            'course_id' => 'get_course_id',
            'course_name' => 'get_course_name',
            'course_scheme_abbr' => 'get_course_scheme_abbr',
            'course_abbr' => 'get_course_abbr',
            'academic_organization' => 'get_academic_organization',
            'academic_year_id' => 'get_academic_year_id',
            'academic_year' => 'get_academic_year',
            'semester_id' => 'get_semester_id',
            'semester' => 'get_semester',
            'evaluation_epoch_id' => 'get_evaluation_epoch_id',
            'evaluation_epoch' => 'get_evaluation_epoch',
            'stage' => 'get_stage',
            'created_at' => 'get_created_at',
            'updated_at' => 'get_updated_at'
    );
    /**
     * $id Calendar id.
     *
     * @var string
     */
    protected $id;

    /**
     * $course_id Course id.
     *
     * @var int
     */
    protected $course_id;

    /**
     * $course_name Course name.
     *
     * @var string
     */
    protected $course_name;

    /**
     * $course_scheme_abbr Course scheme abbreviation (D|PL).
     *
     * @var string
     */
    protected $course_scheme_abbr;

    /**
     * $course_abbr Course abbreviation.
     *
     * @var string
     */
    protected $course_abbr;

    /**
     * $academic_organization Academic organization name.
     *
     * @var int
     */
    protected $academic_organization;

    /**
     * $academic_year_id Academic year id.
     *
     * @var int
     */
    protected $academic_year_id;

    /**
     * $academic_year Academic year.
     *
     * @var string
     */
    protected $academic_year;

    /**
     * $semester_id Semester id.
     *
     * @var int
     */
    protected $semester_id;

    /**
     * $semester Semester.
     *
     * @var string
     */
    protected $semester;

    /**
     * $evaluation_epoch_id Evaluation Epoch id.
     *
     * @var int
     */
    protected $evaluation_epoch_id;

    /**
     * $evaluation_epoch Evaluation Epoch.
     * Normal, etc)
     *
     * @var string
     */
    protected $evaluation_epoch;

    /**
     * $stage Calendar Stage.
     *
     * @var string
     */
    protected $stage;

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
            $this->course_id = $data["course_id"];
            $this->course_name = $data["course_name"];
            $this->course_scheme_abbr = $data["course_scheme_abbr"];
            $this->course_abbr = $data["course_abbr"];
            $this->academic_organization = $data["academic_organization"];
            $this->academic_year_id = $data["academic_year_id"];
            $this->academic_year = $data["academic_year"];
            $this->semester_id = $data["semester_id"];
            $this->semester = $data["semester"];
            $this->evaluation_epoch_id = $data["evaluation_epoch_id"];
            $this->evaluation_epoch = $data["evaluation_epoch"];
            $this->stage = $data["stage"];
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
     * @param $array            calendar[]
     * @param $param            string
     * @param $comparison_value string
     * @return calendar|null
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
     * @return string
     */
    public function get_course_name() {
        return $this->course_name;
    }

    /**
     * @param string $course_name
     */
    public function set_course_name($course_name) {
        $this->course_name = $course_name;
    }

    /**
     * @return string
     */
    public function get_course_scheme_abbr() {
        return $this->course_scheme_abbr;
    }

    /**
     * @param string $course_scheme_abbr
     */
    public function set_course_scheme_abbr($course_scheme_abbr) {
        $this->course_scheme_abbr = $course_scheme_abbr;
    }

    /**
     * @return string
     */
    public function get_course_abbr() {
        return $this->course_abbr;
    }

    /**
     * @param string $course_abbr
     */
    public function set_course_abbr($course_abbr) {
        $this->course_abbr = $course_abbr;
    }

    /**
     * @return int
     */
    public function get_academic_organization() {
        return $this->academic_organization;
    }

    /**
     * @param int $academic_organization
     */
    public function set_academic_organization($academic_organization) {
        $this->academic_organization = $academic_organization;
    }

    /**
     * @return int
     */
    public function get_academic_year_id() {
        return $this->academic_year_id;
    }

    /**
     * @param int $academic_year_id
     */
    public function set_academic_year_id($academic_year_id) {
        $this->academic_year_id = $academic_year_id;
    }

    /**
     * @return string
     */
    public function get_academic_year() {
        return $this->academic_year;
    }

    /**
     * @param string $academic_year
     */
    public function set_academic_year($academic_year) {
        $this->academic_year = $academic_year;
    }

    /**
     * @return int
     */
    public function get_semester_id() {
        return $this->semester_id;
    }

    /**
     * @param int $semester_id
     */
    public function set_semester_id($semester_id) {
        $this->semester_id = $semester_id;
    }

    /**
     * @return string
     */
    public function get_semester() {
        return $this->semester;
    }

    /**
     * @param string $semester
     */
    public function set_semester($semester) {
        $this->semester = $semester;
    }

    /**
     * @return int
     */
    public function get_evaluation_epoch_id() {
        return $this->evaluation_epoch_id;
    }

    /**
     * @param int $evaluation_epoch_id
     */
    public function set_evaluation_epoch_id($evaluation_epoch_id) {
        $this->evaluation_epoch_id = $evaluation_epoch_id;
    }

    /**
     * @return string
     */
    public function get_evaluation_epoch() {
        return $this->evaluation_epoch;
    }

    /**
     * @param string $evaluation_epoch
     */
    public function set_evaluation_epoch($evaluation_epoch) {
        $this->evaluation_epoch = $evaluation_epoch;
    }

    /**
     * @return string
     */
    public function get_stage() {
        return $this->stage;
    }

    /**
     * @param string $stage
     * @throws api_exception When $stage different then allowed values
     */
    public function set_stage($stage) {
        $allowed_values = array("PORELABORAR", "EMELABORACAO", "EMAPROVACAO", "APROVADO", "PUBLICADO");
        if (!in_array($stage, $allowed_values)) {
            throw new api_exception("Invalid value for 'stage', must be one of 'PORELABORAR', 'EMELABORACAO', 'EMAPROVACAO', 'APROVADO', 'PUBLICADO'");
        }
        $this->stage = $stage;
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