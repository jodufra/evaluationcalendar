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
 * @package local_pfc
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once $CFG->libdir . '/formslib.php';


/**
 * Class local_pfc_check_api_form
 *
 * @category Class
 */
class local_pfc_check_api_form extends moodleform {

    /**
     * @throws coding_exception
     */
    protected function definition() {
        $pfc_form = $this->_form;

        // information
        $pfc_form->addElement('static', '', '', get_string('checkapi_info', 'local_pfc'));

        // radio buttons
        $requestTypes = array();
        $requestTypes[] = $pfc_form->createElement('radio', 'requesttype', '',
            get_string('checkapi_all','local_pfc'), 'all');
        $requestTypes[] = $pfc_form->createElement('radio', 'requesttype', '',
            get_string('checkapi_calendars','local_pfc'), 'calendars');
        $requestTypes[] = $pfc_form->createElement('radio', 'requesttype', '',
            get_string('checkapi_evaluations','local_pfc'), 'evaluations');
        $requestTypes[] = $pfc_form->createElement('radio', 'requesttype', '',
            get_string('checkapi_evaluation_types','local_pfc'), 'evaluation_types');
        $pfc_form->addGroup($requestTypes, 'requestTypes', '', array(' '), false);
        $pfc_form->setDefault('requesttype', 'all');

        // submit button
        $pfc_form->addElement('submit', 'submitbutton', get_string('checkapi_submit', 'local_pfc'));
    }
}

/**
 * Class local_pfc_synchronize_calendars_form
 *
 * @category Class
 */
class local_pfc_synchronize_calendars_form extends moodleform {

    /**
     * @throws coding_exception
     */
    protected function definition() {
        $pfc_form = $this->_form;

        // information
        $pfc_form->addElement('static', '', '', get_string('synchronize_calendars_info', 'local_pfc'));

        // radio buttons
        $requestTypes = array();
        $requestTypes[] = $pfc_form->createElement('radio', 'synchronize', '',
            get_string('synchronize_calendars_submit','local_pfc'), 'true');
        $pfc_form->addGroup($requestTypes, 'Synchronize', '', array(' '), false);
        $pfc_form->setDefault('synchronize', 'true');

        // submit button
        $pfc_form->addElement('submit', 'submitbutton', get_string('synchronize_calendars_submit', 'local_pfc'));
    }
}


/**
 * Class local_pfc_config
 *
 * @category Class
 */
final class local_pfc_config{

    /**
     * API authorization header
     */
    public static $API_AUTHORIZATION_HEADER = array('Authorization' => 'Bearer 00ef34c7f062fdb0fa77dcec86db445c');

    /**
     * API host
     */
    const API_HOST = 'https://apis.ipleiria.pt/dev/calendarios-avaliacoes/v1';

    /**
     * API url paths
     */
    public static $API_PATHS = array(
        'calendars' => '/calendarios',
        'evaluations' => '/avaliacoes',
        'evaluations_ucs' => '/avaliacoes/avaliacoes-ucs',
        'evaluation_types' => '/tipos-avaliacao',
        'evaluation_type' => '/tipos-avaliacao/{idTipoAvaliacao}'
    );

    /**
     * local_pfc_config constructor.
     */
    public function __construct()
    {
        throw new coding_exception("local_pfc_config can't be instantiated.");
    }
}

/**
 * Class local_pfc
 *
 * @category Class
 */
class local_pfc{

    /**
     * @var array
     */
    private static $api_interface_map = array(
        'calendars' => 'get_calendars',
        'evaluations' => 'get_evaluations',
        'evaluation_types' => 'get_evaluation_types'
    );

    /**
     * @var boolean
     */
    private $render_html;

    /**
     * @var local_pfc_api_interface
     */
    private $api_interface;

    /**
     * local_pfc constructor.
     * @param $render_html
     */
    public function __construct($render_html = false)
    {
        $this->render_html = $render_html;
        $this->api_interface = new local_pfc_api_interface();
    }

    /**
     * @param $type
     * @return array|string
     * @throws \local_pfc\api_exception
     * @throws moodle_exception
     */
    function check_api_interface($type = '') {
        $result = array();
        if(array_key_exists($type, self::$api_interface_map)){
            $result[] = $this->perform_api_interface_request($type);
        }else{
            foreach (self::$api_interface_map as $key => $getter) {
                $result[] = $this->perform_api_interface_request($key);
            }
        }

        if($this->render_html){
            return implode('<br/>',$result);
        }else{
            return $result;
        }
    }

