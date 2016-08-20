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
 * @package   local_pfc
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once $CFG->libdir . '/formslib.php';


/**
 * Class local_pfc_synchronize_calendars_form
 * @category Class
 */
class local_pfc_synchronize_form extends moodleform
{

    /**
     * @throws coding_exception
     */
    protected function definition()
    {
        $mform = $this->_form;

        // information
        $mform->addElement('static', '', '', get_string('synchronize_info', 'local_pfc'));

        // radio buttons
        $requestTypes = array();
        $requestTypes[] = $mform->createElement('radio', 'synchronize', '',
            get_string('synchronize_last_updated', 'local_pfc'), 'last_updated');
        $requestTypes[] = $mform->createElement('radio', 'synchronize', '',
            get_string('synchronize_all', 'local_pfc'), 'all');
        $mform->addGroup($requestTypes, 'Synchronize', get_string('synchronize', 'local_pfc'), array(' '), false);
        $mform->setDefault('synchronize', 'last_updated');

        // submit button
        $mform->addElement('submit', 'submitbutton', get_string('synchronize_submit', 'local_pfc'));
    }
}

/**
 * Class local_pfc_check_api_form
 * @category Class
 */
class local_pfc_config_form extends moodleform
{

    public function definition_after_data($task_result = '')
    {
        $mform = $this->_form;
        if ($mform->isSubmitted()) {
            $elem = $mform->getElement('restore_defaults');
            $value = $elem->getValue();
            if (!empty($value)) {
                $data = local_pfc_config::Instance()->generate_form_data();
                foreach ($data as $key => $value) {
                    $elem = $mform->getElement($key);
                    $elem->setValue($value);
                }
            }
            $elem = $mform->getElement('result');
            $elem->setValue($task_result);
        }
    }

    /**
     * @throws coding_exception
     */
    protected function definition()
    {
        $mform = $this->_form;

        // static info
        $mform->addElement('static', '', '', get_string('config_info', 'local_pfc'));

        // container to show result
        $mform->addElement('static', 'result', '', '');

        // auth header
        $mform->addElement('text', 'api_authorization_header_key', get_string('config_api_authorization_header', 'local_pfc'), array('size' => '24'));
        $mform->addElement('text', 'api_authorization_header_value', '', array('size' => '64'));
        $mform->setType('api_authorization_header_key', PARAM_NOTAGS);
        $mform->setType('api_authorization_header_value', PARAM_NOTAGS);

        // host
        $mform->addElement('text', 'api_host', get_string('config_api_host', 'local_pfc'), array('size' => '64'));
        $mform->setType('api_host', PARAM_NOTAGS);

        // paths
        $mform->addElement('static', '', '', get_string('config_api_paths', 'local_pfc'));
        foreach (local_pfc_config::Instance()->api_paths as $key => $value) {
            $mform->addElement('text', 'path_' . $key, get_string($key, 'local_pfc'), array('size' => '40'));
            $mform->setType('path_' . $key, PARAM_NOTAGS);
        }

        // buttons
        $mform->addElement('submit', 'restore_defaults', get_string('restore_defaults', 'local_pfc'));
        $mform->addElement('submit', 'submitbutton', get_string('save', 'local_pfc'));
    }
}


/**
 * Manage the plugin settings
 * This class provides the required functionality in order to manage the local_pfc_config.
 * The local_pfc_config is the container of this plugin's settings
 * @category Class
 * @property array  $api_authorization_header
 * @property string $api_host
 * @property array  $api_paths
 */
final class local_pfc_config
{

    /** @var string Default API authorization header */
    private static $DEFAULT_API_AUTHORIZATION_HEADER = array('Authorization' => 'Bearer 00ef34c7f062fdb0fa77dcec86db445c');
    /** @var string Default API host */
    private static $DEFAULT_API_HOST = 'https://apis.ipleiria.pt/dev/calendarios-avaliacoes/v1';
    /** @var array Default API url paths */
    private static $DEFAULT_API_PATHS = array(
        'calendars' => '/calendarios',
        'evaluations' => '/avaliacoes',
        'evaluations_ucs' => '/avaliacoes/avaliacoes-ucs',
        'evaluation_types' => '/tipos-avaliacao',
        'evaluation_type' => '/tipos-avaliacao/{idTipoAvaliacao}'
    );
    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    private $properties = null;

