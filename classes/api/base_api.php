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
 * @package local_evaluationcalendar\api
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_evaluationcalendar\api;
use local_evaluationcalendar\api_client;
use local_evaluationcalendar\api_exception;
use local_evaluationcalendar\api_object_serializer;


/**
 * Class base_api
 *
 * @category Class
 * @package local_evaluationcalendar\api
 */
class base_api
{
    /**
     * Class path of the returning model of the api
     * @var string
     */
    private static $_error_model = '\local_evaluationcalendar\models\error';

    /**
     * API Client
     * @var api_client instance of the api_client
     */
    protected $apiClient;

    /**
     * Constructor
     * @param api_client|null $apiClient The api client to use
     */
    function __construct($apiClient = null)
    {
        if ($apiClient == null) {
            $apiClient = new api_client();
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     * @return api_client get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     * @param api_client $apiClient set the API client
     * @return base_api
     */
    public function setApiClient(api_client $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }


    public function callApiClient($resourcePath, $method, $queryParams, $responseType){
        $_header_accept = api_client::selectHeaderAccept(array('application/json'));
        $headerParams = array();
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = api_client::selectHeaderContentType(array());

        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath, $method, $queryParams,
                $headerParams, $responseType
            );

            if (!$response) {
                return array(null, $statusCode, $httpHeader);
            }

            if(is_string ($response) &&
                strcasecmp(substr($response, 0, 1), '{') == 0 &&
                strcasecmp(substr($response, -1), '}') == 0 ){
                $response = json_decode($response);
            }

            if(is_string ($response)){
                $response_obj = $response;
            }  else{
                $response_obj = api_object_serializer::deserialize($response->data, $responseType, null);
            }

            return array($response_obj, $statusCode, $httpHeader);

        } catch (\Exception $e) {
            if ($e instanceof api_exception) {
                $response = $e->getResponseBody();
                if(is_string ($response) &&
                    strcasecmp(substr($response, 0, 1), '{') == 0 &&
                    strcasecmp(substr($response, -1), '}') == 0 ){
                    $response = json_decode($response);
                }
                if(is_string ($response)){
                    $data = $response;
                }  else{
                    $data = api_object_serializer::deserialize($response, self::$_error_model, null);
                }
                $e->setResponseObject($data);
            } else {
                $e = new api_exception($e->getMessage(), 500);
            }
            throw $e;
        }
    }
}