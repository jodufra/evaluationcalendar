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


namespace local_pfc;
defined('MOODLE_INTERNAL') || die();


/**
 * Class api_object_serializer
 * @category Class
 * @package  local_pfc
 */
class api_object_serializer
{

    /**
     * Serialize data
     * @param mixed $data the data to serialize
     * @return string serialized form of $data
     */
    public static function sanitizeForSerialization($data)
    {
        if (is_scalar($data) || null === $data) {
            $sanitized = $data;
        } elseif ($data instanceof \DateTime) {
            $sanitized = $data->format(\DateTime::ATOM);
        } elseif (is_array($data)) {
            foreach ($data as $property => $value) {
                $data[$property] = self::sanitizeForSerialization($value);
            }
            $sanitized = $data;
        } elseif (is_object($data)) {
            $values = array();
            foreach (array_keys($data::types()) as $property) {
                $getter = $data::getters()[$property];
                if ($data->$getter() !== null) {
                    $values[$data::attributeMap()[$property]] = self::sanitizeForSerialization($data->$getter());
                }
            }
            $sanitized = (object)$values;
        } else {
            $sanitized = (string)$data;
        }

        return $sanitized;
    }

    /**
     * Sanitize filename by removing path.
     * @param string $filename filename to be sanitized
     * @return string the sanitized filename
     */
    public function sanitizeFilename($filename)
    {
        if (preg_match("/.*[\/\\\\](.*)$/", $filename, $match)) {
            return $match[1];
        } else {
            return $filename;
        }
    }

    /**
     * Take value and turn it into a string suitable for inclusion in
     * the path, by url-encoding.
     * @param string $value a string which will be part of the path
     * @return string the serialized object
     */
    public function toPathValue($value)
    {
        return rawurlencode($this->toString($value));
    }

    /**
     * Take value and turn it into a string suitable for inclusion in
     * the query, by imploding comma-separated if it's an object.
     * If it's a string, pass through unchanged. It will be url-encoded
     * later.
     * @param object $object an object to be serialized to a string
     * @return string the serialized object
     */
    public function toQueryValue($object)
    {
        if (is_array($object)) {
            return implode(',', $object);
        } else {
            return $this->toString($object);
        }
    }

    /**
     * Take value and turn it into a string suitable for inclusion in
     * the header. If it's a string, pass through unchanged
     * If it's a datetime object, format it in ISO8601
     * @param string $value a string which will be part of the header
     * @return string the header string
     */
    public function toHeaderValue($value)
    {
        return $this->toString($value);
    }

    /**
     * Take value and turn it into a string suitable for inclusion in
     * the http body (form parameter). If it's a string, pass through unchanged
     * If it's a datetime object, format it in ISO8601
     * @param string $value the value of the form parameter
     * @return string the form string
     */
    public function toFormValue($value)
    {
        if ($value instanceof \SplFileObject) {
            return $value->getRealPath();
        } else {
            return $this->toString($value);
        }
    }

    /**
     * Take value and turn it into a string suitable for inclusion in
     * the parameter. If it's a string, pass through unchanged
     * If it's a datetime object, format it in ISO8601
     * @param string $value the value of the parameter
     * @return string the header string
     */
    public function toString($value)
    {
        if ($value instanceof \DateTime) { // datetime in ISO8601 format
            return $value->format(\DateTime::ATOM);
        } else {
            return $value;
        }
    }

    /**
     * Serialize an array to a string.
     * @param array  $collection       collection to serialize to a string
     * @param string $collectionFormat the format use for serialization (csv,
     *                                 ssv, tsv, pipes, multi)
     * @return string
     */
    public function serializeCollection(array $collection, $collectionFormat, $allowCollectionFormatMulti = false)
    {
        if ($allowCollectionFormatMulti && ('multi' === $collectionFormat)) {
            // http_build_query() almost does the job for us. We just
            // need to fix the result of multidimensional arrays.
            return preg_replace('/%5B[0-9]+%5D=/', '=', http_build_query($collection, '', '&'));
        }
        switch ($collectionFormat) {
            case 'pipes':
                return implode('|', $collection);

            case 'tsv':
                return implode("\t", $collection);

            case 'ssv':
                return implode(' ', $collection);

            case 'csv':
                // Deliberate fall through. CSV is default format.
            default:
                return implode(',', $collection);
        }
    }

    /**
     * Deserialize a JSON string into an object
     * @param mixed  $data          object or primitive to be deserialized
     * @param string $class         class name is passed as a string
     * @param string $discriminator discriminator if polymorphism is used
     * @return object an instance of $class
     */
    public static function deserialize($data, $class, $discriminator = null)
    {
        if (null === $data) {
            $result = null;
        } elseif (substr($class, 0, 4) === 'map[') { // for associative array e.g. map[string,int]
            $inner = substr($class, 4, -1);
            $result = array();
            if (strrpos($inner, ",") !== false) {
                $subClass_array = explode(',', $inner, 2);
                $subClass = $subClass_array[1];
                foreach ($data as $key => $value) {
                    $result[$key] = self::deserialize($value, $subClass, $discriminator);
                }
            }
        } elseif ($class === 'object') {
            settype($data, 'array');
            $result = $data;
        } elseif ($class === '\DateTime') {
            if (!empty($data)) {
                $result = new \DateTime($data);
            } else {
                $result = null;
            }
        } elseif (in_array($class, array('void', 'bool', 'string', 'double', 'byte', 'mixed', 'integer', 'float', 'int', 'number', 'boolean', 'object'))) {
            settype($data, $class);
            $result = $data;
        } elseif ($class === 'DateTime') {
            $result = new \DateTime($data);
        } elseif (strcasecmp(substr($class, -2), '[]') == 0) {
            $subClass = substr($class, 0, -2);
            $values = array();
            foreach ($data as $key => $value) {
                $values[] = self::deserialize($value, $subClass, $discriminator);
            }
            $result = $values;
        } else {
            if (!empty($discriminator) && isset($data->{$discriminator}) && is_string($data->{$discriminator})) {
                $subclass = '\local_pfc\models\\' . $data->{$discriminator};
                if (is_subclass_of($subclass, $class)) {
                    $class = $subclass;
                }
            }
            $instance = new $class();
            foreach ($instance::types() as $property => $type) {
                $propertySetter = $instance::setters()[$property];
                if (!isset($propertySetter) || !isset($data->{$instance::attributeMap()[$property]})) {
                    continue;
                }
                $propertyValue = $data->{$instance::attributeMap()[$property]};
                if (isset($propertyValue)) {
                    $instance->$propertySetter(self::deserialize($propertyValue, $type, $discriminator));
                }
            }
            $result = $instance;
        }

        return $result;
    }
}