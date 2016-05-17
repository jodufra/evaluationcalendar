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
 * ****************************************************************************//**
 *
 * [File Documentation]
 *
 * @package [Package Name]
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pfc;
defined('MOODLE_INTERNAL') || die();

/**
 * 
 *
 * @category Class
 * @package  local_pfc
 */
class api_client
{

    public static $GET = "GET";
    public static $POST = "POST";
    public static $PUT = "PUT";
    public static $DELETE = "DELETE";
    // public static $HEAD = "HEAD";
    // public static $OPTIONS = "OPTIONS";
    // public static $PATCH = "PATCH";

    /**
     * Api Configuration
     * @var api_configuration
     */
    protected $config;

    /**
     * Object Serializer
     * @var api_object_serializer
     */
    protected $serializer;

    /**
     * Constructor of the class
     * @param api_configuration $config config for this ApiClient
     */
    public function __construct(api_configuration $config = null)
    {
        if ($config == null) {
            $config = api_configuration::getDefaultConfiguration();
        }

        $this->config = $config;
        $this->serializer = new api_object_serializer();
    }

    /**
     * Get the config
     * @return api_configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the serializer
     * @return api_object_serializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * Get API key (with prefix if set)
     * @param  string $apiKeyIdentifier name of apikey
     * @return string API key with the prefix
     */
    public function getApiKeyWithPrefix($apiKeyIdentifier)
    {
        $prefix = $this->config->getApiKeyPrefix($apiKeyIdentifier);
        $apiKey = $this->config->getApiKey($apiKeyIdentifier);

        if (!isset($apiKey)) {
            return null;
        }

        if (isset($prefix)) {
            $keyWithPrefix = $prefix." ".$apiKey;
        } else {
            $keyWithPrefix = $apiKey;
        }

        return $keyWithPrefix;
    }

