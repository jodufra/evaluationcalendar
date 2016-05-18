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
use local_pfc_api_exception;
use local_pfc\models\calendar;


/**
 * Class calendar_api
 *
 * @category Class
 * @package local_pfc\api
 */
class calendar_api extends base_api
{

    /**
     * Possible url API paths
     * @var array
     */
    private static $paths = array(
        'calendars' => '/calendarios'
    );

    /**
     * Constructor
     * @param local_pfc_api_client |null $apiClient The api client to use
     */
    function __construct($apiClient = null)
    {
        parent::__construct($apiClient);
    }

    /**
     *
     * Devolve uma lista de calend\u00E1rios.
     *
     * @param string $q Permite efetuar uma pesquisa global sobre varios campos (optional)
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return calendar[]
     * @throws local_pfc_api_exception on non-2xx response
     */
    public function get_calendars($q = null, $fields = null, $sort = null)
    {
        list($response, $statusCode, $httpHeader) = $this->get_calendars_with_http_info ($q, $fields, $sort);
        return $response;
    }


    /**
     *
     * Devolve uma lista de calend\u00E1rios.
     *
     * @param string $q Permite efetuar uma pesquisa global sobre varios campos (optional)
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return array calendar[], HTTP status code, HTTP response headers (array of strings)
     * @throws local_pfc_api_exception on non-2xx response
     */
    public function get_calendars_with_http_info($q = null, $fields = null, $sort = null)
    {
        
        // parse inputs
        $resourcePath = self::$paths['calendars'];
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
            return parent::callApiClient($resourcePath, local_pfc_api_client::$GET, $queryParams, '\local_pfc\models\calendar[]');
        } catch (local_pfc_api_exception $e) {
            throw $e;
        }
    }
    
}