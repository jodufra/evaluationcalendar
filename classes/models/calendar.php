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
 * Class calendar
 *
 * @category Class
 * @package local_pfc\models
 */
class calendar extends base_model
{
    /**
     * Array of property to type mappings. Used for (de)serialization
     * @var string[]
     */
    static $types = array(
        'id' => 'string',
        'id_curso' => 'int',
        'nome_curso' => 'string',
        'abreviatura_regime_frequencia' => 'string',
        'abrv_curso' => 'string',
        'id_ano_letivo' => 'int',
        'ano_letivo' => 'string',
        'id_semestre' => 'int',
        'semestre' => 'string',
        'id_ep_aval' => 'int',
        'epoca_avaliacao' => 'string',
        'estado' => 'string'
    );

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @var string[]
     */
    static $attributeMap = array(
        'id' => 'id',
        'id_curso' => 'idCurso',
        'nome_curso' => 'nomeCurso',
        'abreviatura_regime_frequencia' => 'abreviaturaRegimeFrequencia',
        'abrv_curso' => 'abrvCurso',
        'id_ano_letivo' => 'idAnoLetivo',
        'ano_letivo' => 'anoLetivo',
        'id_semestre' => 'idSemestre',
        'semestre' => 'semestre',
        'id_ep_aval' => 'idEpAval',
        'epoca_avaliacao' => 'epocaAvaliacao',
        'estado' => 'estado'
    );

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @var string[]
     */
    static $setters = array(
        'id' => 'setId',
        'id_curso' => 'setIdCurso',
        'nome_curso' => 'setNomeCurso',
        'abreviatura_regime_frequencia' => 'setAbreviaturaRegimeFrequencia',
        'abrv_curso' => 'setAbrvCurso',
        'id_ano_letivo' => 'setIdAnoLetivo',
        'ano_letivo' => 'setAnoLetivo',
        'id_semestre' => 'setIdSemestre',
        'semestre' => 'setSemestre',
        'id_ep_aval' => 'setIdEpAval',
        'epoca_avaliacao' => 'setEpocaAvaliacao',
        'estado' => 'setEstado'
    );

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @var string[]
     */
    static $getters = array(
        'id' => 'getId',
        'id_curso' => 'getIdCurso',
        'nome_curso' => 'getNomeCurso',
        'abreviatura_regime_frequencia' => 'getAbreviaturaRegimeFrequencia',
        'abrv_curso' => 'getAbrvCurso',
        'id_ano_letivo' => 'getIdAnoLetivo',
        'ano_letivo' => 'getAnoLetivo',
        'id_semestre' => 'getIdSemestre',
        'semestre' => 'getSemestre',
        'id_ep_aval' => 'getIdEpAval',
        'epoca_avaliacao' => 'getEpocaAvaliacao',
        'estado' => 'getEstado'
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
     * @param $array calendar[]
     * @param $param string
     * @param $comparison_value string
     * @return calendar|null
     */
    public static function select_instance_from_array($array, $param, $comparison_value){
        return parent::select_instance_from_array($array, $param, $comparison_value);
    }

    /**
     * $id Identificador do calendário.
     * @var string
     */
    protected $id;

    /**
     * $id_curso Identificador do curso.
     * @var int
     */
    protected $id_curso;

    /**
     * $nome_curso Nome do Curso.
     * @var string
     */
    protected $nome_curso;

    /**
     * $abreviatura_regime_frequencia Abreviatura do regime do curso (D|PL).
     * @var string
     */
    protected $abreviatura_regime_frequencia;

    /**
     * $abrv_curso Abreviatura do Curso.
     * @var string
     */
    protected $abrv_curso;

    /**
     * $id_ano_letivo Identificador do ano letivo.
     * @var int
     */
    protected $id_ano_letivo;

    /**
     * $ano_letivo Ano Letivo.
     * @var string
     */
    protected $ano_letivo;

    /**
     * $id_semestre Identificador do semestre.
     * @var int
     */
    protected $id_semestre;

    /**
     * $semestre Semestre.
     * @var string
     */
    protected $semestre;

    /**
     * $id_ep_aval Identificador da \u00C9poca de Avalia\u00E7\u00E3o.
     * @var int
     */
    protected $id_ep_aval;

    /**
     * $epoca_avaliacao \u00C9poca de Avalia\u00E7\u00E3o (ex. Avalia\u00E7a\u00F5 Cont\u00EDnua/Peri\u00F3dica, \u00C9poca Normal, etc)
     * @var string
     */
    protected $epoca_avaliacao;

    /**
     * $estado Estado do Calendário.
     * @var string
     */
    protected $estado;


    /**
     * Constructor
     * @param mixed[] $data Associated array of property value initalizing the model
     */
    public function __construct(array $data = null)
    {

        if ($data != null) {
            $this->id = $data["id"];
            $this->id_curso = $data["id_curso"];
            $this->nome_curso = $data["nome_curso"];
            $this->abreviatura_regime_frequencia = $data["abreviatura_regime_frequencia"];
            $this->abrv_curso = $data["abrv_curso"];
            $this->id_ano_letivo = $data["id_ano_letivo"];
            $this->ano_letivo = $data["ano_letivo"];
            $this->id_semestre = $data["id_semestre"];
            $this->semestre = $data["semestre"];
            $this->id_ep_aval = $data["id_ep_aval"];
            $this->epoca_avaliacao = $data["epoca_avaliacao"];
            $this->estado = $data["estado"];
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
     * @param string $id Identificador do calendário.
     * @return $this
     */
    public function setId($id)
    {

        $this->id = $id;
        return $this;
    }

    /**
     * Gets id_curso
     * @return int
     */
    public function getIdCurso()
    {
        return $this->id_curso;
    }

    /**
     * Sets id_curso
     * @param int $id_curso Identificador do curso.
     * @return $this
     */
    public function setIdCurso($id_curso)
    {

        $this->id_curso = $id_curso;
        return $this;
    }

    /**
     * Gets nome_curso
     * @return string
     */
    public function getNomeCurso()
    {
        return $this->nome_curso;
    }

    /**
     * Sets nome_curso
     * @param string $nome_curso Nome do Curso.
     * @return $this
     */
    public function setNomeCurso($nome_curso)
    {

        $this->nome_curso = $nome_curso;
        return $this;
    }

    /**
     * Gets abreviatura_regime_frequencia
     * @return string
     */
    public function getAbreviaturaRegimeFrequencia()
    {
        return $this->abreviatura_regime_frequencia;
    }

    /**
     * Sets abreviatura_regime_frequencia
     * @param string $abreviatura_regime_frequencia Abreviatura do regime do curso (D|PL).
     * @return $this
     */
    public function setAbreviaturaRegimeFrequencia($abreviatura_regime_frequencia)
    {

        $this->abreviatura_regime_frequencia = $abreviatura_regime_frequencia;
        return $this;
    }

    /**
     * Gets abrv_curso
     * @return string
     */
    public function getAbrvCurso()
    {
        return $this->abrv_curso;
    }

    /**
     * Sets abrv_curso
     * @param string $abrv_curso Abreviatura do Curso.
     * @return $this
     */
    public function setAbrvCurso($abrv_curso)
    {

        $this->abrv_curso = $abrv_curso;
        return $this;
    }

    /**
     * Gets id_ano_letivo
     * @return int
     */
    public function getIdAnoLetivo()
    {
        return $this->id_ano_letivo;
    }

    /**
     * Sets id_ano_letivo
     * @param int $id_ano_letivo Identificador do ano letivo.
     * @return $this
     */
    public function setIdAnoLetivo($id_ano_letivo)
    {

        $this->id_ano_letivo = $id_ano_letivo;
        return $this;
    }

    /**
     * Gets ano_letivo
     * @return string
     */
    public function getAnoLetivo()
    {
        return $this->ano_letivo;
    }

    /**
     * Sets ano_letivo
     * @param string $ano_letivo Ano Letivo.
     * @return $this
     */
    public function setAnoLetivo($ano_letivo)
    {

        $this->ano_letivo = $ano_letivo;
        return $this;
    }

    /**
     * Gets id_semestre
     * @return int
     */
    public function getIdSemestre()
    {
        return $this->id_semestre;
    }

    /**
     * Sets id_semestre
     * @param int $id_semestre Identificador do semestre.
     * @return $this
     */
    public function setIdSemestre($id_semestre)
    {

        $this->id_semestre = $id_semestre;
        return $this;
    }

    /**
     * Gets semestre
     * @return string
     */
    public function getSemestre()
    {
        return $this->semestre;
    }

    /**
     * Sets semestre
     * @param string $semestre Semestre.
     * @return $this
     */
    public function setSemestre($semestre)
    {

        $this->semestre = $semestre;
        return $this;
    }

    /**
     * Gets id_ep_aval
     * @return int
     */
    public function getIdEpAval()
    {
        return $this->id_ep_aval;
    }

    /**
     * Sets id_ep_aval
     * @param int $id_ep_aval Identificador da \u00C9poca de Avalia\u00E7\u00E3o.
     * @return $this
     */
    public function setIdEpAval($id_ep_aval)
    {

        $this->id_ep_aval = $id_ep_aval;
        return $this;
    }

    /**
     * Gets epoca_avaliacao
     * @return string
     */
    public function getEpocaAvaliacao()
    {
        return $this->epoca_avaliacao;
    }

    /**
     * Sets epoca_avaliacao
     * @param string $epoca_avaliacao \u00C9poca de Avalia\u00E7\u00E3o (ex. Avalia\u00E7a\u00F5 Cont\u00EDnua/Peri\u00F3dica, \u00C9poca Normal, etc)
     * @return $this
     */
    public function setEpocaAvaliacao($epoca_avaliacao)
    {

        $this->epoca_avaliacao = $epoca_avaliacao;
        return $this;
    }

    /**
     * Gets estado
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Sets estado
     * @param string $estado Estado do Calendário.
     * @return $this
     * @throws api_exception When $local different then allowed values
     */
    public function setEstado($estado)
    {
        $allowed_values = array("PORELABORAR", "EMELABORACAO", "EMAPROVACAO", "APROVADO", "PUBLICADO");
        if (!in_array($estado, $allowed_values)) {
            throw new api_exception("Invalid value for 'estado', must be one of 'PORELABORAR', 'EMELABORACAO', 'EMAPROVACAO', 'APROVADO', 'PUBLICADO'");
        }
        $this->estado = $estado;
        return $this;
    }
}