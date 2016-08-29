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

/**
 * Class base_api
 *
 * @category Class
 * @package  local_evaluationcalendar\api
 */
class base_api {

    /**
     * Class path of the returning model of the api
     *
     * @var string
     */
    private static $_error_model = '\local_evaluationcalendar\models\error';

    /**
     * API Client
     *
     * @var api_client instance of the api_client
     */
    protected $api_client;

    /**
     * Constructor
     *
     * @param api_client|null $api_client The api client to use
     */
    function __construct($api_client = null) {
        if ($api_client == null) {
            $api_client = new api_client();
        }

        $this->api_client = $api_client;
    }

    /**
     * Get API client
     *
     * @return api_client get the API client
     */
    public function get_api_client() {
        return $this->api_client;
    }

    /**
     * Set the API client
     *
     * @param api_client $api_client set the API client
     */
    public function set_api_client(api_client $api_client) {
        $this->api_client = $api_client;
    }

    /**
     * Sets the request headers, calls the api client method to make the request and parses the response
     *
     * @param string $resource_path       To where the request will be sent
     * @param array  $query_params        Query parameters
     * @param string $response_type       The type of response we are going to get
     * @param string $method              Request method (ex: GET, POST, DELETE, PUT)
     * @param string $content_type_header Header to specify the type of content we are sending
     * @param null   $accept_header       Header to specify the type of content we want to recieve
     * @return array contains the parsed response, the status code and the responses http headers
     * @throws api_exception
     */
    public function call_api_client($resource_path, $query_params = array(), $response_type = 'string',
            $method = 'GET', $content_type_header = 'application/json', $accept_header = null) {

        $header_params = array();
        if (!is_null($accept_header)) {
            $header_params['Accept'] = $accept_header;
        }
        $header_params['Content-Type'] = $content_type_header;

        try {
            list($response, $statusCode, $httpHeader) = $this->api_client->callApi(
                    $resource_path, $method, $query_params,
                    $header_params, $response_type
            );

            if (!$response) {
                return array(null, $statusCode, $httpHeader);
            }

            if (is_string($response)) {
                // is a json object ?
                if (strcasecmp(substr($response, 0, 1), '{') == 0 && strcasecmp(substr($response, -1), '}') == 0) {
                    $response = json_decode($response);
                }
            }

            if (is_string($response)) {
                $response_obj = $response;
            } else {
                $response_obj = api_object_serializer::deserialize($response->data, $response_type, null);
            }

            return array($response_obj, $statusCode, $httpHeader);

        } catch (\Exception $e) {
            if ($e instanceof api_exception) {
                $response = $e->getResponseBody();
                if (is_string($response) &&
                        strcasecmp(substr($response, 0, 1), '{') == 0 &&
                        strcasecmp(substr($response, -1), '}') == 0
                ) {
                    $response = json_decode($response);
                }

                if (is_string($response)) {
                    $data = $response;
                } else {
                    $data = api_object_serializer::deserialize($response, self::$_error_model, null);
                }
                $e->setResponseObject($data);
            } else {
                $e = new api_exception($e->getMessage(), 500);
            }
            throw $e;
        }
    }

    /**
     * @param string $input              What contains the csv content
     * @param string $row_delimiter      CSV rows are separated by this character
     * @param string $delimiter          CSV items are separated by this character
     * @param bool   $has_header         For the function to know if it already has an header
     * @param array  $header_replacement In case you wish to replace the csv header
     * @return object
     * @throws \coding_exception
     */
    public function parse_csv_to_assoc_array($input, $row_delimiter = "\n", $delimiter = ";", $has_header = true,
            $header_replacement = null) {

        // it is supposed to have a header to create an association
        if (!$has_header && empty($header_replacement)) {
            throw new \coding_exception('Csv must have a header or a replacement for it');
        }

        // if you wish to replace or add an header
        if (!empty($header_replacement)) {
            $header = implode($delimiter, $header_replacement);
            if ($has_header) {
                $input = $header . strstr($input, $row_delimiter);
            } else {
                $input = $header . $row_delimiter . $input;
            }
        }

        // turn csv string to array
        $result = $this->parse_csv($input, $row_delimiter, $delimiter);

        // create assoc
        array_walk($result, function(&$a) use ($result) {
            $a = array_combine($result[0], $a);
        });

        // remove column header before returning
        array_shift($result);

        return (object) $result;
    }

    /**
     * @param string $input         What contains the csv content
     * @param string $row_delimiter CSV rows are separated by this character
     * @param string $delimiter     CSV items are separated by this character
     * @return array
     * @throws \coding_exception
     */
    public function parse_csv($input, $row_delimiter = "\n", $delimiter = ";") {
        //parse the rows
        $result = explode($row_delimiter, $input);

        // remove empty lines
        $result = array_filter($result, function($line) {
            return !is_null($line) && !empty($line);
        });

        //parse the items in rows
        foreach ($result as &$row) {
            $row = str_getcsv($row, $delimiter);
        }
        return $result;
    }

}