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

/**
 * APIConfiguration contains all configurations for accessing the external API
 *
 * @category Class
 * @package  local_evaluationcalendar
 */
class api_configuration {
    /**
     * Default configuration instance
     */
    private static $_defaultConfiguration = null;
    /**
     * The default headers
     *
     * @var string[]
     */
    protected $defaultHeaders = array();
    /**
     * The host
     *
     * @var string
     */
    protected $host = '';
    /**
     * Timeout (second) of the HTTP request, by default set to 0, no timeout
     *
     * @var string
     */
    protected $curlTimeout = 120;

    /**
     * Constructor
     */
    public function __construct() {

    }

    /**
     * Gets the default configuration instance
     *
     * @return api_configuration
     */
    public static function getDefaultConfiguration() {
        if (self::$_defaultConfiguration == null) {
            self::$_defaultConfiguration = new api_configuration();
            self::$_defaultConfiguration->defaultHeaders = \local_evaluationcalendar_config::Instance()->api_authorization_header;
            self::$_defaultConfiguration->host = \local_evaluationcalendar_config::Instance()->api_host;
        }

        return self::$_defaultConfiguration;
    }

    /**
     * Sets the detault configuration instance
     *
     * @param api_configuration $config An instance of the Configuration Object
     * @return void
     */
    public static function setDefaultConfiguration(api_configuration $config) {
        self::$_defaultConfiguration = $config;
    }

    /**
     * Adds a default header
     *
     * @param string $headerName  header name (e.g. Token)
     * @param string $headerValue header value (e.g. 1z8wp3)
     * @return api_configuration
     */
    public function addDefaultHeader($headerName, $headerValue) {
        if (!is_string($headerName)) {
            throw new \InvalidArgumentException('Header name must be a string.');
        }

        $this->defaultHeaders[$headerName] = $headerValue;
        return $this;
    }

    /**
     * Gets the default header
     *
     * @return array An array of default header(s)
     */
    public function getDefaultHeaders() {
        return $this->defaultHeaders;
    }

    /**
     * Deletes a default header
     *
     * @param string $headerName the header to delete
     * @return api_configuration
     */
    public function deleteDefaultHeader($headerName) {
        unset($this->defaultHeaders[$headerName]);
    }

    /**
     * Gets the host
     *
     * @return string Host
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Sets the host
     *
     * @param string $host Host
     * @return api_configuration
     */
    public function setHost($host) {
        $this->host = $host;
        return $this;
    }

    /**
     * Gets the HTTP timeout value
     *
     * @return string HTTP timeout value
     */
    public function getCurlTimeout() {
        return $this->curlTimeout;
    }

    /**
     * Sets the HTTP timeout value
     *
     * @param integer $seconds Number of seconds before timing out [set to 0 for no timeout]
     * @return api_configuration
     * @throws \coding_exception
     */
    public function setCurlTimeout($seconds) {
        if (!is_numeric($seconds) || $seconds < 0) {
            throw new \coding_exception('Timeout value must be numeric and a non-negative number.');
        }

        $this->curlTimeout = $seconds;
        return $this;
    }
}