    /**
     * Instantiates a new local_pfc_config
     */
    private function __construct()
    {
        $this->properties = new stdClass();
        $this->properties->api_authorization_header = local_pfc_config::$DEFAULT_API_AUTHORIZATION_HEADER;
        $this->properties->api_host = local_pfc_config::$DEFAULT_API_HOST;
        $this->properties->api_paths = local_pfc_config::$DEFAULT_API_PATHS;
        $this->read();
    }

    /**
     * Loads all lines from the database and stores them in the properties
     */
    private function read()
    {
        global $DB;
        $lines = $DB->get_records('local_pfc_config');
        foreach ($lines as $line) {
            $value = json_decode($line->value);
            if (is_object($value)) {
                $value = (array)$value;
            }
            $this->properties->{$line->name} = $value;
        }
    }

    /**
     * Properties get method
     * Attempts to call a get_$key method to return the property and falls over
     * to return the raw property
     * @param string $key property name
     * @return mixed property value
     * @throws coding_exception
     */
    public function __get($key)
    {
        if (method_exists($this, 'get_' . $key)) {
            return $this->{'get_' . $key}();
        }
        if (!isset($this->properties->{$key})) {
            throw new coding_exception('Undefined property requested (' . $key . ')');
        }
        return $this->properties->{$key};
    }

    /**
     * Properties set method
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     * @see local_pfc_config::update()
     * @param string $key   property name
     * @param mixed  $value value of the property
     */
    public function __set($key, $value)
    {
        if (method_exists($this, 'set_' . $key)) {
            $this->{'set_' . $key}($value);
        } else {
            $this->properties->{$key} = $value;
            $this->update($key);
        }
    }

    /**
     * Update or create an local_pfc_config within the database
     * Pass in a key containing the config key and the value to be updated. It search the database for a similar key,
     * if found will update it else will insert it into the database
     * @param string $key key attribute of a local_pfc_config
     * @return bool event created or updated with success
     */
    private function update($key)
    {
        global $DB;
        $value = json_encode($this->properties->{$key});
        $line = $DB->get_record('local_pfc_config', array('name' => $key));
        if ($line) {
            // Update
            $line->value = $value;
            return $DB->update_record('local_pfc_config', $line);
        } else {
            // Insert
            $line = new stdClass();
            $line->name = $key;
            $line->value = $value;
            return $DB->insert_record('local_pfc_config', $line);
        }
    }

    /**
     * PHP needs an isset method if you use the properties get method and
     * still want empty calls to work
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    public function __isset($key)
    {
        return !empty($this->properties->{$key});
    }

    /**
     * Sets the properties for their default value using the dynamic _set function
     * @see local_pfc_config::_set()
     */
    public function restore_defaults()
    {
        $this->api_authorization_header = local_pfc_config::$DEFAULT_API_AUTHORIZATION_HEADER;
        $this->api_host = local_pfc_config::$DEFAULT_API_HOST;
        $this->api_paths = local_pfc_config::$DEFAULT_API_PATHS;
    }

    /**
     * Generates an assoc array with data to fill the config form
     * @return array
     */
    public function generate_form_data()
    {
        $result = [];
        $first_key = array_keys($this->properties->api_authorization_header)[0];
        $result['api_authorization_header_key'] = $first_key;
        $result['api_authorization_header_value'] = $this->properties->api_authorization_header[$first_key];
        $result['api_host'] = $this->properties->api_host;
        foreach (local_pfc_config::Instance()->api_paths as $key => $value) {
            $result['path_' . $key] = $value;
        }
        return $result;
    }

