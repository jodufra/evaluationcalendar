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
 * Class schedule
 *
 * @category Class
 * @package  local_evaluationcalendar\models
 */
class schedule extends base_model {
    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @var string[]
     */
    static $types = array(
            "course_code" => "int",
            "semester" => "string",
            "school_year" => "string",
            "subject_code_abbr" => "int",
            "subject_designation_shift" => "string",
            "time_start" => "string",
            "week_day" => "string"
    );

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     *
     * @var string[]
     */
    static $attributeMap = array(
            "course_code" => "course_code",
            "semester" => "semester",
            "school_year" => "school_year",
            "subject_code_abbr" => "subject_code_abbr",
            "subject_designation_shift" => "subject_designation_shift",
            "time_start" => "time_start",
            "week_day" => "week_day"
    );

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    static $setters = array(
            "course_code" => "set_course_code",
            "semester" => "set_semester",
            "school_year" => "set_school_year",
            "subject_code_abbr" => "set_subject_code_abbr",
            "subject_designation_shift" => "set_subject_designation_shift",
            "time_start" => "set_time_start",
            "week_day" => "set_week_day"
    );

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    static $getters = array(
            "course_code" => "get_course_code",
            "semester" => "get_semester",
            "school_year" => "get_school_year",
            "subject_code_abbr" => "get_subject_code_abbr",
            "subject_designation_shift" => "get_subject_designation_shift",
            "time_start" => "get_time_start",
            "week_day" => "get_week_day"
    );

    /**
     * @var int
     */
    private $course_code;

    /**
     * @var string
     */
    private $semester;

    /**
     * @var string
     */
    private $school_year;

    /**
     * @var int
     */
    private $subject_code_abbr;

    /**
     * @var string
     */
    private $subject_designation_shift;

    /**
     * @var string
     */
    private $time_start;

    /**
     * @var string
     */
    private $week_day;

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property value to initialize the model
     */
    public function __construct(array $data = null) {

        if ($data != null) {
            $this->course_code = $data["course_code"];
            $this->semester = $data["semester"];
            $this->school_year = $data["school_year"];
            $this->subject_code_abbr = $data["subject_code_abbr"];
            $this->subject_designation_shift = $data["subject_designation_shift"];
            $this->time_start = $data["time_start"];
            $this->week_day = $data["week_day"];
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
     * @return int get CourseCode
     */
    public function get_course_code() {
        return $this->course_code;
    }

    /**
     * @param int $course_code
     */
    public function set_course_code($course_code) {
        $this->course_code = $course_code;
    }

    /**
     * @return string get Semester
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
     * @return string get LectiveYear
     */
    public function get_school_year() {
        return $this->school_year;
    }

    /**
     * @param string $school_year
     */
    public function set_school_year($school_year) {
        $this->school_year = $school_year;
    }

    /**
     * @return int get SubjectCodeAbbr
     */
    public function get_subject_code_abbr() {
        return $this->subject_code_abbr;
    }

    /**
     * @param int $subject_code_abbr
     */
    public function set_subject_code_abbr($subject_code_abbr) {
        $this->subject_code_abbr = $subject_code_abbr;
    }

    /**
     * @return string get SubjectDesignationShift
     */
    public function get_subject_designation_shift() {
        return $this->subject_designation_shift;
    }

    /**
     * @param string $subject_designation_shift
     */
    public function set_subject_designation_shift($subject_designation_shift) {
        $this->subject_designation_shift = $subject_designation_shift;
    }

    /**
     * @return string get TimeStart
     */
    public function get_time_start() {
        return $this->time_start;
    }

    /**
     * @param string $time_start
     */
    public function set_time_start($time_start) {
        $this->time_start = $time_start;
    }

    /**
     * @return string get WeekDay
     */
    public function get_week_day() {
        return $this->week_day;
    }

    /**
     * @param string $week_day
     */
    public function set_week_day($week_day) {
        $this->week_day = $week_day;
    }

}