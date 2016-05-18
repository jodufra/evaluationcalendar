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
 * @package local_pfc\models
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pfc\models;


/**
 * Class evaluation_type
 *
 * @category Class
 * @package local_pfc\models
 */
class evaluation_type extends base_model
{

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @var string[]
     */
    static $types = array(
        'id' => 'string',
        'descricao' => 'string',
        'abreviatura' => 'string'
    );

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    static $attributeMap = array(
        'id' => 'id',
        'descricao' => 'descricao',
        'abreviatura' => 'abreviatura'
    );

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    static $setters = array(
        'id' => 'setId',
        'descricao' => 'setDescricao',
        'abreviatura' => 'setAbreviatura'
    );

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    static $getters = array(
        'id' => 'getId',
        'descricao' => 'getDescricao',
        'abreviatura' => 'getAbreviatura'
    );

    /**
     * Get array of property to type mappings. Used for (de)serialization
     * @return $types
     */
    static function types() {
        return self::$types;
    }


    /**
     * Get array of attributes where the key is the local name, and the value is the original name
     * @return string[]
     */
    static function attributeMap() {
        return self::$attributeMap;
    }

    /**
     * Get array of attributes to setter functions (for deserialization of responses)
     * @return string[]
     */
    static function setters() {
        return self::$setters;
    }

    /**
     * Get array of attributes to getter functions (for serialization of requests)
     * @return string[]
     */
    static function getters() {
        return self::$getters;
    }

    /**
     * $id Identificador do tipo de avalia\u00E7\u00E3o.
     * @var string
     */
    protected $id;

    /**
     * $descricao Descri\u00E7\u00E3o do tipo de avalia\u00E7\u00E3o.
     * @var string
     */
    protected $descricao;

    /**
     * $abreviatura Abreviatura do Tipo de Avalia\u00E7\u00E3o.
     * @var string
     */
    protected $abreviatura;


    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {

        if ($data != null) {
            $this->id = $data["id"];
            $this->descricao = $data["descricao"];
            $this->abreviatura = $data["abreviatura"];
        }
    }

    /**
     * Gets id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id
     * @param string $id Identificador do tipo de avalia\u00E7\u00E3o.
     * @return $this
     */
    public function setId($id)
    {

        $this->id = $id;
        return $this;
    }

    /**
     * Gets descricao
     * @return string
     */
    public function getDescricao()
    {
        return $this->descricao;
    }

    /**
     * Sets descricao
     * @param string $descricao Descri\u00E7\u00E3o do tipo de avalia\u00E7\u00E3o.
     * @return $this
     */
    public function setDescricao($descricao)
    {

        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Gets abreviatura
     * @return string
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }

    /**
     * Sets abreviatura
     * @param string $abreviatura Abreviatura do Tipo de Avalia\u00E7\u00E3o.
     * @return $this
     */
    public function setAbreviatura($abreviatura)
    {

        $this->abreviatura = $abreviatura;
        return $this;
    }
}