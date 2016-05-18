<?php
/**
 * This file is part of a local Moodle plugin
 *
 * You can redistribute it and/or modify it under the terms of the  GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * This plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with Moodle.
 * If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * [File Documentation]
 *
 * @package local_pfc\api
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pfc\api;
use local_pfc_api_client;
use local_pfc_api_object_serializer;
use local_pfc_api_exception;


/**
 * Class base_api
 *
 * @category Class
 * @package local_pfc\api
 */
class base_api
{

    /**
     * API Client
     * @var \local_pfc_api_client instance of the api_client
     */
    protected $apiClient;

    /**
     * Constructor
     * @param \local_pfc_api_client|null $apiClient The api client to use
     */
    function __construct($apiClient = null)
    {
        if ($apiClient == null) {
            $apiClient = new local_pfc_api_client();
            $apiClient->getConfig()->setHost('https://apis.ipleiria.pt/dev/calendarios-avaliacoes/v1');
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     * @return \local_pfc_api_client get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     * @param \local_pfc_api_client $apiClient set the API client
     * @return base_api
     */
    public function setApiClient(local_pfc_api_client $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }


    public function callApiClient($resourcePath, $method, $queryParams, $responseType){
        $_header_accept = local_pfc_api_client::selectHeaderAccept(array('application/json'));
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = local_pfc_api_client::selectHeaderContentType(array());

        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath, $method, $queryParams,
                $headerParams, $responseType
            );

            if (!$response) {
                return array(null, $statusCode, $httpHeader);
            }

            $response_obj = local_pfc_api_object_serializer::deserialize($response->data, $responseType, null);
            return array($response_obj, $statusCode, $httpHeader);

        } catch (local_pfc_api_exception $e) {
            $response = $e->getResponseBody();
            $httpHeader = $e->getResponseHeaders();
            switch ($e->getCode()) {
                case 200:
                    $data = local_pfc_api_object_serializer::deserialize($response->data, $responseType, null);
                    $e->setResponseObject($data);
                    break;
                case 500:
                    $data = local_pfc_api_object_serializer::deserialize($response, '\local_pfc\models\error', null);
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }
}