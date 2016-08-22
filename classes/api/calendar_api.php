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
 * @package   local_pfc\api
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pfc\api;

use local_pfc\api_client;
use local_pfc\api_exception;
use local_pfc\models\calendar;


/**
 * Class calendar_api
 * @category Class
 * @package  local_pfc\api
 */
class calendar_api extends base_api
{

    /**
     * Class path of the returning model of the api
     * @var string
     */
    private static $_model = '\local_pfc\models\calendar';

    /**
     * Constructor
     * @param api_client |null $apiClient The api client to use
     */
    function __construct($apiClient = null)
    {
        parent::__construct($apiClient);
    }

    /**
     * Devolve uma lista de calend\u00E1rios.
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return calendar[]
     * @throws api_exception on non-2xx response
     */
    public function get_calendars($q = null, $fields = null, $sort = null, $arguments = null)
    {
        list($response, $statusCode, $httpHeader) = $this->get_calendars_with_http_info($q, $fields, $sort, $arguments);
        return $response;
    }


    /**
     * Devolve uma lista de calendários.
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return array calendar[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_calendars_with_http_info($q = null, $fields = null, $sort = null, $arguments = null)
    {

        // parse inputs
        $resourcePath = \local_pfc_config::Instance()->api_paths['calendars'];
        $queryParams = array();

        // query params
        if ($q !== null) {
            $queryParams['q'] = $this->apiClient->getSerializer()->toQueryValue($q);
        }
        if ($fields !== null) {
            $queryParams['fields'] = $this->apiClient->getSerializer()->toQueryValue($fields);
        }
        if ($sort !== null) {
            $queryParams['sort'] = $this->apiClient->getSerializer()->toQueryValue($sort);
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
            return parent::callApiClient($resourcePath, api_client::$GET, $queryParams, self::$_model . '[]');
        } catch (api_exception $e) {
            throw $e;
        }
    }

}