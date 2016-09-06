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
 * @package   local_evaluationcalendar
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once $CFG->libdir . '/formslib.php';

/**
 * This plugin views are separated by sections and this form allows the selection of the section.
 *
 * @category Class
 */
class local_evaluationcalendar_section_form {

    /**
     * @var array
     */
    private $sections = array('synchronize', 'settings', 'reports');

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
     *
     * @param $moodle_url \moodle_url
     * @param $section    string
     */
    public function __construct($moodle_url, $section) {
        $this->moodle_url = $moodle_url;
        $this->set_section($section);
    }

    public function get_section() {
        return $this->section;
    }

    public function set_section($section) {
        if (empty($section) || !in_array($section, $this->sections)) {
            $section = $this->sections[0];
        }
        $this->section = $section;
    }

    /**
     * Print html section selector form.
     */
    public function display() {
        $items = [];
        foreach ($this->sections as $s) {
            $li_class = strcmp($s, $this->section) === 0 ? ' class="active"' : '';
            $a_style = strcmp($s, $this->section) === 0 ? ' style="color:inherit;"' : '';
            $href = $this->moodle_url->out(true, array('section' => $s));
            $name = get_string($s, 'local_evaluationcalendar');
            $items[] = '<li' . $li_class . '><a href="' . $href . '"' . $a_style . '>' . $name . '</a></li>';
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
 * This moodleform presents the user the possibility to execute several types of synchronization
 *
 * @category Class
 */
class local_evaluationcalendar_synchronize_form extends moodleform {

    /**
     * @throws coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        // information
        $mform->addElement('static', '', '', get_string('synchronize_info', 'local_evaluationcalendar'));

        // options
        $types = [];
        $string = get_string('synchronize_schedules', 'local_evaluationcalendar');
        $types['schedules'] = $string;
        $string = get_string('synchronize_last_updated_evaluations', 'local_evaluationcalendar');
        $types['last_updated_evaluations'] = $string;
        $string = get_string('synchronize_all_evaluations', 'local_evaluationcalendar');
        $types['all_evaluations'] = $string;
        $string = get_string('clean_evaluations', 'local_evaluationcalendar');
        $types['clean_evaluations'] = $string;

        $string = get_string('select_task', 'local_evaluationcalendar');
        $mform->addElement('select', 'synchronize', $string, $types);
        $mform->addHelpButton('synchronize', 'synchronize', 'local_evaluationcalendar');
        $mform->addRule('synchronize', get_string('error'), 'required');
        $mform->setDefault('synchronize', 'schedules');
        // submit button
        $string = get_string('submit', 'local_evaluationcalendar');
        $mform->addElement('submit', 'submitbutton', $string);
    }
}

/**
 * This moodleform displays a friendly interface to change this plugins configurations
 *
 * @category Class
 */
class local_evaluationcalendar_config_form extends moodleform {

    /**
     * Overridden method to use if you need to setup the form depending on current
     * values. This method is called after definition(), data submission and set_data().
     * All form setup that is dependent on form values should go in here.
     *
     * @param $result string (Optional) Value to show in the form result static element
     */
    public function definition_after_data($result = '') {
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
        }
    }

    /**
     * @throws coding_exception
     */
    protected function definition() {
        $mform = $this->_form;

        $small = array('size' => '24');
        $normal = array('size' => '48');
        $big = array('size' => '96');

        // static info
        $name = get_string('config_info', 'local_evaluationcalendar');
        $mform->addElement('static', '', '', $name);

        // api auth header key
        $key = 'api_authorization_header_key';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('text', $key, $name, $small);
        $mform->setType($key, PARAM_NOTAGS);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');
        // api auth header value
        $key = 'api_authorization_header_value';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('text', $key, $name, $big);
        $mform->setType($key, PARAM_NOTAGS);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // api host
        $key = 'api_host';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('text', $key, $name, $big);
        $mform->setType($key, PARAM_NOTAGS);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // api paths
        $name = get_string('api_paths', 'local_evaluationcalendar');
        $mform->addElement('static', '', '', $name);
        foreach (local_evaluationcalendar_config::Instance()->api_paths as $key => $value) {
            $name = get_string($key, 'local_evaluationcalendar');
            $mform->addElement('text', 'path_' . $key, $name, $normal);
            $mform->setType('path_' . $key, PARAM_NOTAGS);
            $mform->addHelpButton('path_' . $key, $key, 'local_evaluationcalendar');
            $mform->addRule('path_' . $key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');
        }

        // separator
        $mform->addElement('static', '', '', '');

        // schedule csv url
        $key = 'schedule_csv_url';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('text', $key, $name, $big);
        $mform->setType($key, PARAM_NOTAGS);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // schedule csv dirty src
        $key = 'schedule_csv_dirty_src';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('advcheckbox', $key, $name);
        $mform->setType($key, PARAM_BOOL);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // schedule csv delimiter
        $key = 'schedule_csv_delimiter';
        $delimiters = csv_import_reader::get_delimiter_list();
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('select', $key, $name, $delimiters);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // schedule csv encoding
        $key = 'schedule_csv_encoding';
        $encodings = core_text::get_encodings();
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('select', $key, $name, $encodings);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // separator
        $mform->addElement('static', '', '', '');

        // api auth header key
        $key = 'school_year';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('text', $key, $name, $small);
        $mform->setType($key, PARAM_NOTAGS);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');
        $mform->addRule($key, get_string('is_required', 'local_evaluationcalendar', $name), 'required');

        // separator
        $mform->addElement('static', '', '', '');

        // development mode
        $key = 'development_mode';
        $name = get_string($key, 'local_evaluationcalendar');
        $mform->addElement('advcheckbox', $key, $name);
        $mform->setType($key, PARAM_BOOL);
        $mform->addHelpButton($key, $key, 'local_evaluationcalendar');

        // buttons
        $name = get_string('restore_defaults', 'local_evaluationcalendar');
        $mform->addElement('submit', 'restore_defaults', $name);
        $name = get_string('save', 'local_evaluationcalendar');
        $mform->addElement('submit', 'submitbutton', $name);
    }
}

/**
 * This class is responsible to gather and show the reports
 *
 * @category Class
 */
class local_evaluationcalendar_reports_view {

    /**
     * @var local_evaluationcalendar_reports_view
     */
    private static $instance;

    /**
     * @var string Common date format used in this class
     */
    private $DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var local_evaluationcalendar_report[]
     */
    private $reports;

    /**
     * @var \moodle_url
     */
    private $moodle_url;

    /**
     * local_evaluationcalendar_reports_view constructor.
     */
    private function __construct() {
        $this->reports = local_evaluationcalendar_report::read_all();
    }

    /**
     * Gets singleton instance of local_evaluationcalendar_reports_view
     *
     * @param $moodle_url \moodle_url
     * @return local_evaluationcalendar_reports_view
     */
    public static function Instance($moodle_url = null) {
        if (!isset(local_evaluationcalendar_reports_view::$instance)) {
            local_evaluationcalendar_reports_view::$instance = new local_evaluationcalendar_reports_view();
        }
        if (isset($moodle_url)) {
            local_evaluationcalendar_reports_view::$instance->moodle_url = $moodle_url;
        }
        return local_evaluationcalendar_reports_view::$instance;
    }

    /**
     * @return string
     */
    public function reports() {
        if (count($this->reports) == 0) {
            return get_string('no_reports_found', 'local_evaluationcalendar');
        }

        $result = '<h3>' . get_string('reports', 'local_evaluationcalendar') . '</h3>';

        $table = new html_table();
        $table->head = array('task', 'inserts', 'cleaned', 'updates', 'errors', 'deleted', 'date', 'details');
        foreach ($table->head as $key => $value) {
            $table->head[$key] = get_string($value, 'local_evaluationcalendar');
        }
        $details_string = get_string('details', 'local_evaluationcalendar');
        foreach ($this->reports as $report) {
            $data = [];
            $data[] = get_string($report->task, 'local_evaluationcalendar');
            $data[] = $report->inserts;
            $data[] = $report->cleaned;
            $data[] = $report->updates;
            $data[] = $report->errors;
            $data[] = $report->deleted;
            $data[] = (new DateTime())->setTimestamp($report->timecreated)->format($this->DATE_FORMAT);
            $id = $report->id;
            $url = $this->moodle_url->out(true, array('reportid' => $id));
            $data[] = '<a href="' . $url . '"> ' . $details_string . '</a>';
            $table->data[] = $data;
        }
        $result .= html_writer::table($table);
        return $result;
    }

    /**
     * @param $id
     * @return string
     */
    public function report($id) {
        $go_back_url = $this->moodle_url->out(true, array('reportid' => 0));
        $go_back_string = get_string('go_back', 'local_evaluationcalendar');
        $go_back_link = '<a href="' . $go_back_url . '"> ' . $go_back_string . '</a>';
        if (!isset($this->reports[$id])) {
            $not_available_string = get_string('report_not_available', 'local_evaluationcalendar');
            return $not_available_string . " " . $go_back_link;
        }

        $report = $this->reports[$id];
        $result = '<h3>' . get_string('report', 'local_evaluationcalendar') . ': '
                . get_string($report->task, 'local_evaluationcalendar') . '</h3>';

        $table = new html_table();
        $table->head = array('inserts', 'cleaned', 'updates', 'errors', 'deleted', 'date', 'go_back');
        foreach ($table->head as $key => $value) {
            $table->head[$key] = get_string($value, 'local_evaluationcalendar');
        }
        $data = [];
        $data[] = $report->inserts;
        $data[] = $report->cleaned;
        $data[] = $report->updates;
        $data[] = $report->errors;
        $data[] = $report->deleted;
        $data[] = (new DateTime())->setTimestamp($report->timecreated)->format($this->DATE_FORMAT);
        $data[] = $go_back_link;
        $table->data[] = $data;
        $result .= html_writer::table($table);

        $result .= '<br>';

        $table = new html_table();
        $table->head = array(get_string('logs', 'local_evaluationcalendar'), '', '');
        foreach ($report->logs as $log) {
            $tr = new html_table_row();
            $tr->attributes['class'] = strcmp("Error", $log->type) ? 'danger' : strcmp("Warning", $log->type) ? 'warning' : 'info';

            $td = new html_table_cell();
            $td->text = $log->type;
            $tr->cells[] = $td;

            $td = new html_table_cell();
            $td->text = $log->message;
            $tr->cells[] = $td;

            $td = new html_table_cell();
            $td->text = count($log->params) > 0 ? json_encode($log->params, JSON_PRETTY_PRINT) : '';
            $tr->cells[] = $td;

            $table->data[] = $tr;
        }
        $result .= html_writer::table($table);

        return $result;
    }
}

/**
 * It's the container of the most features this plugin has to offer.
 * If you wish to use the plugin, it's in this class you should look first.
 *
 * @category Class
 */
class local_evaluationcalendar {

    /**
     * @var array
     */
    private static $api_interface_map = array(
            'calendars' => 'get_calendars',
            'evaluations' => 'get_evaluations',
            'evaluation_types' => 'get_evaluation_types',
            'schedules' => 'get_schedules'
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
     *
     * @param $render_html
     */
    public function __construct($render_html = false) {
        $this->render_html = $render_html;
        $this->api_interface = local_evaluationcalendar_api_interface::Instance();
    }

    /**
     * @param $type
     * @return array|string
     * @throws \local_evaluationcalendar\api_exception
     * @throws moodle_exception
     */
    function verify_api_interface($type = '') {
        $result = array();
        if (array_key_exists($type, self::$api_interface_map)) {
            $result[] = $this->verify_api_interface_single_request($type);
        } else {
            foreach (self::$api_interface_map as $key => $getter) {
                $result[] = $this->verify_api_interface_single_request($key);
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
     * @return array|string
     * @throws \local_evaluationcalendar\api_exception
     * @throws moodle_exception
     */
    private function verify_api_interface_single_request($type) {

        if (!array_key_exists($type, self::$api_interface_map)) {
            throw new coding_exception('Must select a valid api interface request type.');
        }

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
     * @return object|string
     */
    function synchronize_schedules() {
        global $DB;

        // set school year
        $school_year = local_evaluationcalendar_config::Instance()->school_year;
        $school_year = str_replace('/', '', $school_year);

        // instantiate the report object
        $report = new stdClass();
        $report->task = 'synchronize_schedules';
        $report->inserts = 0;
        $report->updates = 0;
        $report->cleaned = 0;
        $report->deleted = 0;
        $report->errors = 0;
        $report->finished = false;
        $report->logs = [];

        $schedules = $this->api_interface->get_schedules();
        if (count($schedules)) {
            if (!local_evaluationcalendar_schedule::delete_all()) {
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'Unknown error while attempting to delete old schedules.';
                $log->params = [];
                $report->logs[] = $log;
                $report->errors++;
            }
        } else {
            $log = new stdClass();
            $log->type = 'Log';
            $log->message = 'No schedules to synchronize. The old ones were not deleted.';
            $log->params = [];
            $report->logs[] = $log;
        }

        $records = array();
        foreach ($schedules as $schedule) {
            // check if schedule belongs to the correct school year
            if (strcmp($school_year, $schedule->get_school_year())) {
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'Wrong school year.';
                $log->params = ['school_year' => $school_year, 'schedule_school_year' => $schedule->get_school_year()];
                $report->logs[] = $log;
                $report->errors++;
                continue;
            }

            // parse the schedule to get the needed information
            // siges code
            $sigescode = trim($schedule->get_course_code()) . '' . trim($schedule->get_subject_code_abbr());
            // semester
            $semester = substr(trim($schedule->get_semester()), 1);
            // subject shift
            $trimmed = trim($schedule->get_subject_designation_shift());
            $pos = strpos($trimmed, '(') + 1;
            $subject_shift = substr($trimmed, $pos);
            $pos = strpos($subject_shift, ')');
            $subject_shift = substr($subject_shift, 0, $pos);

            // get courses and check if any
            $courses = $this->get_courses_by_partial_idnumber($sigescode, $semester);
            if (empty($courses)) {
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'No courses found with given sigescode and semester.';
                $log->params = ['sigescode' => $sigescode, 'semester' => $semester];
                $report->logs[] = $log;
                $report->errors++;
                continue;
            }

            // get groups and check if any
            $groups = [];
            foreach ($courses as $course) {
                $query = 'courseid = ' . $course->id . ' AND idnumber LIKE \'' . $subject_shift . '\'';
                $groups = array_merge($groups, $DB->get_records_select('groups', $query));
            }
            if (empty($groups)) {
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'No groups found with given courseid and idnumber.';
                $log->params = ['courseid' => $sigescode, 'idnumber' => $subject_shift];
                $report->logs[] = $log;
                $report->errors++;
                continue;
            }

            // since we have all we need we can create an evaluationcalendar schedule
            foreach ($groups as $group) {
                $record = new stdClass();
                $record->groupid = $group->id;
                $record->courseid = $group->courseid;
                $record->weeks = implode(';', $schedule->get_week_numbers());
                $record->weekday = $schedule->get_week_day();
                $record->timestart = $schedule->get_time_start();
                $key = $record->courseid . "_" . $record->groupid . "_" . $record->weekday . "_" . $record->timestart;
                $records[$key] = $record;
            }
        }

        // it would be performance stressing to insert them one by one, so we insert them all at once
        $count = count($records);
        if ($count > 0) {
            local_evaluationcalendar_schedule::insert_records($records);
            $report->inserts = $count;
        }

        // after the synchronization
        // set the finished flag, save and return the report
        $report->finished = true;
        $report = local_evaluationcalendar_report::create($report);

        if ($this->render_html) {
            if ($report->inserts || $report->errors) {
                $result = "<div class='alert alert-success'>";
                $result .= "<b>" . get_string('synchronized_schedules', 'local_evaluationcalendar') . "</b>";
                $result .= " (" . get_string('inserts', 'local_evaluationcalendar') . ": " . $report->inserts . ", ";
                $result .= get_string('errors', 'local_evaluationcalendar') . ": " . $report->errors . ")";
                $result .= "</div>";
            } else {
                $result = '<div class=\'alert\'>';
                $result .= '<b>' . get_string('nothing_to_synchronize', 'local_evaluationcalendar') . '</b>';
                $result .= '</div>';
            }
            return $result;
        }
        return $report;
    }

    /**
     * Gets courses which idnumber starts with $siges_code and ends in "_S$semester"
     *
     * @param $siges_code string The join between the course code and subject code
     * @param $semester   string (optional) The subject semester
     * @return array
     */
    private function get_courses_by_partial_idnumber($siges_code, $semester = null) {

        global $DB;
        $query = "idnumber LIKE '" . $siges_code . "\_%'";
        if (!is_null($semester)) {
            $query .= " AND idnumber LIKE '%\_S" . $semester . "'";
        }
        return $DB->get_records_select('course', $query);
    }

    /**
     * @param bool $update_all Set to true to synchronize all published evaluations, else it will only synchronize
     *                         the evaluations updated since the last synchronization.
     * @return object|string
     */
    function synchronize_evaluation_calendars($update_all = false) {
        global $DB;

        // set synchronization date range depending on the optional param
        $date_epoch = new DateTime('1970-01-01 00:00:01');
        $date_last_synchronization = $update_all ? $date_epoch : local_evaluationcalendar_config::Instance()->last_synchronization;
        $date_now = new DateTime();

        // set school year
        $school_year = local_evaluationcalendar_config::Instance()->school_year;

        // instantiate the report object
        $report = new stdClass();
        $report->task = $update_all ? 'synchronize_all_evaluations' : 'synchronize_last_updated_evaluations';
        $report->inserts = 0;
        $report->updates = 0;
        $report->cleaned = 0;
        $report->deleted = 0;
        $report->errors = 0;
        $report->finished = false;
        $report->logs = [];

        // we create an object with all the calendars api data
        $api_map = new stdClass();

        // there are two big groups of data we need to retrieve, the first is the recent published calendars and all its
        // evaluations, the other is the recent updated evaluations from published calendars
        // first we get all published calendars
        $api_map->calendars = $this->api_interface->get_calendars_published_updated($date_epoch, $date_now, $school_year);

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
            $evaluations = $this->api_interface->get_evaluations_by_calendar($calendar->get_id());
            $rpc_evaluations = array_merge($rpc_evaluations, $evaluations);
        }

        // evaluations from old published calendars
        // this time we need to apply the updated_at filter
        $opc_evaluations = [];
        foreach ($old_published_calendars as $calendar) {
            $evaluations = $this->api_interface->get_evaluations_updated_by_calendar(
                    $date_last_synchronization, $date_now, $calendar->get_id());
            $opc_evaluations = array_merge($opc_evaluations, $evaluations);
        }

        // we got all the need evaluations, now its time to check for repeated evaluations and remove them,
        // and finally store the final set in the api_map
        foreach ($rpc_evaluations as $rpc_evaluation) {
            $opc_key = null;
            foreach ($opc_evaluations as $key => $opc_evaluation) {
                if (strcmp($rpc_evaluation->get_id(), $opc_evaluation->get_id())) {
                    $opc_key = $key;
                    break;
                }
            }
            if (!is_null($opc_key)) {
                unset($opc_evaluations[$opc_key]);
            }
        }

        // join the different evaluations
        $api_map->evaluations = array_merge($rpc_evaluations, $opc_evaluations);

        // we get the evaluation types just for the extra information to fill in the description of a event
        $api_map->evaluation_types = $this->api_interface->get_evaluation_types();

        // get all schedules
        $api_map->schedules = \local_evaluationcalendar_schedule::read_all();

        // we create a map of id => object foreach element type, and then we store them in a single object
        // resulting in something like: $api_assoc_map->calendars[$calendar_id]
        $api_assoc_map = new stdClass();
        // calendars map
        $api_assoc_map->calendars = array();
        for ($i = 0; $i < count($api_map->calendars); $i++) {
            $api_assoc_map->calendars[$api_map->calendars[$i]->get_id()] = $api_map->calendars[$i];
        }
        // evaluations map
        $api_assoc_map->evaluations = array();
        for ($i = 0; $i < count($api_map->evaluations); $i++) {
            $api_assoc_map->evaluations[$api_map->evaluations[$i]->get_id()] = $api_map->evaluations[$i];
        }
        // evaluation_types map
        $api_assoc_map->evaluation_types = array();
        for ($i = 0; $i < count($api_map->evaluation_types); $i++) {
            $api_assoc_map->evaluation_types[$api_map->evaluation_types[$i]->get_id()] = $api_map->evaluation_types[$i];
        }

        // the data is retrieved and ready to proceed to the synchronizations stage
        foreach ($api_map->evaluations as $evaluation) {
            // this array will contain the tasks that need to be executed for each evaluation
            $tasks = new stdClass();
            $tasks->dirty_event_ids = array(); // array ids of $ec_events that need cleaning
            $tasks->dirty_calendar_ids = array(); // array of ids from calendar events that need cleaning
            $tasks->insert_references = array(); // array of objects, each containing a courseid and groupid
            $tasks->update_calendar_ids = array(); // array of ids from calendar events that will be updated

            // get calendar
            $calendar = $api_assoc_map->calendars[$evaluation->get_calendar_id()];

            // retrieve all evaluation calendar events with the given evaluation id
            $ec_events = local_evaluationcalendar_event::read_from_evaluation_id($evaluation->get_id());

            // retrieve the courses which idnumber starts with the siges code and ends with underscore and something
            $courses = $this->get_courses_by_partial_idnumber($evaluation->get_siges_code(), $calendar->get_semester_id());
            if (empty($courses)) {
                if (!empty($ec_events)) {
                    // If it reaches here means that the evaluation siges code was updated or the courses related to that siges code
                    // were deleted. So it means that we have to delete the calendar events previously fetched
                    foreach ($ec_events as $ec_event) {
                        $ec_event->delete(true);
                        $report->deleted++;
                    }
                }
                $log = new stdClass();
                $log->type = 'Error';
                $log->message = 'No courses found with given siges code.';
                $log->params = ['sigescode' => $evaluation->get_siges_code(), 'evaluationid' => $evaluation->get_id()];
                $report->logs[] = $log;
                $report->errors++;
                continue;
            }

            // retrieves the groups of the retrieved courses
            $query = 'courseid IN (' . implode(',', array_keys($courses)) . ')';
            $groups = $DB->get_records_select('groups', $query);
            $groups[0] = array('id' => '0', 'courseid' => '0', 'name' => '');

            // retrieve schedules related to the courses and to this evaluation
            // get time variables to use later
            $date_start = new DateTime($evaluation->get_date_begin());
            $weeknumber = (string) $date_start->format("W");
            $weekday = $date_start->format("N") + 1;
            $weekday = $weekday > 7 ? 1 : $weekday;
            $timestart = $date_start->format("G:i");

            $schedules = array();
            foreach ($api_map->schedules as $sch) {
                $weeks = explode(';', $sch->weeks);
                if (isset($groups[$sch->groupid]) && in_array($weeknumber, $weeks) && $sch->weekday == $weekday &&
                        $sch->timestart == $timestart
                ) {
                    $schedules[] = $sch;
                }
            }

            // retrieve all calendar events related with the evaluationcalendar events
            $calendar_events = array();
            foreach ($ec_events as $ec_event_key => $ec_event) {
                try {
                    $calendar_event = calendar_event::load($ec_event->eventid);
                    $calendar_events[$calendar_event->id] = $calendar_event;
                } catch (Exception $e) {
                    // means the calendar event was deleted outside this plugin
                    $tasks->dirty_event_ids[] = $ec_event_key;
                }
            }

            // checks if the relation between the calendar and the course and group is still valid
            foreach ($calendar_events as $calendar_event_key => $calendar_event) {
                if (!isset($courses[$calendar_event->courseid]) || !isset($groups[$calendar_event->groupid])) {
                    $tasks->dirty_calendar_ids[] = $calendar_event_key;
                }
            }

            // clean the calendar_events
            foreach ($tasks->dirty_calendar_ids as $key => $calendar_event_key) {
                // since we are going to delete the calendar event we need to delete the related evaluationcalendar event, if any.
                // so we get the array key to use in the clean evaluationcalendar_events stage
                foreach ($ec_events as $ec_event_id => $ec_event) {
                    if ($calendar_events[$calendar_event_key]->id == $ec_event->eventid) {
                        $tasks->dirty_event_ids[] = $ec_event_id;
                        break;
                    }
                }
                // now it's time to delete the calendar event
                $calendar_events[$calendar_event_key]->delete();
                unset($calendar_events[$calendar_event_key]);
            }

            // clean the evaluationcalendar_events
            foreach ($tasks->dirty_event_ids as $key => $ec_event_key) {
                $ec_events[$ec_event_key]->delete();
                unset($ec_events[$ec_event_key]);
                $report->cleaned++;
            }

            // check what needs to be inserted or updated
            // we loop through all courses and calendar events looking for a connection, if found, it's an update
            // else it's an insert
            foreach ($schedules as $schedule) {
                $course_id = $schedule->courseid;
                $group_id = $schedule->groupid;
                $calendar_id = 0;
                foreach ($calendar_events as $ca_id => $calendar_event) {
                    if ($calendar_event->courseid == $course_id && $calendar_event->groupid == $group_id) {
                        $calendar_id = $ca_id;
                        break;
                    }
                }
                if ($calendar_id > 0) {
                    $tasks->update_calendar_ids[$calendar_id] = $calendar_id;
                } else {
                    $tasks->insert_references[] = array('courseid' => $course_id, 'groupid' => $group_id);
                }
            }
            foreach ($courses as $course_id => $course) {
                $inserted_by_schedule = false;
                foreach ($schedules as $schedule) {
                    if ($schedule->courseid == $course_id) {
                        $inserted_by_schedule = true;
                        break;
                    }
                }
                if ($inserted_by_schedule) {
                    continue;
                }

                $calendar_id = 0;
                foreach ($calendar_events as $ca_id => $calendar_event) {
                    if ($calendar_event->courseid == $course->id && $calendar_event->groupid == 0) {
                        $calendar_id = $ca_id;
                        break;
                    }
                }
                if ($calendar_id > 0) {
                    $tasks->update_calendar_ids[$calendar_id] = $calendar_id;
                } else {
                    $tasks->insert_references[] = array('courseid' => $course_id, 'groupid' => 0);
                }
            }

            // insert
            foreach ($tasks->insert_references as $reference) {
                $course = (object) $courses[$reference['courseid']];
                $group = (object) $groups[$reference['groupid']];

                // calendar_event comes first
                $calendar_event = new calendar_event();
                $calendar_event->id = 0; // default value
                $calendar_event->format = 1; // default value
                $calendar_event->userid = 0;  // default value
                $calendar_event->modulename = 0;  // default value
                $calendar_event->courseid = $course->id;
                $calendar_event->groupid = $group->id;
                $calendar_event->eventtype = $calendar_event->groupid == 0 ? "course" : "group";
                // call to the function responsible to edit the calendar
                $calendar_event = $this->edit_calendar_event($api_map, $evaluation, $calendar_event, $course, $group);

                // now we are ready to insert
                // first the calendar to get the calendar id
                $calendar_event = calendar_event::create($calendar_event->properties());
                if (!$calendar_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error inserting calendar event.';
                    $log->params = ['calendar_event' => $calendar_event];
                    $report->logs[] = $log;
                    $report->errors++;
                    continue;
                }
                // then the evaluationcalendar, since we have all we need
                $ec_event = new local_evaluationcalendar_event();
                $ec_event->eventid = $calendar_event->id;
                $ec_event->evaluationid = $evaluation->get_id();
                $ec_event->sigescode = $evaluation->get_siges_code();
                $ec_event = local_evaluationcalendar_event::create($ec_event->properties());
                if (!$ec_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error inserting evaluationcalendar event.';
                    $log->params = ['evaluationcalendar_event' => $ec_event];
                    $report->logs[] = $log;
                    $report->errors++;
                    continue;
                }
                $report->inserts++;
            }

            // update
            foreach ($tasks->update_calendar_ids as $calendar_id) {
                $calendar_event = $calendar_events[$calendar_id];
                $course = $courses[$calendar_event->courseid];
                $group = $groups[$calendar_event->groupid];

                // call to the function responsible to edit the calendar
                $calendar_event = $this->edit_calendar_event($api_map, $evaluation, $calendar_event, $course, $group);

                // now we are ready to update
                // at this stage, only the calendar needs to be updated
                $calendar_event = $calendar_event->update($calendar_event);
                if (!$calendar_event) {
                    $log = new stdClass();
                    $log->type = 'Error';
                    $log->message = 'Error updating calendar event.';
                    $log->params = array('calendar_id' => $calendar_id);
                    $report->logs[] = $log;
                    $report->errors++;
                    continue;
                }
                $report->updates++;
            }
        }

        // update the last synchronization parameter in the config
        local_evaluationcalendar_config::Instance()->last_synchronization = $date_now;

        // after the synchronization
        // set the finished flag, save and return the report
        $report->finished = true;
        $report = local_evaluationcalendar_report::create($report);

        if ($this->render_html) {
            if ($report->inserts || $report->cleaned || $report->updates || $report->errors || $report->deleted) {
                $message_key = $update_all ? 'synchronized_all_evaluations' : 'synchronized_last_updated_evaluations';
                $result = "<div class='alert alert-success'>";
                $result .= "<b>" . get_string($message_key, 'local_evaluationcalendar') . "</b>";
                $result .= " (" . get_string('inserts', 'local_evaluationcalendar') . ": " . $report->inserts;
                $result .= " (" . get_string('cleaned', 'local_evaluationcalendar') . ": " . $report->cleaned . "), ";
                $result .= get_string('updates', 'local_evaluationcalendar') . ": " . $report->updates . ", ";
                $result .= get_string('errors', 'local_evaluationcalendar') . ": " . $report->errors;
                $result .= " (" . get_string('deleted', 'local_evaluationcalendar') . ": " . $report->deleted . "))";
                $result .= "</div>";
            } else {
                $result = '<div class=\'alert\'>';
                $result .= '<b>' . get_string('nothing_to_synchronize', 'local_evaluationcalendar') . '</b>';
                $result .= '</div>';
            }
            return $result;
        }
        return $report;
    }

    /**
     * @param $api_map        object
     * @param $evaluation     \local_evaluationcalendar\models\evaluation
     * @param $calendar_event calendar_event
     * @param $course         object
     * @param $group          object
     * @return calendar_event
     */
    private function edit_calendar_event($api_map, $evaluation, $calendar_event, $course, $group) {

        // type of evaluation, to add some information in the name and description
        $evaluation_type = \local_evaluationcalendar\models\evaluation_type::select_instance_from_array(
                $api_map->evaluation_types, 'id', $evaluation->get_evaluation_type_id());

        // name
        $calendar_event->name = '';
        if (!is_null($evaluation_type)) {
            $calendar_event->name .= $evaluation_type->get_abbreviation();
        }
        if ($evaluation->get_description() != '') {
            $calendar_event->name .= ' (' . $evaluation->get_description() . ')';
        }

        // description
        $calendar_event->description = '';
        if (!is_null($evaluation_type)) {
            $calendar_event->description .= '<p>' . $evaluation_type->get_description() . '</p>';
        }
        if ($evaluation->get_room() != '') {
            $calendar_event->description .= '<p>' . $evaluation->get_room() . '</p>';
        }

        // time stamps // new DateTimeZone('Europe/Rome') may be needed
        // start
        $date_start = new DateTime($evaluation->get_date_begin());
        $timestamp_start = $date_start->getTimestamp();
        $calendar_event->timestart = $timestamp_start;
        // end
        $date_end = new DateTime($evaluation->get_date_end());
        $timestamp_end = $date_end->getTimestamp();
        if ($timestamp_end) {
            $calendar_event->timeduration = $timestamp_end - $timestamp_start;
        }
        return $calendar_event;
    }

    /**
     * @return object|string
     */
    function clean_evaluation_calendars() {
        // set date range depending on the optional param
        $date_epoch = new DateTime('1970-01-01 00:00:01');
        $date_now = new DateTime();

        // set school year
        $school_year = local_evaluationcalendar_config::Instance()->school_year;

        // instantiate the report object
        $report = new stdClass();
        $report->task = 'clean_evaluations';
        $report->inserts = 0;
        $report->updates = 0;
        $report->cleaned = 0;
        $report->deleted = 0;
        $report->errors = 0;
        $report->finished = false;
        $report->logs = [];

        // first we get all published calendars and then the related evaluations
        $calendars = $this->api_interface->get_calendars_published_updated($date_epoch, $date_now, $school_year);
        $retrieved_evaluations = [];
        foreach ($calendars as $calendar) {
            $calendar_evaluations = $this->api_interface->get_evaluations_by_calendar($calendar->get_id());
            $retrieved_evaluations = array_merge($retrieved_evaluations, $calendar_evaluations);
        }

        $evaluations = [];
        foreach ($retrieved_evaluations as $evaluation) {
            $evaluations[$evaluation->get_id()] = $evaluation;
        }

        $ec_events = local_evaluationcalendar_event::read_all();
        $to_clean = [];
        foreach ($ec_events as $id => $ec_event) {
            if (!isset($evaluations[$ec_event->evaluationid])) {
                $to_clean[$id] = $id;
            }
        }
        foreach ($to_clean as $id) {
            if ($ec_events[$id]->delete(true)) {
                $report->cleaned++;
            } else {
                $report->errors++;
            }
        }

        $report->finished = true;
        $report = local_evaluationcalendar_report::create($report);

        if ($this->render_html) {
            if ($report->cleaned || $report->errors) {
                $result = "<div class='alert alert-success'>";
                $result .= "<b>" . get_string('cleaned_evaluations', 'local_evaluationcalendar') . "</b>";
                $result .= " (" . get_string('cleaned', 'local_evaluationcalendar') . ": " . $report->cleaned . ", ";
                $result .= get_string('errors', 'local_evaluationcalendar') . ": " . $report->errors . ")";
                $result .= "</div>";
            } else {
                $result = '<div class=\'alert\'>';
                $result .= '<b>' . get_string('nothing_to_clean', 'local_evaluationcalendar') . '</b>';
                $result .= '</div>';
            }
            return $result;
        }
        return $report;
    }

    /**
     * Updates the local_evaluationcalendar_config singleton with the provided config provided
     *
     * @see local_evaluationcalendar_config::restore_defaults()
     * @param $config object Object containing the values used to update the local_evaluationcalendar_config
     * @return string Returns the success message. If set to render html the success message is enclosed in a green bold html tag
     */
    function update_config($config) {

        $instance = local_evaluationcalendar_config::Instance();

        // api authorization header
        $auth_header = array($config->api_authorization_header_key => $config->api_authorization_header_value);
        $instance->api_authorization_header = $auth_header;

        // api host
        $instance->api_host = $config->api_host;

        // api paths
        $api_paths = $instance->api_paths;
        foreach ($api_paths as $key => $value) {
            $api_paths[$key] = $config->{'path_' . $key};
        }
        $instance->api_paths = $api_paths;

        // schedule csv url
        $instance->schedule_csv_url = $config->schedule_csv_url;

        // development mode
        $instance->schedule_csv_dirty_src = $config->schedule_csv_dirty_src;

        // schedule csv delimiter
        $delimiters = csv_import_reader::get_delimiter_list();
        $instance->schedule_csv_delimiter = $delimiters[$config->schedule_csv_delimiter];

        // schedule csv encoding
        $encodings = core_text::get_encodings();
        $instance->schedule_csv_encoding = $encodings[$config->schedule_csv_encoding];

        // development mode
        $instance->development_mode = $config->development_mode;

        // development mode
        $instance->school_year = $config->school_year;

        $message = get_string('config_changes_saved', 'local_evaluationcalendar');
        return $this->render_html ? ('<b style="color:#4CAF50">' . $message . '</b>') : $message;
    }

    /**
     * Calls the local_evaluationcalendar_config retore_defaults function, and returns a taks done message.
     *
     * @see local_evaluationcalendar_config::restore_defaults()
     * @return string|bool if set to render html, returns a message of task done, else returns true
     */
    function restore_config_to_defaults() {
        local_evaluationcalendar_config::Instance()->restore_defaults();
        $message = get_string('config_defaults_restored', 'local_evaluationcalendar');
        return $this->render_html ? ('<b style="color:#4CAF50">' . $message . '</b>') : $message;
    }
}

/**
 * This class provides an interface to easily access the calendars api features.
 * It is a singleton that can be accessed through the Instance static method.
 *
 * @category Class
 */
class local_evaluationcalendar_api_interface {

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
     * @var \local_evaluationcalendar\api\schedule_api
     */
    private $schedule_api;

    /**
     * local_evaluationcalendar_api_interface constructor.
     */
    private function __construct() {
        $api_client = new \local_evaluationcalendar\api_client();
        $this->calendar_api = new \local_evaluationcalendar\api\calendar_api($api_client);
        $this->evaluation_api = new \local_evaluationcalendar\api\evaluation_api($api_client);
        $this->evaluation_type_api = new \local_evaluationcalendar\api\evaluation_type_api($api_client);

        $api_configuration = new \local_evaluationcalendar\api_configuration();
        $api_configuration->setHost(local_evaluationcalendar_config::Instance()->schedule_csv_url);
        $api_client = new \local_evaluationcalendar\api_client($api_configuration);
        $this->schedule_api = new \local_evaluationcalendar\api\schedule_api($api_client);
    }

    /**
     * Call this method to get singleton
     *
     * @return local_evaluationcalendar_api_interface
     */
    public static function Instance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new local_evaluationcalendar_api_interface();
        }
        return $inst;
    }

    /**
     * Gets published calendars from the give school year that were updated between the given dates
     *
     * @param DateTime $datetime_start Sets the lowest value of the calendar update date time filter
     * @param DateTime $datetime_end   Sets the highest value of the calendar update date time filter
     * @param string   $school_year    Sets school year filter
     * @param string   $q              (optional) Allows to make queries over several attributes
     * @param string   $fields         (optional) Allows a selection of the attributes
     * @param string   $sort           (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\calendar[]
     */
    function get_calendars_published_updated($datetime_start, $datetime_end, $school_year, $q = null, $fields = null,
            $sort = null) {
        $arguments = array();
        if (!local_evaluationcalendar_config::Instance()->development_mode) {
            $arguments['estado'] = 'PUBLICADO';
        }
        $arguments['updatedAt'] = $this->create_date_time_range_filter($datetime_start, $datetime_end, 'updatedAt');
        $arguments['anoLetivo'] = $school_year;
        return $this->get_calendars($q, $fields, $sort, $arguments);
    }

    /**
     * Creates a DateTime range filter to use in the api requests
     *
     * @param DateTime $datetime_start Sets the lowest value of the date time range filter
     * @param DateTime $datetime_end   Sets the highest value of the date time range filter
     * @param string   $argument_name  Sets the name of the argument required to set a proper range
     * @return string date range filter
     */
    private function create_date_time_range_filter($datetime_start, $datetime_end, $argument_name) {
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
     *
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\calendar[]
     */
    function get_calendars($q = null, $fields = null, $sort = null, $arguments = null) {
        return $this->calendar_api->get_calendars($q, $fields, $sort, $arguments);
    }

    /**
     * Gets published calendars that were updated between the given dates
     * When any date parameters are omitted, it will retrieve all published calendars
     *
     * @param DateTime $datetime_start Sets the lowest value of the calendar update date time filter
     * @param DateTime $datetime_end   Sets the highest value of the calendar update date time filter
     * @param string   $q              (optional) Allows to make queries over several attributes
     * @param string   $fields         (optional) Allows a selection of the attributes
     * @param string   $sort           (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations_updated($datetime_start, $datetime_end, $q = null, $fields = null, $sort = null) {
        $arguments = array();
        $arguments['updatedAt'] = $this->create_date_time_range_filter($datetime_start, $datetime_end, 'updatedAt');
        return $this->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Calls the evaluation_api get_evaluations method to get an array of evaluations,
     * based on the query, fields and sort parameters.
     *
     * @param string $q         (optional) Allows to make queries over several attributes
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations($q = null, $fields = null, $sort = null, $arguments = null) {
        return $this->evaluation_api->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Gets published calendars that were updated between the given dates.
     *
     * @param string $calendar_id Sets the lowest value of the calendar update date time filter
     * @param string $q           (optional) Allows to make queries over several attributes
     * @param string $fields      (optional) Allows a selection of the attributes
     * @param string $sort        (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations_by_calendar($calendar_id, $q = null, $fields = null, $sort = null) {
        $arguments['idCalendario'] = $calendar_id;
        return $this->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Gets published calendars that were updated between the given dates
     * When any date parameters are omitted, it will retrieve all published calendars
     *
     * @param DateTime $datetime_start Sets the lowest value of the calendar update date time filter
     * @param DateTime $datetime_end   Sets the highest value of the calendar update date time filter
     * @param string   $calendar_id    Sets the lowest value of the calendar update date time filter
     * @param string   $q              (optional) Allows to make queries over several attributes
     * @param string   $fields         (optional) Allows a selection of the attributes
     * @param string   $sort           (optional) Allows sorting the results by attribute
     * @return \local_evaluationcalendar\models\evaluation[]
     */
    function get_evaluations_updated_by_calendar($datetime_start, $datetime_end, $calendar_id, $q = null, $fields = null,
            $sort = null) {
        $arguments = array();
        $arguments['idCalendario'] = $calendar_id;
        $arguments['updatedAt'] = $this->create_date_time_range_filter($datetime_start, $datetime_end, 'updatedAt');
        return $this->get_evaluations($q, $fields, $sort, $arguments);
    }

    /**
     * Calls the evaluation_types_api get_evaluations_types method to get an array of evaluation types,
     * based on the fields and sort parameters.
     *
     * @param string $fields    (optional) Allows a selection of the attributes
     * @param string $sort      (optional) Allows sorting the results by attribute
     * @param array  $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\evaluation_type[]
     */
    function get_evaluation_types($fields = null, $sort = null, $arguments = null) {
        return $this->evaluation_type_api->get_evaluation_types($fields, $sort, $arguments);
    }

    /**
     * Calls the schedule_api get_schedules method to get an array of schedules.
     *
     * @param array $arguments (optional) Allows custom arguments be passed to the query string
     * @return \local_evaluationcalendar\models\schedule[]
     */
    function get_schedules($arguments = null) {
        $encoding = local_evaluationcalendar_config::Instance()->schedule_csv_encoding;;
        $delimiter = local_evaluationcalendar_config::Instance()->schedule_csv_delimiter;
        $dirty_src = local_evaluationcalendar_config::Instance()->schedule_csv_dirty_src;
        return $this->schedule_api->get_schedules($encoding, $delimiter, $dirty_src, $arguments);
    }
}

/**
 * Manage the plugin config table
 * This class provides the required functionality in order to manage the local_evaluationcalendar_config.
 * The local_evaluationcalendar_config is the container of this plugin's settings
 * It is a singleton that can be accessed through the Instance static method.
 *
 * @category Class
 * @property array    $api_authorization_header
 * @property string   $api_host
 * @property array    $api_paths
 * @property string   $schedule_csv_url
 * @property bool     $schedule_csv_dirty_src
 * @property string   $schedule_csv_delimiter
 * @property string   $schedule_csv_encoding
 * @property DateTime $last_synchronization
 * @property string   $school_year
 * @property bool     $development_mode
 */
class local_evaluationcalendar_config {

    /** @var array Default API authorization header */
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

    /** @var string Default Schedule csv url */
    private static $DEFAULT_SCHEDULE_CSV_URL = 'http://www.dei.estg.ipleiria.pt/intranet/horarios/ws/get_horarios_agcp.php';

    /** @var bool Default Schedule csv delimiter */
    private static $DEFAULT_SCHEDULE_CSV_DIRTY_SRC = true;

    /** @var string Default Schedule csv delimiter */
    private static $DEFAULT_SCHEDULE_CSV_DELIMITER = ';';

    /** @var string Default Schedule csv encoding */
    private static $DEFAULT_SCHEDULE_CSV_ENCODING = 'ISO-8859-1';

    /** @var string Default school year */
    private static $DEFAULT_SCHOOL_YEAR = '2015/16';

    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    private $properties = null;

    /**
     * Instantiates a new local_evaluationcalendar_config
     */
    private function __construct() {
        $this->properties = new stdClass();
        // have default
        $this->properties->api_authorization_header = local_evaluationcalendar_config::$DEFAULT_API_AUTHORIZATION_HEADER;
        $this->properties->api_host = local_evaluationcalendar_config::$DEFAULT_API_HOST;
        $this->properties->api_paths = local_evaluationcalendar_config::$DEFAULT_API_PATHS;
        $this->properties->schedule_csv_url = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_URL;
        $this->properties->schedule_csv_dirty_src = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_DIRTY_SRC;
        $this->properties->schedule_csv_delimiter = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_DELIMITER;
        $this->properties->schedule_csv_encoding = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_ENCODING;
        $this->properties->school_year = local_evaluationcalendar_config::$DEFAULT_SCHOOL_YEAR;
        // doesn't have default
        $this->properties->last_synchronization = new DateTime('1970-01-01 00:00:01');
        $this->properties->development_mode = false;
        $this->read();
    }

    /**
     * Loads all lines from the database and stores them in the properties
     */
    private function read() {
        global $DB;
        $lines = $DB->get_records('evaluationcalendar_config');
        foreach ($lines as $line) {
            $value = json_decode($line->value);
            if ($this->properties->{$line->name} instanceof \DateTime) {
                $value = new DateTime($value);
            } elseif (is_object($value)) {
                $value = (array) $value;
            }
            $this->properties->{$line->name} = $value;
        }
    }

    /**
     * Properties get method
     * Attempts to call a get_$key method to return the property and falls over
     * to return the raw property
     *
     * @param string $key property name
     * @return mixed property value
     * @throws coding_exception
     */
    function __get($key) {
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
     *
     * @see local_evaluationcalendar_config::update()
     * @param string $key   property name
     * @param mixed  $value value of the property
     */
    function __set($key, $value) {
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
     *
     * @param string $key key attribute of a local_evaluationcalendar_config
     */
    private function update($key) {
        global $DB;

        // per key changes
        if ($key === 'development_mode' && !$this->properties->{$key}) {
            local_evaluationcalendar_event::delete_development_events();
        }
        // per value type changes
        if ($this->properties->{$key} instanceof \DateTime) {
            $value = $this->properties->{$key}->format('Y-m-d H:i:s');
        } else {
            $value = $this->properties->{$key};
        }

        // db insert or update
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
     *
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    function __isset($key) {
        return !empty($this->properties->{$key});
    }

    /**
     * Sets the properties for their default value using the dynamic _set function
     *
     * @see local_evaluationcalendar_config::_set()
     */
    function restore_defaults() {
        $this->api_authorization_header = local_evaluationcalendar_config::$DEFAULT_API_AUTHORIZATION_HEADER;
        $this->api_host = local_evaluationcalendar_config::$DEFAULT_API_HOST;
        $this->api_paths = local_evaluationcalendar_config::$DEFAULT_API_PATHS;
        $this->schedule_csv_url = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_URL;
        $this->schedule_csv_dirty_src = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_DIRTY_SRC;
        $this->schedule_csv_delimiter = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_DELIMITER;
        $this->schedule_csv_encoding = local_evaluationcalendar_config::$DEFAULT_SCHEDULE_CSV_ENCODING;
        $this->school_year = local_evaluationcalendar_config::$DEFAULT_SCHOOL_YEAR;
    }

    /**
     * Generates an assoc array with data to fill the config form
     *
     * @return array
     */
    function generate_form_data() {
        $result = [];
        $first_key = array_keys($this->properties->api_authorization_header)[0];
        $result['api_authorization_header_key'] = $first_key;
        $result['api_authorization_header_value'] = $this->properties->api_authorization_header[$first_key];
        $result['api_host'] = $this->properties->api_host;
        foreach (local_evaluationcalendar_config::Instance()->api_paths as $key => $value) {
            $result['path_' . $key] = $value;
        }
        $result['schedule_csv_url'] = $this->properties->schedule_csv_url;
        $result['schedule_csv_dirty_src'] = $this->properties->schedule_csv_dirty_src;
        $delimiters = csv_import_reader::get_delimiter_list();
        foreach ($delimiters as $key => $value) {
            if ($this->properties->schedule_csv_delimiter == $value) {
                $result['schedule_csv_delimiter'] = $key;
                break;
            }
        }
        $encodings = core_text::get_encodings();
        foreach ($encodings as $key => $value) {
            if ($this->properties->schedule_csv_encoding == $value) {
                $result['schedule_csv_encoding'] = $key;
                break;
            }
        }
        $result['school_year'] = $this->properties->school_year;
        $result['development_mode'] = $this->properties->development_mode;
        return $result;
    }

    /**
     * Call this method to get singleton
     *
     * @return local_evaluationcalendar_config
     */
    static function Instance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new local_evaluationcalendar_config();
        }
        return $inst;
    }
}

/**
 * Manage the plugin events table
 * This class provides the required functionality in order to manage the local_evaluationcalendar_event.
 * The local_evaluationcalendar_event determines the relation between the calendar_event and the "Calendars Web API"
 * evaluations.
 *
 * @category Class
 * @property int    $id                The id within the event table
 * @property int    $eventid           The calendar event this event is associated with (0 if none)
 * @property string $evaluationid      The calendars web api evaluation id this event is associated with (empty if none)
 * @property int    $sigescode         The Siges code from calendars web api evaluation this event is associated with (0
 *           if none)
 * @property bool   $development       Flag indicating if this event was created during development mode
 */
class local_evaluationcalendar_event {

    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    protected $properties = null;

    /**
     * Instantiates a new local_evaluationcalendar event and optionally populates its properties with the data provided
     *
     * @param stdClass $data Optional. An object containing the properties to for an event
     */
    public function __construct($data = null) {
        // First convert to object if it is not already (should either be object or assoc array)
        if (!is_object($data)) {
            $data = (object) $data;
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
        if (empty($data->development)) {
            $data->development = local_evaluationcalendar_config::Instance()->development_mode;
        }
        $this->properties = $data;
    }

    /**
     * Creates a new event and returns a local_evaluationcalendar_event object
     *
     * @param stdClass|array $properties An object containing event properties
     * @throws coding_exception
     * @return local_evaluationcalendar_event|bool The event object or false if it failed
     */
    public static function create($properties) {
        if (is_array($properties)) {
            $properties = (object) $properties;
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
     *
     * @see self::create()
     * @see self::update()
     * @param stdClass $data object of event
     * @return bool event created or updated with success
     */
    public function update($data) {
        global $DB;

        foreach ($data as $key => $value) {
            $this->properties->$key = $value;
        }
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
     *
     * @param int|object $param event object or id
     * @return local_evaluationcalendar_event|false status for loading local_evaluationcalendar_event
     */
    public static function read($param) {
        global $DB;
        if (is_object($param)) {
            $event = new local_evaluationcalendar_event($param);
        } else {
            $event = $DB->get_record('evaluationcalendar_event', array('id' => (int) $param), '*', MUST_EXIST);
            $event = new local_evaluationcalendar_event($event);
        }
        return $event;
    }

    /**
     * Fetch all event properties
     * This function returns all of the events properties as an object
     *
     * @return stdClass Object containing event properties
     */
    public function properties() {
        return clone($this->properties);
    }

    /**
     * Retrieves and returns all local_evaluationcalendar_event
     *
     * @return local_evaluationcalendar_event[]
     */
    public static function read_all() {
        global $DB;

        $events = $DB->get_records('evaluationcalendar_event');
        foreach ($events as $key => $event) {
            $events[$key] = new local_evaluationcalendar_event($event);
        }
        return $events;
    }

    /**
     * Returns an array of local_evaluationcalendar_event objects when provided with a existing evaluation id
     *
     * @param string $param evaluation id
     * @return local_evaluationcalendar_event[]|false status for loading local_evaluationcalendar_event
     */
    public static function read_from_evaluation_id($param) {
        global $DB;
        $events = $DB->get_records('evaluationcalendar_event', array('evaluationid' => $param));
        foreach ($events as $key => $event) {
            $events[$key] = new local_evaluationcalendar_event($event);
        }
        return $events;
    }

    /**
     * Deletes events inserted during development stage.
     * Also deletes the related calendar events
     *
     * @see self::delete()
     * @return bool succession of deleting event
     */
    public static function delete_development_events() {
        global $DB;

        $records = $DB->get_records('evaluationcalendar_event', array('development' => 1), null, 'eventid');
        if (count($records) == 0) {
            return true;
        }

        $DB->delete_records('evaluationcalendar_event', array('development' => 1));

        foreach ($records as $record) {
            $calendar_event = calendar_event::load((int) $record->eventid);
            $calendar_event->delete();
        }
        return true;
    }

    /**
     * Deletes all events.
     * Also deletes the related calendar events
     *
     * @see self::delete()
     * @return bool succession of deleting event
     */
    public static function delete_all_events() {
        global $DB;

        $records = $DB->get_records('evaluationcalendar_event', null, '', 'eventid');
        if (count($records) == 0) {
            return true;
        }

        $DB->delete_records('evaluationcalendar_event');

        foreach ($records as $record) {
            $calendar_event = calendar_event::load((int) $record->eventid);
            $calendar_event->delete();
        }
        return true;
    }

    /**
     * Properties get method
     * Attempts to call a get_$key method to return the property and falls over
     * to return the raw property
     *
     * @param string $key property name
     * @return mixed property value
     * @throws coding_exception
     */
    public function __get($key) {
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
     *
     * @param string $key   property name
     * @param mixed  $value value of the property
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_' . $key)) {
            $this->{'set_' . $key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * PHP needs an isset method if you use the properties get method and
     * still want empty calls to work
     *
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    public function __isset($key) {
        return !empty($this->properties->{$key});
    }

    /**
     * Deletes an local_evaluationcalendar_event, and if selected, deletes the associated calendar_event
     * This function deletes an event and the associated calendar_event if $deletecalendarevent=true.
     * This function makes use of MUST_EXIST to ensure the local_evaluationcalendar_event is valid, if not
     * it will result in an exception being thrown
     *
     * @see self::delete()
     * @param bool $deletecalendarevent delete calendar_event
     * @return bool succession of deleting event
     */
    public function delete($deletecalendarevent = false) {
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

/**
 * Manage the plugin schedules table
 * This class provides the required functionality in order to manage the local_evaluationcalendar_schedule.
 *
 * @category Class
 * @property int    $id                 The id within the schedule table
 * @property int    $courseid           The course id
 * @property int    $groupid            The group id
 * @property string $weeks              The yearly weeks (1,2,3,14,15,16,20,21,22)
 * @property string $weekday            The number of the week day (1 = Sunday, 7 = Saturday)
 * @property string $timestart          The time the schedule starts in 24h format (ex "15:30")
 */
class local_evaluationcalendar_schedule {

    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    protected $properties = null;

    /**
     * Instantiates a new local_evaluationcalendar_schedule and optionally populates its properties with the data provided
     *
     * @param stdClass $data Optional. An object containing the properties to for an event
     */
    public function __construct($data = null) {
        // First convert to object if it is not already (should either be object or assoc array)
        if (!is_object($data)) {
            $data = (object) $data;
        }

        if (empty($data->id)) {
            $data->id = null;
        }
        if (empty($data->courseid)) {
            $data->courseid = 0;
        }
        if (empty($data->groupid)) {
            $data->groupid = 0;
        }
        if (empty($data->weeks)) {
            $data->weeks = "";
        }
        if (empty($data->weekday)) {
            $data->weekday = "1";
        }
        if (empty($data->timestart)) {
            $data->timestart = "00:00";
        }
        $this->properties = $data;
    }

    /**
     * Calls moodle db insert_records function to insert a bulk of records
     *
     * @param $records array
     */
    public static function insert_records($records) {
        global $DB;
        $DB->insert_records('evaluationcalendar_schedule', $records);
    }

    /**
     * Read all evaluationcalendar_schedule from the database
     *
     * @return local_evaluationcalendar_schedule[]
     */
    public static function read_all() {
        global $DB;
        $records = $DB->get_records('evaluationcalendar_schedule');
        foreach ($records as $key => $record) {
            $records[$key] = new local_evaluationcalendar_schedule($record);
        }
        return $records;
    }

    /**
     * Deletes all evaluationcalendar_schedule from the database
     *
     * @return bool
     */
    public static function delete_all() {
        global $DB;
        return $DB->delete_records('evaluationcalendar_schedule');
    }

    /**
     * Properties get method
     * Attempts to call a get_$key method to return the property and falls over
     * to return the raw property
     *
     * @param string $key property name
     * @return mixed property value
     * @throws coding_exception
     */
    public function __get($key) {
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
     *
     * @param string $key   property name
     * @param mixed  $value value of the property
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_' . $key)) {
            $this->{'set_' . $key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * PHP needs an isset method if you use the properties get method and
     * still want empty calls to work
     *
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    public function __isset($key) {
        return !empty($this->properties->{$key});
    }
}

/**
 * Manage the plugin reports table
 * This class provides the required functionality in order to manage the local_evaluationcalendar_report.
 *
 * @category Class
 * @property int    $id                            The id within the reports table
 * @property string $task                          Task that created the report
 * @property int    $inserts                       Count of inserted elements
 * @property int    $cleaned                       Count of cleaned elements
 * @property int    $updates                       Count of updated elements
 * @property int    $errors                        Count of errors elements
 * @property int    $deleted                       Count of deleted elements
 * @property int    $timecreated                   The time the report was created
 * @property array  $logs                          The list of all logs
 */
class local_evaluationcalendar_report {

    /** @var array An object containing the event properties can be accessed via the __get/set methods */
    protected $properties = null;

    /**
     * Instantiates a new local_evaluationcalendar_report and optionally populates its properties with the data provided
     *
     * @param stdClass $data Optional. An object containing the properties to for an event
     */
    public function __construct($data = null) {
        // First convert to object if it is not already (should either be object or assoc array)
        if (!is_object($data)) {
            $data = (object) $data;
        }
        if (!isset($data->id)) {
            $data->id = 0;
        }
        if (!isset($data->task)) {
            $data->task = "";
        }
        if (!isset($data->inserts)) {
            $data->inserts = 0;
        }
        if (!isset($data->cleaned)) {
            $data->cleaned = 0;
        }
        if (!isset($data->updates)) {
            $data->updated = 0;
        }
        if (!isset($data->errors)) {
            $data->errors = 0;
        }
        if (!isset($data->deleted)) {
            $data->deleted = 0;
        }
        if (!isset($data->logs)) {
            $data->logs = array();
        } elseif (is_string($data->logs)) {
            $data->logs = json_decode($data->logs);
        }
        $this->properties = $data;
    }

    /**
     * Read all evaluationcalendar_report from the database
     *
     * @return local_evaluationcalendar_report[]
     */
    public static function read_all() {
        global $DB;
        $records = $DB->get_records('evaluationcalendar_report');
        foreach ($records as $key => $record) {
            $records[$key] = new local_evaluationcalendar_report($record);
        }
        return $records;
    }

    /**
     * Inserts the new evaluationcalendar_report and removes the oldest if more than 50
     *
     * @param $properties
     * @return bool|local_evaluationcalendar_report
     * @throws coding_exception
     */
    public static function create($properties) {
        global $DB, $CFG;
        if (is_array($properties)) {
            $properties = (object) $properties;
        }
        if (!is_object($properties)) {
            throw new coding_exception('When creating a report, properties should be either an object or an assoc array');
        }
        $report = new local_evaluationcalendar_report($properties);
        $report->timecreated = (new DateTime())->getTimestamp();

        // encode logs to json
        $insert_object = $report->properties;
        $insert_object->logs = json_encode($insert_object->logs);
        $report->id = $DB->insert_record('evaluationcalendar_report', $insert_object);

        // remove excess of reports
        $count = $DB->count_records('evaluationcalendar_report') - 50;
        for ($i = 0; $i < $count; $i++) {
            $oldest_id = $DB->get_field_sql('SELECT MIN(id) FROM ' . $CFG->prefix . 'evaluationcalendar_report');
            $DB->delete_records('evaluationcalendar_report', array("id" => $oldest_id));
        }
        return $report;
    }

    /**
     * Deletes all evaluationcalendar_report from the database
     *
     * @return bool
     */
    public static function delete_all() {
        global $DB;
        return $DB->delete_records('evaluationcalendar_report');
    }

    /**
     * Properties get method
     * Attempts to call a get_$key method to return the property and falls over
     * to return the raw property
     *
     * @param string $key property name
     * @return mixed property value
     * @throws coding_exception
     */
    public function __get($key) {
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
     *
     * @param string $key   property name
     * @param mixed  $value value of the property
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_' . $key)) {
            $this->{'set_' . $key}($value);
        }
        $this->properties->{$key} = $value;
    }

    /**
     * PHP needs an isset method if you use the properties get method and
     * still want empty calls to work
     *
     * @param string $key $key property name
     * @return bool|mixed property value, false if property is not exist
     */
    public function __isset($key) {
        return !empty($this->properties->{$key});
    }
}

