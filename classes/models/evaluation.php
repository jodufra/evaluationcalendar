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
 * @package local_pfc\models
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pfc\models;
use \local_pfc\api_exception;


/**
 * Class evaluation
 *
 * @category Class
 * @package local_pfc\models
 */
class evaluation extends base_model
{
    /**
     * Array of property to type mappings. Used for (de)serialization
     * @var string[]
     */
    static $types = array(
        'id' => 'string',
        'data_inicio' => 'string',
        'data_fim' => 'string',
        'descricao' => 'string',
        'local' => 'string',
        'tipo_sala' => 'string',
        'id_tipo_avaliacao' => 'string',
        'id_unidade_curricular' => 'int',
        'codigo_siges' => 'int',
        'id_calendario' => 'string'
    );

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    static $attributeMap = array(
        'id' => 'id',
        'data_inicio' => 'dataInicio',
        'data_fim' => 'dataFim',
        'descricao' => 'descricao',
        'local' => 'local',
        'tipo_sala' => 'tipoSala',
        'id_tipo_avaliacao' => 'idTipoAvaliacao',
        'id_unidade_curricular' => 'idUnidadeCurricular',
        'codigo_siges' => 'codigoSiges',
        'id_calendario' => 'idCalendario'
    );

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    static $setters = array(
        'id' => 'setId',
        'data_inicio' => 'setDataInicio',
        'data_fim' => 'setDataFim',
        'descricao' => 'setDescricao',
        'local' => 'setLocal',
        'tipo_sala' => 'setTipoSala',
        'id_tipo_avaliacao' => 'setIdTipoAvaliacao',
        'id_unidade_curricular' => 'setIdUnidadeCurricular',
        'codigo_siges' => 'setCodigoSiges',
        'id_calendario' => 'setIdCalendario'
    );

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    static $getters = array(
        'id' => 'getId',
        'data_inicio' => 'getDataInicio',
        'data_fim' => 'getDataFim',
        'descricao' => 'getDescricao',
        'local' => 'getLocal',
        'tipo_sala' => 'getTipoSala',
        'id_tipo_avaliacao' => 'getIdTipoAvaliacao',
        'id_unidade_curricular' => 'getIdUnidadeCurricular',
        'codigo_siges' => 'getCodigoSiges',
        'id_calendario' => 'getIdCalendario'
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
     * $id Identificador da avaliação.
     * @var string
     */
    protected $id;

    /**
     * $data_inicio Data de Inicio da Avaliação.
     * @var string
     */
    protected $data_inicio;

    /**
     * $data_fim Data de Fim da Avaliação.
     * @var string
     */
    protected $data_fim;

    /**
     * $descricao Descrição da Avaliação.
     * @var string
     */
    protected $descricao;

    /**
     * $local Local da Avaliação (SALADEAULA|NAOSEAPLICA|OUTROLOCAL).
     * @var string
     */
    protected $local;

    /**
     * $tipo_sala Descrição da sala que o docente precisa para a avaliação.
     * @var string
     */
    protected $tipo_sala;

    /**
     * $id_tipo_avaliacao Identificador do Tipo de Avaliação.
     * @var string
     */
    protected $id_tipo_avaliacao;

    /**
     * $id_unidade_curricular Identificador da Unidade Curricular.
     * @var int
     */
    protected $id_unidade_curricular;

    /**
     * $codigo_siges Código Siges da Unidade Curricular (ex: 9119102).
     * @var int
     */
    protected $codigo_siges;

    /**
     * $id_calendario Identificador do Calendário.
     * @var string
     */
    protected $id_calendario;


    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {

        if ($data != null) {
            $this->id = $data["id"];
            $this->data_inicio = $data["data_inicio"];
            $this->data_fim = $data["data_fim"];
            $this->descricao = $data["descricao"];
            $this->local = $data["local"];
            $this->tipo_sala = $data["tipo_sala"];
            $this->id_tipo_avaliacao = $data["id_tipo_avaliacao"];
            $this->id_unidade_curricular = $data["id_unidade_curricular"];
            $this->codigo_siges = $data["codigo_siges"];
            $this->id_calendario = $data["id_calendario"];
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
     * @param string $id Identificador da avaliação.
     * @return $this
     */
    public function setId($id)
    {

        $this->id = $id;
        return $this;
    }

    /**
     * Gets data_inicio
     * @return string
     */
    public function getDataInicio()
    {
        return $this->data_inicio;
    }

    /**
     * Sets data_inicio
     * @param string $data_inicio Data de Inicio da Avaliação.
     * @return $this
     */
    public function setDataInicio($data_inicio)
    {

        $this->data_inicio = $data_inicio;
        return $this;
    }

    /**
     * Gets data_fim
     * @return string
     */
    public function getDataFim()
    {
        return $this->data_fim;
    }

    /**
     * Sets data_fim
     * @param string $data_fim Data de Fim da Avaliação.
     * @return $this
     */
    public function setDataFim($data_fim)
    {

        $this->data_fim = $data_fim;
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
     * @param string $descricao Descrição da Avaliação.
     * @return $this
     */
    public function setDescricao($descricao)
    {

        $this->descricao = $descricao;
        return $this;
    }

    /**
     * Gets local
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Sets local 
     * @param string $local Local da Avaliação (SALADEAULA|NAOSEAPLICA|OUTROLOCAL).
     * @return $this
     * @throws api_exception When $local different then allowed values
     */
    public function setLocal($local)
    {
        $allowed_values = array("SALADEAULA", "NAOSEAPLICA", "OUTROLOCAL");
        if (!in_array($local, $allowed_values)) {
            throw new api_exception("Invalid value for 'local', must be one of 'SALADEAULA', 'NAOSEAPLICA', 'OUTROLOCAL'");
        }
        $this->local = $local;
        return $this;
    }

    /**
     * Gets tipo_sala
     * @return string
     */
    public function getTipoSala()
    {
        return $this->tipo_sala;
    }

    /**
     * Sets tipo_sala
     * @param string $tipo_sala Descrição da sala que o docente precisa para a avaliação.
     * @return $this
     */
    public function setTipoSala($tipo_sala)
    {

        $this->tipo_sala = $tipo_sala;
        return $this;
    }

    /**
     * Gets id_tipo_avaliacao
     * @return string
     */
    public function getIdTipoAvaliacao()
    {
        return $this->id_tipo_avaliacao;
    }

    /**
     * Sets id_tipo_avaliacao
     * @param string $id_tipo_avaliacao Identificador do Tipo de Avaliação.
     * @return $this
     */
    public function setIdTipoAvaliacao($id_tipo_avaliacao)
    {

        $this->id_tipo_avaliacao = $id_tipo_avaliacao;
        return $this;
    }

    /**
     * Gets id_unidade_curricular
     * @return int
     */
    public function getIdUnidadeCurricular()
    {
        return $this->id_unidade_curricular;
    }

    /**
     * Sets id_unidade_curricular
     * @param int $id_unidade_curricular Identificador da Unidade Curricular.
     * @return $this
     */
    public function setIdUnidadeCurricular($id_unidade_curricular)
    {

        $this->id_unidade_curricular = $id_unidade_curricular;
        return $this;
    }

    /**
     * Gets codigo_siges
     * @return int
     */
    public function getCodigoSiges()
    {
        return $this->codigo_siges;
    }

    /**
     * Sets codigo_siges
     * @param int $codigo_siges Código Siges da Unidade Curricular (ex: 9119102).
     * @return $this
     */
    public function setCodigoSiges($codigo_siges)
    {

        $this->codigo_siges = $codigo_siges;
        return $this;
    }

    /**
     * Gets id_calendario
     * @return string
     */
    public function getIdCalendario()
    {
        return $this->id_calendario;
    }

    /**
     * Sets id_calendario
     * @param string $id_calendario Identificador do Calendário.
     * @return $this
     */
    public function setIdCalendario($id_calendario)
    {

        $this->id_calendario = $id_calendario;
        return $this;
    }
}