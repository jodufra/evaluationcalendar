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
 * @package   local_evaluationcalendar
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
require_once $CFG->libdir . '/formslib.php';

/**
 * Class local_evaluationcalendar_section_form
 * @category Class
 */
class local_evaluationcalendar_section_form
{

    /**
     * @var array
     */
    private $sections = array('information', 'synchronize', 'settings', 'reports');

    /**
     * @var string
     */
    private $section;
    /**
     * @var \moodle_url
     */
    private $moodle_url;

    /**
     * local_evaluationcalendar_section_selector constructor.
     * @param $moodle_url \moodle_url
     * @param $section    string
     */
    public function __construct($moodle_url, $section)
    {
        $this->moodle_url = $moodle_url;
        $this->set_section($section);
    }

    public function get_section()
    {
        return $this->section;
    }

    public function set_section($section)
    {
        if (empty($section) || !in_array($section, $this->sections)) {
            $section = $this->sections[0];
        }
        $this->section = $section;
    }

    /**
     * Print html section selector form.
     */
    public function display()
    {
        $items = [];
        foreach ($this->sections as $s) {
            $section_name = get_string($s, 'local_evaluationcalendar');
            if (strcmp($s, $this->section) === 0) {
                $items[] = '<li><span>' . $section_name . '</span></li>';
            } else {
                $param = array('section' => $s);
                $items[] = '<li><a href="' . $this->moodle_url->out(true, $param) . '">' . $section_name . '</a></li>';
            }
        }
        $html = '<div class="clearfix text-center">' .
            '<nav class="breadcrumb-nav block_navigation block" style="float: none; font-size: 125%;">' .
            '<ul class="breadcrumb">';
        $divider = '<li>' .
            '<span class="divider">' .
            '<span class="accesshide"><span class="arrow_text">/</span>&nbsp;</span>' .
            '<span class="arrow sep">|</span>' .
            '</span></li>';
        $html .= implode($divider, $items);
        $html .= '</ul></nav></div>';
        echo $html;
    }

}


/**
 * Class local_evaluationcalendar_synchronize_form
 * @category Class
 */
class local_evaluationcalendar_synchronize_form extends moodleform
{

    /**
     * @throws coding_exception
     */
    protected function definition()
    {
        $mform = $this->_form;

        // information
        $mform->addElement('static', '', '', get_string('synchronize_info', 'local_evaluationcalendar'));

        // radio buttons
        $requestTypes = array();
        $requestTypes[] = $mform->createElement('radio', 'synchronize', '',
            get_string('synchronize_last_updated', 'local_evaluationcalendar'), 'last_updated');
        $requestTypes[] = $mform->createElement('radio', 'synchronize', '',
            get_string('synchronize_all', 'local_evaluationcalendar'), 'all');
        $mform->addGroup($requestTypes, 'Synchronize', get_string('synchronize', 'local_evaluationcalendar'), array(' '), false);
        $mform->setDefault('synchronize', 'last_updated');

        // submit button
        $mform->addElement('submit', 'submitbutton', get_string('synchronize_submit', 'local_evaluationcalendar'));
    }
}

/**
 * Class local_evaluationcalendar_config_form
 * @category Class
 */
class local_evaluationcalendar_config_form extends moodleform
{

    /**
     * Overridden method to use if you need to setup the form depending on current
     * values. This method is called after definition(), data submission and set_data().
     * All form setup that is dependent on form values should go in here.
     * @param $result string (Optional) Value to show in the form result static element
     */
    public function definition_after_data($result = '')
    {
        parent::definition_after_data();
        $mform = $this->_form;
        if ($mform->isSubmitted()) {
            $elem = $mform->getElement('restore_defaults');
            $value = $elem->getValue();
            if (!empty($value)) {
                $data = local_evaluationcalendar_config::Instance()->generate_form_data();
                foreach ($data as $key => $value) {
                    $elem = $mform->getElement($key);
                    $elem->setValue($value);
                }
            }
            $elem = $mform->getElement('result');
            $elem->setValue($result);
        }
    }

