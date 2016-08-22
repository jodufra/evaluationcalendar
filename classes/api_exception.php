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
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_evaluationcalendar;

defined('MOODLE_INTERNAL') || die();

/**
 * Class api_exception
 * @category Class
 * @package  local_evaluationcalendar
 */
class api_exception extends \moodle_exception
{

    /**
     * Original exception Message for debug purposes.
     * @var string
     */
    protected $originalMessage;

    /**
     * The HTTP body of the server response either as Json or string.
     * @var mixed
     */
    protected $responseBody;

    /**
     * The HTTP header of the server response.
     * @var string[]
     */
    protected $responseHeaders;

    /**
     * The deserialized response object
     * @var $responseObject ;
     */
    protected $responseObject;

    /**
     * Constructor
     * @param string $message         Error message
     * @param int    $code            HTTP status code
     * @param string $responseHeaders HTTP response header
     * @param mixed  $responseBody    HTTP body of the server response either as Json or string
     */
    public function __construct($message = "", $code = 0, $responseHeaders = null, $responseBody = null)
    {
        $a = null;
        if (!is_null($responseHeaders) || !is_null($responseBody)) {
            $a = new \stdClass();
            if (!empty($responseHeaders)) {
                $a->headers = $responseHeaders;
            }
            if (!empty($responseBody)) {
                $a->body = $responseBody;
            }
        }
        parent::__construct($code, 'local_evaluationcalendar', new \moodle_url('/local/evaluationcalendar/'), $a, $message);
        $this->originalMessage = $message;
        $this->responseHeaders = $responseHeaders;
        $this->responseBody = $responseBody;
    }


    /**
     * Gets original exception Message for debug purposes.
     * @return string original exception Message for debug purposes.
     */
    public function getOriginalMessage()
    {
        return $this->originalMessage;
    }

    /**
     * Gets the HTTP response header
     * @return string HTTP response header
     */
    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    /**
     * Gets the HTTP body of the server response either as Json or string
     * @return mixed HTTP body of the server response either as Json or string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Gets the deseralized response object (during deserialization)
     * @return mixed the deserialized response object
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }

    /**
     * Sets the deseralized response object (during deserialization)
     * @param mixed $obj Deserialized response object
     * @return void
     */
    public function setResponseObject($obj)
    {
        $this->responseObject = $obj;
    }
}