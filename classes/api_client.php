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
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_evaluationcalendar;

defined('MOODLE_INTERNAL') || die();
/*
require_once($CFG->dirroot.'/local/evaluationcalendar/classes/api_configuration.php');
require_once($CFG->dirroot.'/local/evaluationcalendar/classes/api_exception.php');
require_once($CFG->dirroot.'/local/evaluationcalendar/classes/api_object_serializer.php');
require_once($CFG->dirroot.'/local/evaluationcalendar/classes/models/evaluation.php');*/

/**
 * @category Class
 * @package  local_evaluationcalendar
 */
class api_client {

    public static $GET = "GET";

    /**
     * Api Configuration
     *
     * @var api_configuration
     */
    protected $config;

    /**
     * Object Serializer
     *
     * @var api_object_serializer
     */
    protected $serializer;

    /**
     * Constructor of the class
     *
     * @param api_configuration $config config for this api_client
     */
    public function __construct(api_configuration $config = null) {
        if ($config == null) {
            $config = api_configuration::getDefaultConfiguration();
        }

        $this->config = $config;
        $this->serializer = new api_object_serializer();
    }

    /**
     * Get the config
     *
     * @return api_configuration
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Get the serializer
     *
     * @return api_object_serializer
     */
    public function getSerializer() {
        return $this->serializer;
    }

    /**
     * Get API key (with prefix if set)
     *
     * @param  string $apiKeyIdentifier name of apikey
     * @return string API key with the prefix
     */
    public function getApiKeyWithPrefix($apiKeyIdentifier) {
        $prefix = $this->config->getApiKeyPrefix($apiKeyIdentifier);
        $apiKey = $this->config->getApiKey($apiKeyIdentifier);

        if (!isset($apiKey)) {
            return null;
        }

        if (isset($prefix)) {
            $keyWithPrefix = $prefix . " " . $apiKey;
        } else {
            $keyWithPrefix = $apiKey;
        }

        return $keyWithPrefix;
    }

    /**
     * Make the HTTP call (Sync)
     *
     * @param string $resourcePath path to method endpoint
     * @param string $method       method to call
     * @param array  $queryParams  parameters to be place in query URL
     * @param array  $headerParams parameters to be place in request header
     * @param string $responseType expected response type of the endpoint
     * @throws api_exception on a non 2xx response
     * @return mixed
     */
    public function callApi($resourcePath, $method, $queryParams, $headerParams, $responseType = null) {

        $curl = curl_init();

        // set timeout, if needed
        if ($this->config->getCurlTimeout() != 0) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->config->getCurlTimeout());
        }

        // return the result on success, rather than just true
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        // construct the http header
        $headers = array();
        $headerParams = array_merge(
                (array) $this->config->getDefaultHeaders(),
                (array) $headerParams
        );
        foreach ($headerParams as $key => $val) {
            $headers[] = $key . ': ' . $val;
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // disable ssl verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // check if method is valid
        if ($method != self::$GET) {
            throw new api_exception("Method $method is not recognized.");
        }

        // set url and query string
        $url = $this->config->getHost() . $resourcePath;
        if (!empty($queryParams)) {
            // using PHP_QUERY_RFC3986 to get '%20' instead of '+' for encoded space characters
            $url = ($url . '?' . http_build_query($queryParams, null, '&', PHP_QUERY_RFC3986));
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        // obtain the HTTP response headers
        curl_setopt($curl, CURLOPT_HEADER, 1);

        // Make the request
        $response = curl_exec($curl);
        $http_header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $http_header = $this->http_parse_headers(substr($response, 0, $http_header_size));
        $http_body = substr($response, $http_header_size);
        $response_info = curl_getinfo($curl);
        $error_num = curl_errno($curl);
        $error_msg = curl_error($curl);
        curl_close($curl);

        if ($error_num) {
            throw new api_exception("[$error_num] - cURL error: $error_msg", 0, null, null);
        }

        // Handle the response
        $http_code = $response_info['http_code'];
        if ($http_code == 0) {
            throw new api_exception("API call to $url timed out", 0, null, null);
        } elseif ($http_code >= 200 && $http_code <= 299) {
            if ($responseType == 'string') {
                foreach ($headerParams as $param) {
                    if (strpos($param, 'ISO-8859-1') !== false) {
                        break;
                    }
                }
                return array($http_body, $http_code, $http_header);
            }
            $data = json_last_error() > 0 ? $http_body : json_decode($http_body);
            return array($data, $http_code, $http_header);
        } else {
            $data = json_last_error() > 0 ? $http_body : json_decode($http_body);
            throw new api_exception(
                    "[$http_code] Error connecting to the API ($url)", $http_code, $http_header, $data
            );
        }
    }

    /**
     * Return an array of HTTP response headers
     *
     * @param string $raw_headers A string of raw HTTP response headers
     * @return string[] Array of HTTP response headers
     */
    protected function http_parse_headers($raw_headers) {
        $headers = array();
        $key = '';

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }

        return $headers;
    }
}