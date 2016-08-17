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

global $CFG, $OUTPUT;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/local/pfc/lib.php');


// Gets query data
$requestType = optional_param('requesttype', '', PARAM_TEXT);
$synchronize = optional_param('synchronize', '', PARAM_TEXT);
$pageParams = array();
if ($requestType) {
    $pageParams['requesttype'] = $requestType;
}
if ($synchronize) {
    $pageParams['synchronize'] = $synchronize;
}

// Initialize admin page
admin_externalpage_setup('local_pfc', '', $pageParams);

// Prepares the form and checks if given data is valid
$check_api_form = new local_pfc_check_api_form(new moodle_url('/local/pfc/'));
$check_api_form->set_data((object) $pageParams);
if ($data = $check_api_form->get_data()) {
    redirect(new moodle_url('/local/pfc/', $pageParams));
}

$synchronize_calendars_form = new local_pfc_synchronize_calendars_form(new moodle_url('/local/pfc/'));
$synchronize_calendars_form->set_data((object) $pageParams);
if ($data = $synchronize_calendars_form->get_data()) {
    redirect(new moodle_url('/local/pfc/', $pageParams));
}

// Print page
echo $OUTPUT->header();
$check_api_form->display();
$synchronize_calendars_form->display();
if($requestType || $synchronize){
    $pfc = new local_pfc(true);
    if($requestType){
        echo $pfc->check_api_interface($requestType);
    }
    if($synchronize){
        echo $pfc->synchronize_evaluation_calendars();
    }
}
echo $OUTPUT->footer();