    /**
     * @param $type
     * @return string
     * @throws \local_pfc\api_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    private function perform_api_interface_request($type) {
        $getter = self::$api_interface_map[$type];
        $error = false;
        $critical_error = false;

        try {
            $response = $this->api_interface->$getter();
            if(is_null ($response)){
                throw new moodle_exception("API request returned null.");
            }
        }catch (\local_pfc\api_exception $e){
            if(!$this->render_html){
                throw $e;
            }
            $response = $e;
            $error = true;
        }catch (moodle_exception $e){
            if(!$this->render_html){
                throw $e;
            }
            $response = $e;
            $error = true;
            $critical_error = true;
        }
        
        if($this->render_html){
            $name = get_string('checkapi_'.$type,'local_pfc');
            $html = $name.':<br/>';
            if(!$error){
                $count = count($response);
                $html = $html . '<span style="color:#558b2f;">Working, received '.$count.' '.$name.'</span>';
            }elseif(!$critical_error){
                $html = $html.'<pre style="color:#e65100;">'.$response->getOriginalMessage().'<br/>';
                if($response->getResponseObject()){
                    $html = $html.'<p>'.$response->getResponseObject().'</p>';
                }
                $html = $html.'</pre>';
            }else{
                $html = $html.'<pre style="color:#f00;">'.$response->getMessage().'</pre>';
            }
            $response = $html;
        }
        return $response;
    }

    /**
     * @return stdClass|string
     */
    function synchronize_evaluation_calendars() {
        $result = new stdClass();
        $result->evaluations = 0;
        $result->errors = 0;

        $calendars = $this->api_interface->get_calendars();
        $evaluations = $this->api_interface->get_evaluations();
        $evaluation_types = $this->api_interface->get_evaluation_types();

        foreach ($evaluations as $evaluation){
            $calendar_event = new \calendar_event();
            $event = new stdClass();
            $event->id = 0;
            // name
            $getter = \local_pfc\models\evaluation_type::$getters['id'];
            $evaluation_type = $this->select_instance_from_array(
                $evaluation_types, $getter, $evaluation->getIdTipoAvaliacao());
            $event->name = !is_null($evaluation_type) ? $evaluation_type->getDescricao() : '';

            // description, format
            $event->description = $evaluation->getDescricao();
            $event->format = 1;

            // course id
            $event->courseid = 2;
            $event->userid = 0;

            // modulename, event type
            $event->modulename = 0;
            $event->eventtype = "course";

            // Time stamps
            $time_start = new DateTime($evaluation->getDataInicio(), new DateTimeZone('Europe/Rome'));
            $time_start_stamp = $time_start->getTimestamp();
            $time_end = new DateTime($evaluation->getDataFim(), new DateTimeZone('Europe/Rome'));
            $time_end_stamp = $time_end->getTimestamp();
            $event->timestart = $time_start_stamp;
            if($time_end_stamp){
                $event->timeduration = $time_end_stamp - $time_start_stamp;
            }

            // Insert event
            $result->evaluations++;
            if(!$calendar_event->update($event))
                $result->errors++;
        }

        if($this->render_html){
            $html = "<p>Synchronized [";
            $html = $html."<span style='color:#558b2f'>Evaluations: ".$result->evaluations."</span>, ";
            $html = $html."<span style='color:#e65100'>Errors: ".$result->errors."</span>";
            $html = $html."]</p>";
            return $html;
        }
        return $result;
    }

    /**
     * @param $array
     * @param $getter
     * @param $comparation_value
     * @return mixed|null
     */
    private function select_instance_from_array($array, $getter, $comparation_value){
        $instance = NULL;
        foreach ($array as $element){
            if($element->$getter() == $comparation_value){
                $instance = $element;
                break;
            }
        }
        return $instance;
    }
}

/**
 * Class local_pfc_api_interface
 *
 * @category Class
 */
class local_pfc_api_interface {

    /**
     * @var \local_pfc\api\calendar_api
     */
    private $calendar_api;

    /**
     * @var \local_pfc\api\evaluation_api
     */
    private $evaluation_api;

    /**
     * @var \local_pfc\api\evaluation_type_api
     */
    private  $evaluation_type_api;

    /**
     * local_pfc_api_interface constructor.
     */
    public function __construct()
    {
        $api_client = new \local_pfc\api_client();
        $this->calendar_api = new \local_pfc\api\calendar_api($api_client);
        $this->evaluation_api = new \local_pfc\api\evaluation_api($api_client);
        $this->evaluation_type_api = new \local_pfc\api\evaluation_type_api($api_client);
    }

    /**
     * @return \local_pfc\models\calendar[]
     */
    function get_calendars() {
        return $this->calendar_api->get_calendars();
    }

    /**
     * @return \local_pfc\models\evaluation[]
     */
    function get_evaluations() {
        return $this->evaluation_api->get_evaluations();
    }

    /**
     * @return \local_pfc\models\evaluation_type[]
     */
    function get_evaluation_types() {
        return $this->evaluation_type_api->get_evaluation_types();
    }
}