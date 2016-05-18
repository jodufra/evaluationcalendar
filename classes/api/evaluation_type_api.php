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
use local_pfc\models\evaluation_type;


/**
 * Class evaluation_type_api
 *
 * @category Class
 * @package local_pfc\api
 */
class evaluation_type_api extends base_api
{

    /**
     * Possible url API paths
     * @var array
     */
    private static $paths = array(
        'evaluation_types' => '/tipos-avaliacao',
        'evaluation_type' => '/tipos-avaliacao/{idTipoAvaliacao}'
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
     * Devolve uma lista de tipos de avalia\u00E7\u00E3o que uma avalia\u00E7\u00E3o pode ter (como por exemp. Trabalho Laboratorial, Prova Oral, Prova Escrita, etc).
     *
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return evaluation_type[]
     * @throws local_pfc_api_exception on non-2xx response
     */
    public function get_evaluation_types($fields = null, $sort = null)
    {
        list($response, $statusCode, $httpHeader) = $this->get_evaluation_types_with_http_info ($fields, $sort);
        return $response;
    }

    /**
     *
     * Devolve uma lista de tipos de avalia\u00E7\u00E3o que uma avalia\u00E7\u00E3o pode ter (como por exemp. Trabalho Laboratorial, Prova Oral, Prova Escrita, etc).
     *
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return array of evaluation_type[], HTTP status code, HTTP response headers (array of strings)
     * @throws local_pfc_api_exception on non-2xx response
     */
    public function get_evaluation_types_with_http_info($fields = null, $sort = null)
    {

        // parse inputs
        $resourcePath = self::$paths['evaluation_types'];
        $queryParams = array();

        // query params
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
            return parent::callApiClient($resourcePath, local_pfc_api_client::$GET, $queryParams, '\local_pfc\models\evaluation_type[]');
        } catch (local_pfc_api_exception $e) {
            throw $e;
        }
    }


    /**
     *
     * Devolve um tipo de avalia\u00E7\u00E3o que uma avalia\u00E7\u00E3o pode ter (como por exemp. Trabalho Laboratorial, Prova Oral, Prova Escrita, etc).
     *
     * @param string $id_evaluation_type Identificador do tipo de avalia\u00E7\u00E3o (required)
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return evaluation_type[]
     * @throws local_pfc_api_exception on non-2xx response
     */
    public function get_evaluation_type($id_evaluation_type, $fields = null, $sort = null)
    {
        list($response, $statusCode, $httpHeader) = $this->get_evaluation_type_with_http_info ($id_evaluation_type, $fields, $sort);
        return $response;
    }


    /**
     *
     * Devolve um tipo de avalia\u00E7\u00E3o que uma avalia\u00E7\u00E3o pode ter (como por exemp. Trabalho Laboratorial, Prova Oral, Prova Escrita, etc).
     *
     * @param string $id_tipo_avaliacao Identificador do tipo de avalia\u00E7\u00E3o (required)
     * @param string $fields Permite selecionar um sub conjunto de atributos (optional)
     * @param string $sort Permite ordenar os resultados por atributo (optional)
     * @return array of evaluation_type[], HTTP status code, HTTP response headers (array of strings)
     * @throws local_pfc_api_exception on non-2xx response
     */
    public function get_evaluation_type_with_http_info($id_evaluation_type, $fields = null, $sort = null)
    {

        // verify the required parameter 'id_tipo_avaliacao' is set
        if ($id_evaluation_type === null) {
            throw new local_pfc_api_exception('Missing the required parameter $id_evaluation_type when calling get_evaluation_type');
        }

        // parse inputs
        $resourcePath = self::$paths['evaluation_type'];
        $queryParams = array();

        // query params
        if ($fields !== null) {
            $queryParams['fields'] = $this->apiClient->getSerializer()->toQueryValue($fields);
        }
        if ($sort !== null) {
            $queryParams['sort'] = $this->apiClient->getSerializer()->toQueryValue($sort);
        }

        // path params
        if ($id_evaluation_type !== null) {
            $resourcePath = str_replace('{idTipoAvaliacao}', $this->apiClient->getSerializer()->toPathValue($id_evaluation_type), $resourcePath);
        }
        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // make the API Call
        try {
            return parent::callApiClient($resourcePath, local_pfc_api_client::$GET, $queryParams, '\local_pfc\models\evaluation_type[]');
        } catch (local_pfc_api_exception $e) {
            throw $e;
        }
    }
}