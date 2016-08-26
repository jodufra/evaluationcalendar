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

global $CFG, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->dirroot . '/local/evaluationcalendar/lib.php');

// Initialize admin page
admin_externalpage_setup('local_evaluationcalendar');

$moodle_url = new moodle_url('/local/evaluationcalendar/');
$section_form = new local_evaluationcalendar_section_form($moodle_url, optional_param('section', '', PARAM_PATH));
$section = $section_form->get_section();
$moodle_url->param('section', $section);

echo $OUTPUT->header();
$section_form->display();

switch ($section) {
    case 'information':
        echo 'todo';
        break;
    case 'synchronize':
        $synchronize_form_result = '';
        $synchronize_form = new local_evaluationcalendar_synchronize_form($moodle_url);
        if ($data = $synchronize_form->get_data()) {
            $evaluation_calendar = new local_evaluationcalendar(true);
            switch ($data->synchronize){
                case 'last_updated_evaluations':
                    $synchronize_form_result = $evaluation_calendar->synchronize_evaluation_calendars();
                    break;
                case 'all_evaluations':
                    $synchronize_form_result = $evaluation_calendar->synchronize_evaluation_calendars(true);
                    break;
                case 'schedules':
                    $synchronize_form_result = $evaluation_calendar->synchronize_schedules();
                    break;
            }
            // To show the report
            $synchronize_form->definition_after_data($synchronize_form_result);
        }
        $synchronize_form->display();
        break;
    case 'settings':
        $config_form_result = '';
        $config_form = new local_evaluationcalendar_config_form($moodle_url);
        $config_form->set_data(local_evaluationcalendar_config::Instance()->generate_form_data());
        if ($data = $config_form->get_data()) {
            $evaluation_calendar = new local_evaluationcalendar(true);
            if (!empty($data->restore_defaults)) {
                $config_form_result = $evaluation_calendar->restore_config_to_defaults();
            } else {
                $config_form_result = $evaluation_calendar->update_config($data);
            }
            // Since changes might been made we need to reload the form in order to display the correct information
            $config_form->definition_after_data($config_form_result);
        }
        $config_form->display();
        break;
    case 'reports':
        echo 'todo';
        break;
}
echo $OUTPUT->footer();