    /**
     * Call this method to get singleton
     * @return local_pfc_config
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new local_pfc_config();
        }
        return $inst;
    }
}

/**
 * Class local_pfc
 * @category Class
 */
class local_pfc
{

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
    function check_api_interface($type = '')
    {
        $result = array();
        if (array_key_exists($type, self::$api_interface_map)) {
            $result[] = $this->perform_api_interface_request($type);
        } else {
            foreach (self::$api_interface_map as $key => $getter) {
                $result[] = $this->perform_api_interface_request($key);
            }
        }

        if ($this->render_html) {
            return implode('<br/>', $result);
        } else {
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
    private function perform_api_interface_request($type)
    {
        $getter = self::$api_interface_map[$type];
        $error = false;
        $critical_error = false;

        try {
            $response = $this->api_interface->$getter();
            if (is_null($response)) {
                throw new moodle_exception("API request returned null.");
            }
        } catch (\local_pfc\api_exception $e) {
            if (!$this->render_html) {
                throw $e;
            }
            $response = $e;
            $error = true;
        } catch (moodle_exception $e) {
            if (!$this->render_html) {
                throw $e;
            }
            $response = $e;
            $error = true;
            $critical_error = true;
        }

        if ($this->render_html) {
            $name = get_string($type, 'local_pfc');
            $html = $name . ':<br/>';
            if (!$error) {
                $count = count($response);
                $html = $html . '<span style="color:#558b2f;">Working, received ' . $count . ' ' . $name . '</span>';
            } elseif (!$critical_error) {
                $html = $html . '<pre style="color:#e65100;">' . $response->getOriginalMessage() . '<br/>';
                if ($response->getResponseObject()) {
                    $html = $html . '<p>' . $response->getResponseObject() . '</p>';
                }
                $html = $html . '</pre>';
            } else {
                $html = $html . '<pre style="color:#f00;">' . $response->getMessage() . '</pre>';
            }
            $response = $html;
        }
        return $response;
    }

    /**
     * @param bool $update_all Set to true to synchronize all published evaluations, else it will only synchronize
     *                         the evaluations updated since the last synchronization.
     * @return stdClass|string
     */
    public function synchronize_evaluation_calendars($update_all = false)
    {
        global $DB;

        // first we create an object with all the calendars api data
        $api_map = new stdClass();
        $api_map->calendars = $this->api_interface->get_calendars();
        $api_map->evaluations = $this->api_interface->get_evaluations();
        $api_map->evaluation_types = $this->api_interface->get_evaluation_types();

        // for easy access, we create an object containing assoc arrays, ex: $api_assoc_map->calendars[$calendar_id]
        $api_assoc_map = new stdClass();
        // calendars
        $calendar_assoc_map = array();
        for ($i = 0; $i < count($api_map->calendars); $i++) {
            $calendar_assoc_map[$api_map->calendars[$i]->getId()] = $api_map->calendars[$i];
        }
        $api_assoc_map->calendars = $calendar_assoc_map;
        // evaluations
        $evaluations_assoc_map = array();
        for ($i = 0; $i < count($api_map->evaluations); $i++) {
            $evaluations_assoc_map[$api_map->evaluations[$i]->getId()] = $api_map->evaluations[$i];
        }
        $api_assoc_map->evaluations = $evaluations_assoc_map;
        // evaluation_types
        $evaluation_types_assoc_map = array();
        for ($i = 0; $i < count($api_map->evaluation_types); $i++) {
            $evaluation_types_assoc_map[$api_map->evaluation_types[$i]->getId()] = $api_map->evaluation_types[$i];
        }
        $api_assoc_map->evaluation_types = $evaluation_types_assoc_map;

        // instantiate the report object
        $result = new stdClass();
        $result->calendars_count = sizeof($api_map->calendars);
        $result->evaluations_count = sizeof($api_map->evaluations);
        $result->evaluation_types_count = sizeof($api_map->evaluation_types);
        $result->logs = [];
        $result->inserts = 0;
        $result->updates = 0;
        $result->cleaned = 0;
        $result->deleted = 0;
        $result->errors = 0;

        foreach ($api_map->evaluations as $evaluation) {
            // this array will contain the tasks that need to be executed for each evaluation
            $tasks = new stdClass();
            $tasks->dirty_event_keys = array(); // "array keys" of $pfc_events that need cleaning
            $tasks->dirty_calendar_keys = array(); // "array keys" of $calendar_events that need cleaning
            $tasks->insert_keys = array(); // "array keys" of $courses that require an evaluation event insert
            $tasks->update_keys = array(); // "array keys" of $calendar_events that will be updated

            // retrieve all pfc events with the given evaluation id
            $pfc_events = local_pfc_event::read_from_evaluation_id($evaluation->getId());

            // retrieve the courses which idnumber starts with the siges code and ends with underscore and something
            $courses = $DB->get_records_select('course', "idnumber LIKE '" . $evaluation->getCodigoSiges() . "\_%'");
            if (empty($courses)) {
                if (!empty($pfc_events)) {
                    // the evaluation siges code was updated and since there are no courses to match the siges code
                    // they need to be deleted
                    foreach ($pfc_events as $pfc_event) {
                        $pfc_event->delete(true);
                        $result->deleted++;
                    }
                }
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'No courses found with given siges code.';
                $log->params = ['sigescode' => $evaluation->getCodigoSiges(), 'evaluationid' => $evaluation->getId()];
                array_push($result->logs, $log);
                $result->errors++;
                continue;
            }

            // retrieve all calendar events related with the pfc events
            $calendar_events = array();
            foreach ($pfc_events as $pfc_event_key => $pfc_event) {
                try {
                    $calendar_event = calendar_event::load($pfc_event->eventid);
                    array_push($calendar_events, $calendar_event);
                } catch (Exception $e) {
                    array_push($tasks->dirty_event_keys, $pfc_event_key);
                }
            }

            // checks if the relation between the calendar and the course is still valid
            foreach ($calendar_events as $calendar_event_key => $calendar_event) {
                $dissociated = true;
                foreach ($courses as $course) {
                    if ($calendar_event->courseid == $course->id) {
                        $dissociated = false;
                        break;
                    }
                }
                if ($dissociated) {
                    array_push($tasks->dirty_calendar_keys, $calendar_event_key);
                }
            }

            // clean the calendar_events
            foreach ($tasks->dirty_calendar_keys as $key => $calendar_event_key) {
                // since we are going to delete the calendar event we need to delete the related pfc event, if any
                // so we get the array key to use in the clean pfc_events stage
                foreach ($pfc_events as $pfc_event_key => $pfc_event) {
                    if ($calendar_events[$calendar_event_key]->id == $pfc_event->eventid) {
                        array_push($tasks->dirty_event_keys, $pfc_event_key);
                        break;
                    }
                }
                // now it's time to delete the calendar event
                $calendar_events[$calendar_event_key]->delete();
                unset($calendar_events[$calendar_event_key]);
            }

            // clean the pfc_events
            foreach ($tasks->dirty_event_keys as $key => $pfc_event_key) {
                $pfc_events[$pfc_event_key]->delete();
                unset($pfc_events[$pfc_event_key]);
                $result->cleaned++;
            }

            // check what needs to be inserted or updated
            // we loop through all courses and calendar events looking for a connection, if found, it's an update
            // else it's an insert
            foreach ($courses as $course_key => $course) {
                $calendar_key = -1;
                foreach ($calendar_events as $ca_key => $calendar_event) {
                    if ($calendar_event->courseid == $course->id) {
                        $calendar_key = $ca_key;
                        break;
                    }
                }
                if ($calendar_key >= 0) {
                    array_push($tasks->update_keys, $calendar_key);
                } else {
                    array_push($tasks->insert_keys, $course_key);
                }
            }

            // insert
            foreach ($tasks->insert_keys as $key => $course_key) {
                $pfc_event = new local_pfc_event();
                $calendar_event = new calendar_event();

                // set default values
                $calendar_event->id = 0;
                $calendar_event->format = 1;
                $calendar_event->userid = 0;
                $calendar_event->modulename = 0;
                $calendar_event->eventtype = "course";

                // course id
                $calendar_event->courseid = $courses[$course_key]->id;

                // call to the function responsible to edit the calendar
                $calendar_event = $this->edit_calendar_event($api_map, $evaluation, $calendar_event);

                // now we are ready to insert
                // first the calendar to get the calendar id
                $calendar_event = calendar_event::create($calendar_event->properties());
                if (!$calendar_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error inserting calendar event.';
                    $log->params = ['calendar_event' => $calendar_event];
                    array_push($result->logs, $log);
                    $result->errors++;
                    continue;
                }
                // then the pfc, since we have all we need
                $pfc_event->eventid = $calendar_event->id;
                $pfc_event->evaluationid = $evaluation->getId();
                $pfc_event->sigescode = $evaluation->getCodigoSiges();
                $pfc_event = local_pfc_event::create($pfc_event->properties());
                if (!$pfc_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error inserting pfc event.';
                    $log->params = ['pfc_event' => $pfc_event];
                    array_push($result->logs, $log);
                    $result->errors++;
                    continue;
                }
                $result->inserts++;
            }

            // update
            foreach ($tasks->update_keys as $calendar_key) {
                $calendar_event = $calendar_events[$calendar_key];

                // call to the function responsible to edit the calendar
                $calendar_event = $this->edit_calendar_event($api_map, $evaluation, $calendar_event);

                // now we are ready to update
                // at this stage, only the calendar needs to be updated
                $calendar_event = $calendar_event->update($calendar_event);
                if (!$calendar_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error updating calendar event.';
                    $log->params = ['calendar_event' => $calendar_event];
                    array_push($result->logs, $log);
                    $result->errors++;
                    continue;
                }
                $result->updates++;
            }
        }


        if ($this->render_html) {
            $html = "<p style='color: black'>Synchronized ( ";
            $html = $html . "<b style='color:#4CAF50'>" . get_string('inserts', 'local_pfc') . ": " . $result->inserts . "</b> ";
            $html = $html . "<b style='color:#FF9800'>( " . get_string('cleaned', 'local_pfc') . ": " . $result->cleaned . " )</b>, ";
            $html = $html . "<b style='color:#2196F3'>" . get_string('updates', 'local_pfc') . ": " . $result->updates . "</b>, ";
            $html = $html . "<b style='color:#F44336'>" . get_string('errors', 'local_pfc') . ": " . $result->errors . "</b> ";
            $html = $html . "<b style='color:#F44336'>( " . get_string('deleted', 'local_pfc') . ": " . $result->deleted . " )</b> ";
            $html = $html . " )</p>";
            foreach ($result->logs as $log) {
                $html = $html . "<p>[" . $log->type . "] " . $log->message;
                foreach ($log->params as $key => $value) {
                    $html = $html . " [" . $key . " => " . $value . "]";
                }
                $html = $html . "</p>";
            }
            return $html;
        }

        return $result;
    }


    /**
     * @param $api_map        object
     * @param $evaluation     \local_pfc\models\evaluation
     * @param $calendar_event calendar_event
     * @return calendar_event
     */
    private function edit_calendar_event($api_map, $evaluation, $calendar_event)
    {
        // first we get the type of evaluation
        $evaluation_type = \local_pfc\models\evaluation_type::select_instance_from_array(
            $api_map->evaluation_types, 'id', $evaluation->getIdTipoAvaliacao());
        // then we set the name
        $calendar_event->name = (!is_null($evaluation_type) ? $evaluation_type->getAbreviatura() : '');
        if ($evaluation->getDescricao() !== '') {
            $calendar_event->name = $calendar_event->name . ' (' . $evaluation->getDescricao() . ')';
        }
        // description
        $calendar_event->description = $calendar_event->name;
        // time stamps
        $time_start = new DateTime($evaluation->getDataInicio(), new DateTimeZone('Europe/Rome'));
        $time_start_stamp = $time_start->getTimestamp();
        $time_end = new DateTime($evaluation->getDataFim(), new DateTimeZone('Europe/Rome'));
        $time_end_stamp = $time_end->getTimestamp();
        $calendar_event->timestart = $time_start_stamp;
        if ($time_end_stamp) {
            $calendar_event->timeduration = $time_end_stamp - $time_start_stamp;
        }
        return $calendar_event;
    }


    /**
     * Updates the local_pfc_config singleton with the provided config provided
     * @see local_pfc_config::restore_defaults()
     * @param $config object Object containing the values used to update the local_pfc_config
     * @return string|bool if set to render html, returns a message of task done, else returns true
     */
    public function update_config($config)
    {
        if (!is_null($config->api_authorization_header_key) && !is_null($config->api_authorization_header_value)) {
            $auth_header = array($config->api_authorization_header_key => $config->api_authorization_header_value);
            local_pfc_config::Instance()->api_authorization_header = $auth_header;
        }
        if (!is_null($config->api_host)) {
            local_pfc_config::Instance()->api_host = $config->api_host;
        }
        $api_paths = local_pfc_config::Instance()->api_paths;
        foreach ($api_paths as $key => $value) {
            if (!is_null($config->{'path_' . $key})) {
                $api_paths[$key] = $config->{'path_' . $key};
            }
        }
        local_pfc_config::Instance()->api_paths = $api_paths;

        if ($this->render_html) {
            return '<b style=\'color:#4CAF50\'>' . get_string('config_changes_saved', 'local_pfc') . '</b> ';
        }
        return true;
    }

    /**
     * Calls the local_pfc_config retore_defaults function, and returns a taks done message.
     * @see local_pfc_config::restore_defaults()
     * @return string|bool if set to render html, returns a message of task done, else returns true
     */
    public function restore_config_to_defaults()
    {
        local_pfc_config::Instance()->restore_defaults();
        if ($this->render_html) {
            return '<b style=\'color:#4CAF50\'>' . get_string('config_defaults_restored', 'local_pfc') . '</b> ';
        }
        return true;
    }
}

/**
 * Class local_pfc_api_interface
 * @category Class
 */
class local_pfc_api_interface
{

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
    private $evaluation_type_api;

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
    function get_calendars()
    {
        return $this->calendar_api->get_calendars();
    }

    /**
     * @return \local_pfc\models\evaluation[]
     */
    function get_evaluations()
    {
        return $this->evaluation_api->get_evaluations();
    }

    /**
     * @return \local_pfc\models\evaluation_type[]
     */
    function get_evaluation_types()
    {
        return $this->evaluation_type_api->get_evaluation_types();
    }
}


/**
 * Manage the plugin events table
 * This class provides the required functionality in order to manage the local_pfc_events.
 * The local_pfc_event determines the relation between the calendar_event and the "Calendars Web API" evaluations.
 * @category Class
 * @property int    $id              The id within the event table
 * @property int    $eventid         The calendar event this event is associated with (0 if none)
 * @property string $evaluationid    The calendars web api evaluation id this event is associated with (empty if none)
 * @property int    $sigescode       The Siges code from calendars web api evaluation this event is associated with (0
 *           if none)
 */
class local_pfc_event
{
    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    protected $properties = null;

    /**
     * Instantiates a new local_pfc event and optionally populates its properties with the data provided
     * @param stdClass $data Optional. An object containing the properties to for an event
     */
    public function __construct($data = null)
    {
        // First convert to object if it is not already (should either be object or assoc array)
        if (!is_object($data)) {
            $data = (object)$data;
        }

        if (empty($data->id)) {
            $data->id = null;
        }
        if (empty($data->eventid)) {
            $data->eventid = 0;
        }
        if (empty($data->evaluationid)) {
            $data->evaluationid = "";
        }
        $this->properties = $data;
    }

    /**
     * Creates a new event and returns a local_pfc_event object
     * @param stdClass|array $properties An object containing event properties
     * @throws coding_exception
     * @return local_pfc_event|bool The event object or false if it failed
     */
    public static function create($properties)
    {
        if (is_array($properties)) {
            $properties = (object)$properties;
        }
        if (!is_object($properties)) {
            throw new coding_exception('When creating an event properties should be either an object or an assoc array');
        }
        $event = new local_pfc_event($properties);
        if ($event->update($properties)) {
            return $event;
        } else {
            return false;
        }
    }

    /**
     * Update or create an local_pfc_event within the database
     * Pass in a object containing the event properties and this function will
     * insert it into the database
     * @see self::create()
     * @see self::update()
     * @param stdClass $data object of event
     * @return bool event created or updated with success
     */
    public function update($data)
    {
        global $DB;

        foreach ($data as $key => $value) {
            $this->properties->$key = $value;
        }

        if (empty($this->properties->id) || $this->properties->id < 1) {
            // Insert
            $this->properties->id = $DB->insert_record('local_pfc_event', $this->properties);
            return true;
        } else {
            // Update
            $DB->update_record('local_pfc_event', $this->properties);
            $event = local_pfc_event::read($this->properties->id);
            $this->properties = $event->properties();
            return true;
        }
    }

    /**
     * Returns a local_pfc_event object when provided with an id
     * This function makes use of MUST_EXIST, if the id passed in is invalid
     * it will result in an exception being thrown
     * @param int|object $param event object or id
     * @return local_pfc_event|false status for loading local_pfc_event
     */
    public static function read($param)
    {
        global $DB;
        if (is_object($param)) {
            $event = new local_pfc_event($param);
        } else {
            $event = $DB->get_record('local_pfc_event', array('id' => (int)$param), '*', MUST_EXIST);
            $event = new local_pfc_event($event);
        }
        return $event;
    }

    /**
     * Fetch all event properties
     * This function returns all of the events properties as an object
     * @return stdClass Object containing event properties
     */
    public function properties()
    {
        return clone($this->properties);
    }

    /**
     * Returns an array of local_pfc_event objects when provided with a existing evaluation id
     * @param string $param evaluation id
     * @return local_pfc_event[]|false status for loading local_pfc_event
     */
    public static function read_from_evaluation_id($param)
    {
        global $DB;
        $events = $DB->get_records('local_pfc_event', array('evaluationid' => $param));
        foreach ($events as $key => $event) {
            $events[$key] = new local_pfc_event($event);
        }
        return $events;
    }

    /**
     * Properties get method
     * Attempts to call a get_$key method to return the property and falls over
     * to return the raw property
     * @param string $key property name
     * @return mixed property value
     * @throws coding_exception
     */
    public function __get($key)
    {
        if (method_exists($this, 'get_' . $key)) {
            return $this->{'get_' . $key}();
        }
        if (!isset($this->properties->{$key})) {
            throw new coding_exception('Undefined property requested');
        }
        return $this->properties->{$key};
    }

    /**
     * Properties set method
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     * @param string $key   property name
     * @param mixed  $value value of the property
     */
    public function __set($key, $value)
    {
        if (method_exists($this, 'set_' . $key)) {
            $this->{'set_' . $key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * PHP needs an isset method if you use the properties get method and
     * still want empty calls to work
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    public function __isset($key)
    {
        return !empty($this->properties->{$key});
    }

    /**
     * Deletes an local_pfc_event, and if selected, deletes the associated calendar_event
     * This function deletes an event and the associated calendar_event if $deletecalendarevent=true.
     * This function makes use of MUST_EXIST to ensure the local_pfc_event is valid, if not
     * it will result in an exception being thrown
     * @see self::delete()
     * @param bool $deletecalendarevent delete calendar_event
     * @return bool succession of deleting event
     */
    public function delete($deletecalendarevent = false)
    {
        global $DB;

        // If $this->properties->id is not set then something is wrong
        if (empty($this->id) || $this->id < 1) {
            debugging('Attempting to delete an event before it has been loaded', DEBUG_DEVELOPER);
            return false;
        }

        // Ensures there is an event to be deleted
        $DB->get_record('local_pfc_event', array('id' => $this->id), '*', MUST_EXIST);
        // Delete the event
        $DB->delete_records('local_pfc_event', array('id' => $this->id));

        if ($deletecalendarevent) {
            $calendar_event = calendar_event::load($this->eventid);
            return $calendar_event->delete();
        }
        return true;
    }
}