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
use local_evaluationcalendar\models\evaluation;

/**
 * Class evaluation_api
 *
 * @category Class
 * @package  local_evaluationcalendar\api
 */
class evaluation_api extends base_api {

    /**
     * Class path of the returning model of the api
     *
     * @var string
     */
    private static $_model = '\local_evaluationcalendar\models\evaluation';

    /**
     * Constructor
     *
     * @param api_client |null $api_client The api client to use
     */
    function __construct($api_client = null) {
        parent::__construct($api_client);
    }

    /**
     * Calls the api_client to get a list of evaluations.
     *
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return evaluation[]
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations($q = null, $fields = null, $sort = null, $arguments = null) {
        list($response, $statusCode, $httpHeader) = $this->get_evaluations_with_http_info($q, $fields, $sort, $arguments);
        return $response;
    }

    /**
     * Calls the api_client to get a list of evaluations together with the response status and header
     *
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return array evaluation[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations_with_http_info($q = null, $fields = null, $sort = null, $arguments = null) {
        // parse inputs
        $resourcePath = \local_evaluationcalendar_config::Instance()->api_paths['evaluations'];
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
                $queryParams[$arg] = $this->api_client->getSerializer()->toQueryValue($value);
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

    /**
     * Calls the api_client to get a list of evaluations based on given list of subject codes.
     *
     * @param string $uc_list String containing several subject codes glued together with a comma (ex: "9119123,9119456,9119789")
     * @return evaluation[]
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations_from_ucs($uc_list) {
        list($response, $statusCode, $httpHeader) = $this->get_evaluations_form_ucs_with_http_info($uc_list);
        return $response;
    }

    /**
     * Calls the api_client to get a list of evaluations based on given list of subject identifiers.
     * Also returns the response status and header
     *
     * @param string $uc_list String containing several subject codes glued together with a comma (ex: "9119123,9119456,9119789")
     * @return array evaluation[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations_form_ucs_with_http_info($uc_list) {

        // verify the required parameter 'uc_list' is set
        if ($uc_list === null) {
            throw new api_exception('Missing the required parameter $uc_list when calling evaluations_get');
        }

        // parse inputs
        $resourcePath = \local_evaluationcalendar_config::Instance()->api_paths['evaluations_ucs'];
        $queryParams = array();

        // query params
        if ($uc_list !== null) {
            $queryParams['listaUc'] = $this->api_client->getSerializer()->toQueryValue($uc_list);
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