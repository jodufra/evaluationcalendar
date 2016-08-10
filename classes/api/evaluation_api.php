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
 * @package local_pfc\api
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pfc\api;
use local_pfc\api_client;
use local_pfc\api_exception;
use local_pfc\models\evaluation;


/**
 * Class evaluation_api
 *
 * @category Class
 * @package local_pfc\api
 */
class evaluation_api extends base_api
{

    /**
     * Class path of the returning model of the api
     * @var string
     */
    private static $_model = '\local_pfc\models\evaluation';

    /**
     * Constructor
     * @param api_client |null $apiClient The api client to use
     */
    function __construct($apiClient = null)
    {
        parent::__construct($apiClient);
    }


    /**
     *
     * Gets evaluations list
     *
     * @param string $q Permite efetuar uma pesquisa global sobre varios campos (optional)
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return evaluation[]
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations($q = null, $fields = null, $sort = null)
    {
        list($response, $statusCode, $httpHeader) = $this->get_evaluations_with_http_info ($q, $fields, $sort);
        return $response;
    }

    /**
     *
     * Gets evaluations list
     *
     * @param string $q Permite efetuar uma pesquisa global sobre varios campos (optional)
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return array evaluation[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations_with_http_info($q = null, $fields = null, $sort = null)
    {
        // parse inputs
        $resourcePath = \local_pfc_config::$API_PATHS['evaluations'];
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

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // make the API Call
        try {
            return parent::callApiClient($resourcePath, api_client::$GET, $queryParams, self::$_model.'[]');
        } catch (api_exception $e) {
            throw $e;
        }
    }
    
    
    /**
     *
     * Devolve uma lista de avaliações consoante a lista de códigos de Unidades Curriculares.
     *
     * @param string $uc_list Lista de c\u00F3digos de Unidades Curriculares (ex.:9119238,9119255) (required)
     * @return evaluation[]
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations_from_ucs($uc_list)
    {
        list($response, $statusCode, $httpHeader) = $this->get_evaluations_form_ucs_with_http_info ($uc_list);
        return $response;
    }

    /**
     *
     * Devolve uma lista de avaliações consoante a lista de códigos de Unidades Curriculares.
     *
     * @param string $uc_list Lista de c\u00F3digos de Unidades Curriculares (ex.:9119238,9119255) (required)
     * @return array evaluation[], HTTP status code, HTTP response headers (array of strings)
     * @throws api_exception on non-2xx response
     */
    public function get_evaluations_form_ucs_with_http_info($uc_list)
    {

        // verify the required parameter 'uc_list' is set
        if ($uc_list === null) {
            throw new api_exception('Missing the required parameter $uc_list when calling evaluations_get');
        }

        // parse inputs
        $resourcePath = \local_pfc_config::$API_PATHS['evaluations_ucs'];
        $queryParams = array();

        // query params
        if ($uc_list !== null) {
            $queryParams['listaUc'] = $this->apiClient->getSerializer()->toQueryValue($uc_list);
        }

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // make the API Call
        try {
            return parent::callApiClient($resourcePath, api_client::$GET, $queryParams, self::$_model.'[]');
        } catch (api_exception $e) {
            throw $e;
        }
    }

}