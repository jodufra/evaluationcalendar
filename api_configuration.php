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
 * APIConfiguration contains all configurations for accessing the external API
 *
 * @category Class
 * @package  local_pfc
 */
class api_configuration
{

    private static $_defaultConfiguration = null;

    /**
     * Associate array to store API key(s)
     *
     * @var string[]
     */
    protected $apiKeys = array();

    /**
     * Associate array to store API prefix (e.g. Bearer)
     *
     * @var string[]
     */
    protected $apiKeyPrefixes = array();

    /**
     * Access token for OAuth
     *
     * @var string
     */
    protected $accessToken = '';

    /**
     * Username for HTTP basic authentication
     *
     * @var string
     */
    protected $username = '';

    /**
     * Password for HTTP basic authentication
     *
     * @var string
     */
    protected $password = '';

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
    protected $host = 'https://apis.ipleiria.pt/dev/calendarios-avaliacoes/v1';

    /**
     * Timeout (second) of the HTTP request, by default set to 0, no timeout
     *
     * @var string
     */
    protected $curlTimeout = 0;

    /**
     * User agent of the HTTP request
     *
     * @var string
     */
    protected $userAgent = "";

    /**
     * Debug switch (default set to false)
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Debug file location (log to STDOUT by default)
     *
     * @var string
     */
    protected $debugFile = 'php://output';

    /**
     * Debug file location (log to STDOUT by default)
     *
     * @var string
     */
    protected $tempFolderPath;