    /**
     * Make the HTTP call (Sync)
     * @param string $resourcePath path to method endpoint
     * @param string $method       method to call
     * @param array  $queryParams  parameters to be place in query URL
     * @param array  $postData     parameters to be placed in POST body
     * @param array  $headerParams parameters to be place in request header
     * @param string $responseType expected response type of the endpoint
     * @throws \local_pfc\api_exception on a non 2xx response
     * @return mixed
     */
    public function callApi($resourcePath, $method, $queryParams, $postData, $headerParams, $responseType = null)
    {

        $headers = array();

        // construct the http header
        $headerParams = array_merge(
            (array)$this->config->getDefaultHeaders(),
            (array)$headerParams
        );

        foreach ($headerParams as $key => $val) {
            $headers[] = "$key: $val";
        }

        // form data
        if ($postData and in_array('Content-Type: application/x-www-form-urlencoded', $headers)) {
            $postData = http_build_query($postData);
        } elseif ((is_object($postData) or is_array($postData)) and !in_array('Content-Type: multipart/form-data', $headers)) { // json model
            $postData = json_encode(\local_pfc\api_object_serializer::sanitizeForSerialization($postData));
        }

        $url = $this->config->getHost() . $resourcePath;

        $curl = curl_init();
        // set timeout, if needed
        if ($this->config->getCurlTimeout() != 0) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->config->getCurlTimeout());
        }
        // return the result on success, rather than just true 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        // disable SSL verification, if needed
        if ($this->config->getSSLVerification() == false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if (! empty($queryParams)) {
            $url = ($url . '?' . http_build_query($queryParams));
        }

        if ($method == self::$POST) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method == self::$HEAD) {
            curl_setopt($curl, CURLOPT_NOBODY, true);
        } elseif ($method == self::$OPTIONS) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "OPTIONS");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method == self::$PATCH) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method == self::$PUT) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method == self::$DELETE) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        } elseif ($method != self::$GET) {
            throw new api_exception('Method ' . $method . ' is not recognized.');
        }
        curl_setopt($curl, CURLOPT_URL, $url);

        // Set user agent
        curl_setopt($curl, CURLOPT_USERAGENT, $this->config->getUserAgent());

        // debugging for curl
        if ($this->config->getDebug()) {
            error_log("[DEBUG] HTTP Request body  ~BEGIN~\n".print_r($postData, true)."\n~END~\n", 3, $this->config->getDebugFile());

            curl_setopt($curl, CURLOPT_VERBOSE, 1);
            curl_setopt($curl, CURLOPT_STDERR, fopen($this->config->getDebugFile(), 'a'));
        } else {
            curl_setopt($curl, CURLOPT_VERBOSE, 0);
        }

        // obtain the HTTP response headers
        curl_setopt($curl, CURLOPT_HEADER, 1);

        // Make the request
        $response = curl_exec($curl);
        $http_header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $http_header = $this->http_parse_headers(substr($response, 0, $http_header_size));
        $http_body = substr($response, $http_header_size);
        $response_info = curl_getinfo($curl);

        // debug HTTP response body
        if ($this->config->getDebug()) {
            error_log("[DEBUG] HTTP Response body ~BEGIN~\n".print_r($http_body, true)."\n~END~\n", 3, $this->config->getDebugFile());
        }

        // Handle the response
        if ($response_info['http_code'] == 0) {
            throw new api_exception("API call to $url timed out: ".serialize($response_info), 0, null, null);
        } elseif ($response_info['http_code'] >= 200 && $response_info['http_code'] <= 299 ) {
            // return raw body if response is a file
            if ($responseType == '\SplFileObject' || $responseType == 'string') {
                return array($http_body, $response_info['http_code'], $http_header);
            }

            $data = json_decode($http_body);
            if (json_last_error() > 0) { // if response is a string
                $data = $http_body;
            }
        } else {
            $data = json_decode($http_body);
            if (json_last_error() > 0) { // if response is a string
                $data = $http_body;
            }

            throw new api_exception(
                "[".$response_info['http_code']."] Error connecting to the API ($url)",
                $response_info['http_code'], $http_header, $data
            );
        }
        return array($data, $response_info['http_code'], $http_header);
    }

    /**
     * Return the header 'Accept' based on an array of Accept provided
     *
     * @param string[] $accept Array of header
     *
     * @return string Accept (e.g. application/json)
     */
    public static function selectHeaderAccept($accept)
    {
        if (count($accept) === 0 or (count($accept) === 1 and $accept[0] === '')) {
            return null;
        } elseif (preg_grep("/application\/json/i", $accept)) {
            return 'application/json';
        } else {
            return implode(',', $accept);
        }
    }

    /**
     * Return the content type based on an array of content-type provided
     *
     * @param string[] $content_type Array fo content-type
     *
     * @return string Content-Type (e.g. application/json)
     */
    public static function selectHeaderContentType($content_type)
    {
        if (count($content_type) === 0 or (count($content_type) === 1 and $content_type[0] === '')) {
            return 'application/json';
        } elseif (preg_grep("/application\/json/i", $content_type)) {
            return 'application/json';
        } else {
            return implode(',', $content_type);
        }
    }

    /**
     * Return an array of HTTP response headers
     *
     * @param string $raw_headers A string of raw HTTP response headers
     *
     * @return string[] Array of HTTP response headers
     */
    protected function http_parse_headers($raw_headers)
    {
        $headers = array();
        $key = '';

        foreach(explode("\n", $raw_headers) as $i => $h)
        {
            $h = explode(':', $h, 2);

            if (isset($h[1]))
            {
                if (!isset($headers[$h[0]]))
                    $headers[$h[0]] = trim($h[1]);
                elseif (is_array($headers[$h[0]]))
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                else
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));

                $key = $h[0];
            }
            else
            {
                if (substr($h[0], 0, 1) == "\t")
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                elseif (!$key)
                    $headers[0] = trim($h[0]);trim($h[0]);
            }
        }

        return $headers;
    }
}