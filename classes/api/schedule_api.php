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
 * @package   local_evaluationcalendar\api
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_evaluationcalendar\api;

use local_evaluationcalendar\api_client;
use local_evaluationcalendar\api_exception;
use local_evaluationcalendar\api_object_serializer;
use local_evaluationcalendar\models\schedule;

/**
 * Class calendar_api
 *
 * @category Class
 * @package  local_evaluationcalendar\api
 */
class schedule_api extends base_api {

    /**
     * Class path of the returning model of the api
     *
     * @var string
     */
    private static $_model = '\local_evaluationcalendar\models\schedule';

    /**
     * Constructor
     *
     * @param api_client |null $apiClient The api client to use
     */
    function __construct($apiClient = null) {
        parent::__construct($apiClient);
    }

    /**
     * Calls the api_client to get the schedules csv
     *
     * @param string $encoding  (optional) The encoding of the csv
     * @param string $delimiter (optional) The columns delimiter of the csv
     * @param bool   $dirty_src (optional) If set to true it will clean the scv thinking the source was from html and that all rows
     *                          are separated by <br> tag
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return schedule[]
     * @throws api_exception on non-2xx response
     */
    public function get_schedules($encoding = 'ISO-8859-1', $delimiter = ';', $dirty_src = false, $arguments = null) {
        list($response, $statusCode, $httpHeader) =
                $this->get_schedules_with_http_info($encoding, $delimiter, $dirty_src, $arguments);
        return $response;
    }

    /**
     * Calls the api_client to get the schedules csv together with the response status and header
     *
     * @param string $encoding  (optional) The encoding of the csv
     * @param string $delimiter (optional) The columns delimiter of the csv
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return array schedule[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_schedules_with_http_info($encoding = 'UTF-8', $delimiter = ';', $dirty_src = false, $arguments = null) {

        // parse inputs
        $queryParams = array();

        // query params
        if ($arguments !== null) {
            foreach ($arguments as $arg => $value) {
                $queryParams[$arg] = $value;
            }
        }

        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) =
                    parent::call_api_client('', $queryParams, 'string', api_client::$GET, 'text/html; charset=' . $encoding);

            // convert encodings
            if (strcmp('UTF-8', $encoding) !== 0) {
                $response = mb_convert_encoding($response, 'UTF-8', $encoding);
                $response = normalizer_normalize($response);
            }

            // clean the response
            if ($dirty_src) {
                $response = str_replace("<br>", "[breakpoint]", $response);
                $response = strip_tags($response);
                $response = trim(str_replace(array("\n", "\r"), '', $response));
            }

            // convert csv text to array
            $headers = array("course_name", "course_scheme", "course_code", "semester", "school_year", "course_field",
                    "subject_designation_shift", "subject_code_abbr", "subject_abbr", "year", "week_day", "time_start", "time_end",
                    "weeks", "room", "teacher");
            $response = parent::parse_csv_to_assoc_array($response, '[breakpoint]', $delimiter, false, $headers);

            // convert array to object
            $response = json_decode(json_encode($response), false);

            // deserialize object to model
            $response = api_object_serializer::deserialize($response, self::$_model . '[]', null);
            return array($response, $statusCode, $httpHeader);
        } catch (api_exception $e) {
            throw $e;
        }
    }

}