    /**
     * Indicates if SSL verification should be enabled or disabled.
     *
     * This is useful if the host uses a self-signed SSL certificate.
     *
     * @var boolean True if the certificate should be validated, false otherwise.
     */
    protected $sslVerification = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tempFolderPath = sys_get_temp_dir();
    }

    /**
     * Sets API key
     *
     * @param string $apiKeyIdentifier API key identifier (authentication scheme)
     * @param string $key              API key or token
     *
     * @return api_configuration
     */
    public function setApiKey($apiKeyIdentifier, $key)
    {
        $this->apiKeys[$apiKeyIdentifier] = $key;
        return $this;
    }

    /**
     * Gets API key
     *
     * @param string $apiKeyIdentifier API key identifier (authentication scheme)
     *
     * @return string API key or token
     */
    public function getApiKey($apiKeyIdentifier)
    {
        return isset($this->apiKeys[$apiKeyIdentifier]) ? $this->apiKeys[$apiKeyIdentifier] : null;
    }

    /**
     * Sets the prefix for API key (e.g. Bearer)
     *
     * @param string $apiKeyIdentifier API key identifier (authentication scheme)
     * @param string $prefix           API key prefix, e.g. Bearer
     *
     * @return api_configuration
     */
    public function setApiKeyPrefix($apiKeyIdentifier, $prefix)
    {
        $this->apiKeyPrefixes[$apiKeyIdentifier] = $prefix;
        return $this;
    }

    /**
     * Gets API key prefix
     *
     * @param string $apiKeyIdentifier API key identifier (authentication scheme)
     *
     * @return string
     */
    public function getApiKeyPrefix($apiKeyIdentifier)
    {
        return isset($this->apiKeyPrefixes[$apiKeyIdentifier]) ? $this->apiKeyPrefixes[$apiKeyIdentifier] : null;
    }

    /**
     * Sets the access token for OAuth
     *
     * @param string $accessToken Token for OAuth
     *
     * @return api_configuration
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * Gets the access token for OAuth
     *
     * @return string Access token for OAuth
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Sets the username for HTTP basic authentication
     *
     * @param string $username Username for HTTP basic authentication
     *
     * @return api_configuration
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Gets the username for HTTP basic authentication
     *
     * @return string Username for HTTP basic authentication
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the password for HTTP basic authentication
     *
     * @param string $password Password for HTTP basic authentication
     *
     * @return api_configuration
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Gets the password for HTTP basic authentication
     *
     * @return string Password for HTTP basic authentication
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Adds a default header
     *
     * @param string $headerName  header name (e.g. Token)
     * @param string $headerValue header value (e.g. 1z8wp3)
     *
     * @return api_configuration
     */
    public function addDefaultHeader($headerName, $headerValue)
    {
        if (!is_string($headerName)) {
            throw new \InvalidArgumentException('Header name must be a string.');
        }

        $this->defaultHeaders[$headerName] =  $headerValue;
        return $this;
    }

    /**
     * Gets the default header
     *
     * @return array An array of default header(s)
     */
    public function getDefaultHeaders()
    {
        return $this->defaultHeaders;
    }

    /**
     * Deletes a default header
     *
     * @param string $headerName the header to delete
     *
     * @return api_configuration
     */
    public function deleteDefaultHeader($headerName)
    {
        unset($this->defaultHeaders[$headerName]);
    }

    /**
     * Sets the host
     *
     * @param string $host Host
     *
     * @return api_configuration
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    /**
     * Gets the host
     *
     * @return string Host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the user agent of the api client
     *
     * @param string $userAgent the user agent of the api client
     *
     * @return api_configuration
     */
    public function setUserAgent($userAgent)
    {
        if (!is_string($userAgent)) {
            throw new \InvalidArgumentException('User-agent must be a string.');
        }

        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Gets the user agent of the api client
     *
     * @return string user agent
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Sets the HTTP timeout value
     *
     * @param integer $seconds Number of seconds before timing out [set to 0 for no timeout]
     *
     * @return api_configuration
     */
    public function setCurlTimeout($seconds)
    {
        if (!is_numeric($seconds) || $seconds < 0) {
            throw new \InvalidArgumentException('Timeout value must be numeric and a non-negative number.');
        }

        $this->curlTimeout = $seconds;
        return $this;
    }

    /**
     * Gets the HTTP timeout value
     *
     * @return string HTTP timeout value
     */
    public function getCurlTimeout()
    {
        return $this->curlTimeout;
    }

    /**
     * Sets debug flag
     *
     * @param bool $debug Debug flag
     *
     * @return api_configuration
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Gets the debug flag
     *
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Sets the debug file
     *
     * @param string $debugFile Debug file
     *
     * @return api_configuration
     */
    public function setDebugFile($debugFile)
    {
        $this->debugFile = $debugFile;
        return $this;
    }

    /**
     * Gets the debug file
     *
     * @return string
     */
    public function getDebugFile()
    {
        return $this->debugFile;
    }

    /**
     * Sets the temp folder path
     *
     * @param string $tempFolderPath Temp folder path
     *
     * @return api_configuration
     */
    public function setTempFolderPath($tempFolderPath)
    {
        $this->tempFolderPath = $tempFolderPath;
        return $this;
    }

    /**
     * Gets the temp folder path
     *
     * @return string Temp folder path
     */
    public function getTempFolderPath()
    {
        return $this->tempFolderPath;
    }

    /**
     * Sets if SSL verification should be enabled or disabled
     *
     * @param boolean $sslVerification True if the certificate should be validated, false otherwise
     *
     * @return api_configuration
     */
    public function setSSLVerification($sslVerification)
    {
        $this->sslVerification = $sslVerification;
        return $this;
    }

    /**
     * Gets if SSL verification should be enabled or disabled
     *
     * @return boolean True if the certificate should be validated, false otherwise
     */
    public function getSSLVerification()
    {
        return $this->sslVerification;
    }

    /**
     * Gets the default configuration instance
     *
     * @return api_configuration
     */
    public static function getDefaultConfiguration()
    {
        if (self::$_defaultConfiguration == null) {
            self::$_defaultConfiguration = new api_configuration();
        }

        return self::$_defaultConfiguration;
    }

    /**
     * Sets the detault configuration instance
     *
     * @param api_configuration $config An instance of the Configuration Object
     *
     * @return void
     */
    public static function setDefaultConfiguration(api_configuration $config)
    {
        self::$_defaultConfiguration = $config;
    }
}