    /**
     * @throws coding_exception
     */
    protected function definition()
    {
        $mform = $this->_form;

        // static info
        $mform->addElement('static', '', '', get_string('config_info', 'local_evaluationcalendar'));

        // container to show result
        $mform->addElement('static', 'result', '', '');

        // auth header
        $mform->addElement('text', 'api_authorization_header_key',
            get_string('config_api_authorization_header_key', 'local_evaluationcalendar'), array('size' => '24'));
        $mform->addElement('text', 'api_authorization_header_value',
            get_string('config_api_authorization_header_value', 'local_evaluationcalendar'), array('size' => '64'));
        $mform->setType('api_authorization_header_key', PARAM_NOTAGS);
        $mform->setType('api_authorization_header_value', PARAM_NOTAGS);

        // host
        $mform->addElement('text', 'api_host', get_string('config_api_host', 'local_evaluationcalendar'), array('size' => '64'));
        $mform->setType('api_host', PARAM_NOTAGS);

        // paths
        $mform->addElement('static', '', '', get_string('config_api_paths', 'local_evaluationcalendar'));
        foreach (local_evaluationcalendar_config::Instance()->api_paths as $key => $value) {
            $mform->addElement('text', 'path_' . $key, get_string($key, 'local_evaluationcalendar'), array('size' => '40'));
            $mform->setType('path_' . $key, PARAM_NOTAGS);
        }

        // development mode
        $mform->addElement('advcheckbox', 'development_mode', get_string('config_development_mode', 'local_evaluationcalendar'));
        $mform->setType('development_mode', PARAM_BOOL);

        // buttons
        $mform->addElement('submit', 'restore_defaults', get_string('restore_defaults', 'local_evaluationcalendar'));
        $mform->addElement('submit', 'submitbutton', get_string('save', 'local_evaluationcalendar'));
    }
}


/**
 * Manage the plugin settings
 * This class provides the required functionality in order to manage the local_evaluationcalendar_config.
 * The local_evaluationcalendar_config is the container of this plugin's settings
 * It is a singleton that can be accessed through the Instance static method.
 * @category Class
 * @property array    $api_authorization_header
 * @property string   $api_host
 * @property array    $api_paths
 * @property DateTime $last_synchronization
 * @property bool     $development_mode
 */
