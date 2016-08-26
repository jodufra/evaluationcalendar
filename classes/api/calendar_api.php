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
use local_evaluationcalendar\models\calendar;

/**
 * Class calendar_api
 *
 * @category Class
 * @package  local_evaluationcalendar\api
 */
class calendar_api extends base_api {

    /**
     * Class path of the returning model of the api
     *
     * @var string
     */
    private static $_model = '\local_evaluationcalendar\models\calendar';

    /**
     * Constructor
     *
     * @param api_client |null $apiClient The api client to use
     */
    function __construct($apiClient = null) {
        parent::__construct($apiClient);
    }

    /**
     * Calls the api_client to get a list of evaluation calendars.
     *
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return calendar[]
     * @throws api_exception on non-2xx response
     */
    public function get_calendars($q = null, $fields = null, $sort = null, $arguments = null) {
        list($response, $statusCode, $httpHeader) = $this->get_calendars_with_http_info($q, $fields, $sort, $arguments);
        return $response;
    }

    /**
     * Calls the api_client to get a list of evaluation calendars together with the response status and header
     *
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return array calendar[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_calendars_with_http_info($q = null, $fields = null, $sort = null, $arguments = null) {

        // parse inputs
        $resourcePath = \local_evaluationcalendar_config::Instance()->api_paths['calendars'];
        $queryParams = array();

        // query params
        if ($q !== null) {
            $queryParams['q'] = $this->api_client->getSerializer()->toQueryValue($q);
        }
        if ($fields !== null) {
            $queryParams['fields'] = $this->api_client->getSerializer()->toQueryValue($fields);
        }
        if ($sort !== null) {
            $queryParams['sort'] = $this->api_client->getSerializer()->toQueryValue($sort);
        }
        if ($arguments !== null) {
            foreach ($arguments as $arg => $value) {
                //$queryParams[$arg] = $this->apiClient->getSerializer()->toQueryValue($value);
                $queryParams[$arg] = $value;
            }
        }

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // make the API Call
        try {
            return parent::call_api_client($resourcePath, $queryParams, self::$_model . '[]');
        } catch (api_exception $e) {
            throw $e;
        }
    }

}