final class local_evaluationcalendar_config
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
     * Instantiates a new local_evaluationcalendar_config
     */
    private function __construct()
    {
        $this->properties = new stdClass();
        $this->properties->api_authorization_header = local_evaluationcalendar_config::$DEFAULT_API_AUTHORIZATION_HEADER;
        $this->properties->api_host = local_evaluationcalendar_config::$DEFAULT_API_HOST;
        $this->properties->api_paths = local_evaluationcalendar_config::$DEFAULT_API_PATHS;
        $this->properties->last_synchronization = new DateTime('1970-01-01 00:00:01');
        $this->properties->development_mode = false;
        $this->read();
    }

    /**
     * Loads all lines from the database and stores them in the properties
     */
    private function read()
    {
        global $DB;
        $lines = $DB->get_records('evaluationcalendar_config');
        foreach ($lines as $line) {
            $value = json_decode($line->value);
            if (is_object($this->properties->{$line->name}) && get_class($this->properties->{$line->name}) === 'DateTime') {
                $value = new DateTime($value);
            } else if (is_object($value)) {
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
     * @see local_evaluationcalendar_config::update()
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
     * Update or create an local_evaluationcalendar_config within the database
     * Pass in a key containing the config key and the value to be updated. It search the database for a similar key,
     * if found will update it else will insert it into the database
     * @param string $key key attribute of a local_evaluationcalendar_config
     */
    private function update($key)
    {
        global $DB;

        if ($key === 'development_mode' && !$this->properties->{$key}) {
            local_evaluationcalendar_event::delete_development_events();
        }
        if (is_object($this->properties->{$key}) && get_class($this->properties->{$key}) === 'DateTime') {
            $value = $this->properties->{$key}->format('Y-m-d H:i:s');
        } else {
            $value = $this->properties->{$key};
        }
        $value = json_encode($value);
        $line = $DB->get_record('evaluationcalendar_config', array('name' => $key));
        if ($line) {
            // Update
            $line->value = $value;
            $DB->update_record('evaluationcalendar_config', $line);
        } else {
            // Insert
            $line = new stdClass();
            $line->name = $key;
            $line->value = $value;
            $line->id = $DB->insert_record('evaluationcalendar_config', $line);
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
     * @see local_evaluationcalendar_config::_set()
     */
    public function restore_defaults()
    {
        $this->api_authorization_header = local_evaluationcalendar_config::$DEFAULT_API_AUTHORIZATION_HEADER;
        $this->api_host = local_evaluationcalendar_config::$DEFAULT_API_HOST;
        $this->api_paths = local_evaluationcalendar_config::$DEFAULT_API_PATHS;
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
        foreach (local_evaluationcalendar_config::Instance()->api_paths as $key => $value) {
            $result['path_' . $key] = $value;
        }
        $result['development_mode'] = $this->properties->development_mode;
        return $result;
    }

    /**
     * Call this method to get singleton
     * @return local_evaluationcalendar_config
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new local_evaluationcalendar_config();
        }
        return $inst;
    }
}

/**
 * Class local_evaluationcalendar
 * @category Class
 */
class local_evaluationcalendar
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
     * @var local_evaluationcalendar_api_interface
     */
    private $api_interface;

    /**
     * local_evaluationcalendar constructor.
     * @param $render_html
     */
    public function __construct($render_html = false)
    {
        $this->render_html = $render_html;
        $this->api_interface = local_evaluationcalendar_api_interface::Instance();
    }

    /**
     * @param $type
     * @return array|string
     * @throws \local_evaluationcalendar\api_exception
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
     * @throws \local_evaluationcalendar\api_exception
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
        } catch (\local_evaluationcalendar\api_exception $e) {
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
            $name = get_string($type, 'local_evaluationcalendar');
            $html = $name . ':<br/>';
            if (!$error) {
                $count = count($response);
                $html .= '<span style="color:#558b2f;">Working, received ' . $count . ' ' . $name . '</span>';
            } elseif (!$critical_error) {
                $html .= '<pre style="color:#e65100;">' . $response->getOriginalMessage() . '<br/>';
                if ($response->getResponseObject()) {
                    $html .= '<p>' . $response->getResponseObject() . '</p>';
                }
                $html .= '</pre>';
            } else {
                $html .= '<pre style="color:#f00;">' . $response->getMessage() . '</pre>';
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

        // set synchronization date range depending on the optional param
        $date_epoch = new DateTime('1970-01-01 00:00:01');
        $date_last_synchronization = $update_all ? $date_epoch : local_evaluationcalendar_config::Instance()->last_synchronization;
        $date_now = new DateTime();

        // instantiate the report object
        $report = new stdClass();
        $report->task = 'synchronize';
        $report->inserts = 0;
        $report->updates = 0;
        $report->cleaned = 0;
        $report->deleted = 0;
        $report->errors = 0;
        $report->finished = false;
        $report->synchronization_date = $date_now;
        $report->logs = [];

        // we create an object with all the calendars api data
        $api_map = new stdClass();

        // there are two big groups of data we need to retrieve, the first is the recent published calendars and all its
        // evaluations, the other is the recent updated evaluations from published calendars
        // first we get all published calendars
        $api_map->calendars = $this->api_interface->get_calendars_published_updated($date_epoch, $date_now);

        // then we filter the recent from the old published calendars here,
        // since it's way faster than doing a http request for each
        $recent_published_calendars = []; // abbreviation: rpc
        $old_published_calendars = []; // abbreviation: opc
        foreach ($api_map->calendars as $calendar) {
            $updated_at = $calendar->get_updated_at();
            if ($updated_at >= $date_last_synchronization && $updated_at < $date_now) {
                $recent_published_calendars[] = $calendar;
            } else {
                $old_published_calendars[] = $calendar;
            }
        }

        // evaluations from recent published calendars
        // for these evaluations we don't apply the updated_at filter
        $rpc_evaluations = [];
        foreach ($recent_published_calendars as $calendar) {
            $evaluations = $this->api_interface->get_evaluations_by_calendar($calendar->getId());
            $rpc_evaluations = array_merge($rpc_evaluations, $evaluations);
        }

        // evaluations from old published calendars
        // this time we need to apply the updated_at filter
        $opc_evaluations = [];
        foreach ($old_published_calendars as $calendar) {
            $evaluations = $this->api_interface->get_evaluations_updated_by_calendar(
                $date_last_synchronization, $date_now, $calendar->getId());
            $opc_evaluations = array_merge($opc_evaluations, $evaluations);
        }

        // we got all the need evaluations, now its time to check for repeated evaluations and remove them,
        // and finally store the final set in the api_map
        foreach ($rpc_evaluations as $rpc_evaluation) {
            $opc_key = null;
            foreach ($opc_evaluations as $key => $opc_evaluation) {
                if (strcmp($rpc_evaluation->getId(), $opc_evaluation->getId())) {
                    $opc_key = $key;
                    break;
                }
            }
            if (is_null($opc_key)) {
                unset($opc_evaluations[$opc_key]);
            }
        }

        $api_map->evaluations = array_merge($rpc_evaluations, $opc_evaluations);

        // we get the evaluation types just for the extra information to fill in the description of a event
        $api_map->evaluation_types = $this->api_interface->get_evaluation_types();

        // we create a map of id => object foreach element type, and then we store them in a single object
        // resulting in something like: $api_assoc_map->calendars[$calendar_id]
        $api_assoc_map = new stdClass();
        // calendars map
        $api_assoc_map->calendars = array();
        for ($i = 0; $i < count($api_map->calendars); $i++) {
            $api_assoc_map->calendars[$api_map->calendars[$i]->getId()] = $api_map->calendars[$i];
        }
        // evaluations map
        $api_assoc_map->evaluations = array();
        for ($i = 0; $i < count($api_map->evaluations); $i++) {
            $api_assoc_map->evaluations[$api_map->evaluations[$i]->getId()] = $api_map->evaluations[$i];
        }
        // evaluation_types map
        $api_assoc_map->evaluation_types = array();
        for ($i = 0; $i < count($api_map->evaluation_types); $i++) {
            $api_assoc_map->evaluation_types[$api_map->evaluation_types[$i]->getId()] = $api_map->evaluation_types[$i];
        }

        // the data is retrieved and ready to proceed to the synchronizations stage
        foreach ($api_map->evaluations as $evaluation) {
            // this array will contain the tasks that need to be executed for each evaluation
            $tasks = new stdClass();
            $tasks->dirty_event_keys = array(); // "array keys" of $ec_events that need cleaning
            $tasks->dirty_calendar_keys = array(); // "array keys" of $calendar_events that need cleaning
            $tasks->insert_keys = array(); // "array keys" of $courses that require an evaluation event insert
            $tasks->update_keys = array(); // "array keys" of $calendar_events that will be updated

            // retrieve all evaluationcalendar events with the given evaluation id
            $ec_events = local_evaluationcalendar_event::read_from_evaluation_id($evaluation->getId());

            // retrieve the courses which idnumber starts with the siges code and ends with underscore and something
            $courses = $DB->get_records_select('course', "idnumber LIKE '" . $evaluation->getCodigoSiges() . "\_%'");
            if (empty($courses)) {
                if (!empty($ec_events)) {
                    // the evaluation siges code was updated and since there are no courses to match the siges code
                    // they need to be deleted
                    foreach ($ec_events as $ec_event) {
                        $ec_event->delete(true);
                        $report->deleted++;
                    }
                }
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'No courses found with given siges code.';
                $log->params = ['sigescode' => $evaluation->getCodigoSiges(), 'evaluationid' => $evaluation->getId()];
                array_push($report->logs, $log);
                $report->errors++;
                continue;
            }

            // retrieve all calendar events related with the evaluationcalendar events
            $calendar_events = array();
            foreach ($ec_events as $ec_event_key => $ec_event) {
                try {
                    $calendar_event = calendar_event::load($ec_event->eventid);
                    array_push($calendar_events, $calendar_event);
                } catch (Exception $e) {
                    array_push($tasks->dirty_event_keys, $ec_event_key);
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
                // since we are going to delete the calendar event we need to delete the related evaluationcalendar event, if any
                // so we get the array key to use in the clean evaluationcalendar_events stage
                foreach ($ec_events as $ec_event_key => $ec_event) {
                    if ($calendar_events[$calendar_event_key]->id == $ec_event->eventid) {
                        array_push($tasks->dirty_event_keys, $ec_event_key);
                        break;
                    }
                }
                // now it's time to delete the calendar event
                $calendar_events[$calendar_event_key]->delete();
                unset($calendar_events[$calendar_event_key]);
            }

            // clean the evaluationcalendar_events
            foreach ($tasks->dirty_event_keys as $key => $ec_event_key) {
                $ec_events[$ec_event_key]->delete();
                unset($ec_events[$ec_event_key]);
                $report->cleaned++;
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
                $ec_event = new local_evaluationcalendar_event();
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
                    array_push($report->logs, $log);
                    $report->errors++;
                    continue;
                }
                // then the evaluationcalendar, since we have all we need
                $ec_event->eventid = $calendar_event->id;
                $ec_event->evaluationid = $evaluation->getId();
                $ec_event->sigescode = $evaluation->getCodigoSiges();
                $ec_event = local_evaluationcalendar_event::create($ec_event->properties());
                if (!$ec_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error inserting evaluationcalendar event.';
                    $log->params = ['evaluationcalendar_event' => $ec_event];
                    array_push($report->logs, $log);
                    $report->errors++;
                    continue;
                }
                $report->inserts++;
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
                    array_push($report->logs, $log);
                    $report->errors++;
                    continue;
                }
                $report->updates++;
            }
        }

        // after the synchronization
        // set the finished flag for the report
        $report->finished = true;
        // update the last synchronization parameter in the config
        local_evaluationcalendar_config::Instance()->last_synchronization = $date_now;

        // now it's time to present the results
        if ($this->render_html) {
            if ($report->inserts || $report->cleaned || $report->updates || $report->errors || $report->deleted) {
                if ($update_all) {
                    $html = "<p style='color: black'>" . get_string('synchronize_synchronized_all', 'local_evaluationcalendar') . " ( ";
                } else {
                    $html = "<p style='color: black'>" . get_string('synchronize_synchronized', 'local_evaluationcalendar') . " ( ";
                }
                $html .= "<b style='color:#4CAF50'>" . get_string('inserts', 'local_evaluationcalendar') . ": " . $report->inserts . "</b> ";
                $html .= "<b style='color:#FF9800'>( " . get_string('cleaned', 'local_evaluationcalendar') . ": " . $report->cleaned . " )</b>, ";
                $html .= "<b style='color:#2196F3'>" . get_string('updates', 'local_evaluationcalendar') . ": " . $report->updates . "</b>, ";
                $html .= "<b style='color:#F44336'>" . get_string('errors', 'local_evaluationcalendar') . ": " . $report->errors . "</b> ";
                $html .= "<b style='color:#F44336'>( " . get_string('deleted', 'local_evaluationcalendar') . ": " . $report->deleted . " )</b> ";
                $html .= " )</p>";
                foreach ($report->logs as $log) {
                    $html .= "<p>[" . $log->type . "] " . $log->message;
                    foreach ($log->params as $key => $value) {
                        $html .= " [" . $key . " => " . $value . "]";
                    }
                    $html .= "</p>";
                }
            } else {
                $html = "<p>" . get_string('synchronize_nothing_to_synchronize', 'local_evaluationcalendar') . "</p>";
            }
            return $html;
        }
        return $report;
    }


    /**
     * @param $api_map        object
     * @param $evaluation     \local_evaluationcalendar\models\evaluation
     * @param $calendar_event calendar_event
     * @return calendar_event
     */
    private function edit_calendar_event($api_map, $evaluation, $calendar_event)
    {
        // first we get the type of evaluation
        $evaluation_type = \local_evaluationcalendar\models\evaluation_type::select_instance_from_array(
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
     * Updates the local_evaluationcalendar_config singleton with the provided config provided
     * @see local_evaluationcalendar_config::restore_defaults()
     * @param $config object Object containing the values used to update the local_evaluationcalendar_config
     * @return string|string[] If set to render html it returns html related to the success or the errors of the task,
     *                else returns an array of strings containing all errors or empty if no errors.
     */
    public function update_config($config)
    {
        $errors = array();
        if (empty($config->api_authorization_header_key)) {
            $errors[] = get_string('is_required', 'local_evaluationcalendar', get_string('config_api_authorization_header_key', 'local_evaluationcalendar'));
        }
        if (empty($config->api_authorization_header_value)) {
            $errors[] = get_string('is_required', 'local_evaluationcalendar', get_string('config_api_authorization_header_value', 'local_evaluationcalendar'));
        }
        if (count($errors) == 0) {
            $auth_header = array($config->api_authorization_header_key => $config->api_authorization_header_value);
            local_evaluationcalendar_config::Instance()->api_authorization_header = $auth_header;
        }
        if (empty($config->api_host)) {
            $errors[] = get_string('is_required', 'local_evaluationcalendar', get_string('config_api_host', 'local_evaluationcalendar'));
        } else {
            local_evaluationcalendar_config::Instance()->api_host = $config->api_host;
        }
        $api_paths = local_evaluationcalendar_config::Instance()->api_paths;
        foreach ($api_paths as $key => $value) {
            if (empty($config->{'path_' . $key})) {
                $name = get_string('config_api_paths', 'local_evaluationcalendar') . ' - ' . get_string($key, 'local_evaluationcalendar');
                $errors[] = get_string('is_required', 'local_evaluationcalendar', $name);
            } else {
                $api_paths[$key] = $config->{'path_' . $key};
            }
        }
        local_evaluationcalendar_config::Instance()->api_paths = $api_paths;
        local_evaluationcalendar_config::Instance()->development_mode = !empty($config->development_mode) && $config->development_mode;

        $has_errors = count($errors) > 0;
        if ($this->render_html) {
            if ($has_errors) {
                $html = '<p style="color: #F44336">';
                $html .= implode('<br />', $errors);
                $html .= '</p>';
            } else {
                $html = '<b style=\'color:#4CAF50\'>' . get_string('config_changes_saved', 'local_evaluationcalendar') . '</b> ';
            }
            return $html;
        }
        return $errors;
    }

    /**
     * Calls the local_evaluationcalendar_config retore_defaults function, and returns a taks done message.
     * @see local_evaluationcalendar_config::restore_defaults()
     * @return string|bool if set to render html, returns a message of task done, else returns true
     */
    public function restore_config_to_defaults()
    {
        local_evaluationcalendar_config::Instance()->restore_defaults();
        if ($this->render_html) {
            return '<b style=\'color:#4CAF50\'>' . get_string('config_defaults_restored', 'local_evaluationcalendar') . '</b> ';
        }
        return true;
    }
}

/**
 * This class provides an interface to easily access the calendars api features.
 * It is a singleton that can be accessed through the Instance static method.
 * @category Class
 */
class local_evaluationcalendar_api_interface
{

    /**
     * @var \local_evaluationcalendar\api\calendar_api
     */
    private $calendar_api;

    /**
     * @var \local_evaluationcalendar\api\evaluation_api
     */
    private $evaluation_api;

    /**
     * @var \local_evaluationcalendar\api\evaluation_type_api
     */
    private $evaluation_type_api;

    /**
     * local_evaluationcalendar_api_interface constructor.
     */
    private function __construct()
    {
        $api_client = new \local_evaluationcalendar\api_client();
        $this->calendar_api = new \local_evaluationcalendar\api\calendar_api($api_client);
        $this->evaluation_api = new \local_evaluationcalendar\api\evaluation_api($api_client);
        $this->evaluation_type_api = new \local_evaluationcalendar\api\evaluation_type_api($api_client);
    }

    /**
     * Call this method to get singleton
     * @return local_evaluationcalendar_api_interface
     */
    public static function Instance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new local_evaluationcalendar_api_interface();
        }
        return $inst;
    }

    /**
     * Gets published calendars that were updated between the given dates.
     * When any date parameters are omitted, it will retrieve all published calendars
     * @param DateTime $datetime_start Sets the lowest value of the calendar update date time filter
     * @param DateTime $datetime_end   Sets the highest value of the calendar update date time filter
     * @param string   $q              (optional) Allows to make queries over several attributes
     * @param string   $fields         (optional) Allows a selection of the attributes
     * @param string   $sort           (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\calendar[]
     */
    function get_calendars_published_updated($datetime_start, $datetime_end, $q = null, $fields = null, $sort = null)
    {
        $arguments = array();
        if (!local_evaluationcalendar_config::Instance()->development_mode)
            $arguments['estado'] = 'PUBLICADO';
        $arguments['updatedAt'] = $this->create_date_time_range_filter($datetime_start, $datetime_end, 'updatedAt');
        return $this->get_calendars($q, $fields, $sort, $arguments);
    }

    /**
     * Creates a DateTime range filter to use in the api requests
     * @param DateTime $datetime_start Sets the lowest value of the date time range filter
     * @param DateTime $datetime_end   Sets the highest value of the date time range filter
     * @param string   $argument_name  Sets the name of the argument required to set a proper range
     * @return string date range filter
     */
    private function create_date_time_range_filter($datetime_start, $datetime_end, $argument_name)
    {
        $date_filter = 'dateRange(';
        $date_filter .= $datetime_start->format('Y-m-d H:i:s');
        $date_filter .= ';' . $argument_name . ';';
        $date_filter .= $datetime_end->format('Y-m-d H:i:s');
        $date_filter .= ')';
        return $date_filter;
    }

    /**
     * Calls the calendar_api get_calendars method to get an array of calendars,
     * based on the query, fields and sort parameters.
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\calendar[]
     */
    function get_calendars($q = null, $fields = null, $sort = null, $arguments = null)
    {
        return $this->calendar_api->get_calendars($q, $fields, $sort, $arguments);
    }

    /**
     * Gets published calendars that were updated between the given dates.
     * @param string $calendar_id Sets the lowest value of the calendar update date time filter
     * @param string $q           (optional) Allows to make queries over several attributes
     * @param string $fields      (optional) Allows a selection of the attributes
     * @param string $sort        (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations_by_calendar($calendar_id, $q = null, $fields = null, $sort = null)
    {
        $arguments['idCalendario'] = $calendar_id;
        return $this->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Calls the evaluation_api get_evaluations method to get an array of evaluations,
     * based on the query, fields and sort parameters.
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations($q = null, $fields = null, $sort = null, $arguments = null)
    {
        return $this->evaluation_api->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Gets published calendars that were updated between the given dates
     * When any date parameters are omitted, it will retrieve all published calendars
     * @param DateTime $datetime_start Sets the lowest value of the calendar update date time filter
     * @param DateTime $datetime_end   Sets the highest value of the calendar update date time filter
     * @param string   $calendar_id    Sets the lowest value of the calendar update date time filter
     * @param string   $q              (optional) Allows to make queries over several attributes
     * @param string   $fields         (optional) Allows a selection of the attributes
     * @param string   $sort           (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations_updated_by_calendar($datetime_start, $datetime_end, $calendar_id, $q = null, $fields = null, $sort = null)
    {
        $arguments = array();
        $arguments['idCalendario'] = $calendar_id;
        $arguments['updatedAt'] = $this->create_date_time_range_filter($datetime_start, $datetime_end, 'updatedAt');
        return $this->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Gets published calendars that were updated between the given dates
     * When any date parameters are omitted, it will retrieve all published calendars
     * @param DateTime $datetime_start Sets the lowest value of the calendar update date time filter
     * @param DateTime $datetime_end   Sets the highest value of the calendar update date time filter
     * @param string   $q              (optional) Allows to make queries over several attributes
     * @param string   $fields         (optional) Allows a selection of the attributes
     * @param string   $sort           (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations_updated($datetime_start, $datetime_end, $q = null, $fields = null, $sort = null)
    {
        $arguments = array();
        $arguments['updatedAt'] = $this->create_date_time_range_filter($datetime_start, $datetime_end, 'updatedAt');
        return $this->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Calls the evaluation_types_api get_evaluations_types method to get an array of evaluation types,
     * based on the fields and sort parameters.
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\evaluation_type[]
     */
    function get_evaluation_types($fields = null, $sort = null, $arguments = null)
    {
        return $this->evaluation_type_api->get_evaluation_types($fields, $sort, $arguments);
    }
}


/**
 * Manage the plugin events table
 * This class provides the required functionality in order to manage the local_evaluationcalendar_events.
 * The local_evaluationcalendar_event determines the relation between the calendar_event and the "Calendars Web API"
 * evaluations.
 * @category Class
 * @property int    $id              The id within the event table
 * @property int    $eventid         The calendar event this event is associated with (0 if none)
 * @property string $evaluationid    The calendars web api evaluation id this event is associated with (empty if none)
 * @property int    $sigescode       The Siges code from calendars web api evaluation this event is associated with (0
 *           if none)
 */
class local_evaluationcalendar_event
{
    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    protected $properties = null;

    /**
     * Instantiates a new local_evaluationcalendar event and optionally populates its properties with the data provided
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
     * Creates a new event and returns a local_evaluationcalendar_event object
     * @param stdClass|array $properties An object containing event properties
     * @throws coding_exception
     * @return local_evaluationcalendar_event|bool The event object or false if it failed
     */
    public static function create($properties)
    {
        if (is_array($properties)) {
            $properties = (object)$properties;
        }
        if (!is_object($properties)) {
            throw new coding_exception('When creating an event properties should be either an object or an assoc array');
        }
        $event = new local_evaluationcalendar_event($properties);
        if ($event->update($properties)) {
            return $event;
        } else {
            return false;
        }
    }

    /**
     * Update or create an local_evaluationcalendar_event within the database
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
        $this->properties->development = local_evaluationcalendar_config::Instance()->development_mode;
        if (empty($this->properties->id) || $this->properties->id < 1) {
            // Insert
            $this->properties->id = $DB->insert_record('evaluationcalendar_event', $this->properties);
            return true;
        } else {
            // Update
            $DB->update_record('evaluationcalendar_event', $this->properties);
            $event = local_evaluationcalendar_event::read($this->properties->id);
            $this->properties = $event->properties();
            return true;
        }
    }

    /**
     * Returns a local_evaluationcalendar_event object when provided with an id
     * This function makes use of MUST_EXIST, if the id passed in is invalid
     * it will result in an exception being thrown
     * @param int|object $param event object or id
     * @return local_evaluationcalendar_event|false status for loading local_evaluationcalendar_event
     */
    public static function read($param)
    {
        global $DB;
        if (is_object($param)) {
            $event = new local_evaluationcalendar_event($param);
        } else {
            $event = $DB->get_record('evaluationcalendar_event', array('id' => (int)$param), '*', MUST_EXIST);
            $event = new local_evaluationcalendar_event($event);
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
     * Returns an array of local_evaluationcalendar_event objects when provided with a existing evaluation id
     * @param string $param evaluation id
     * @return local_evaluationcalendar_event[]|false status for loading local_evaluationcalendar_event
     */
    public static function read_from_evaluation_id($param)
    {
        global $DB;
        $events = $DB->get_records('evaluationcalendar_event', array('evaluationid' => $param));
        foreach ($events as $key => $event) {
            $events[$key] = new local_evaluationcalendar_event($event);
        }
        return $events;
    }

    /**
     * Deletes all events inserted during development stage.
     * Also deletes the related calendar events
     * @see self::delete()
     * @return bool succession of deleting event
     */
    public static function delete_development_events()
    {
        global $DB;

        $records = $DB->get_records('evaluationcalendar_event', array('development' => 1), null, 'eventid');
        if (count($records) == 0) return true;

        $DB->delete_records('evaluationcalendar_event', array('development' => 1));

        foreach ($records as $record) {
            $calendar_event = calendar_event::load((int)$record->eventid);
            $calendar_event->delete();
        }
        return true;
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
     * Deletes an local_evaluationcalendar_event, and if selected, deletes the associated calendar_event
     * This function deletes an event and the associated calendar_event if $deletecalendarevent=true.
     * This function makes use of MUST_EXIST to ensure the local_evaluationcalendar_event is valid, if not
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
        $DB->get_record('evaluationcalendar_event', array('id' => $this->id), '*', MUST_EXIST);
        // Delete the event
        $DB->delete_records('evaluationcalendar_event', array('id' => $this->id));

        if ($deletecalendarevent) {
            $calendar_event = calendar_event::load($this->eventid);
            return $calendar_event->delete();
        }
        return true;
    }
}