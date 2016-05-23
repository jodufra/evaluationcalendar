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
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author Duarte Mateus <2120189@my.ipleiria.pt>
 * @author Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot.'/local/pfc/lib.php');

$requestType = optional_param('requesttype', '', PARAM_TEXT);

$pageParams = array();
if ($requestType) {
    $pageParams['requesttype'] = $requestType;
}
admin_externalpage_setup('local_pfc', '', $pageParams);


$mform = new local_pfc_form(new moodle_url('/local/pfc/'));
$mform->set_data((object)$pageParams);
if ($data = $mform->get_data()) {
    redirect(new moodle_url('/local/pfc/', $pageParams));
}

echo $OUTPUT->header();
$mform->display();
if($requestType){
    echo local_pfc_make_api_request_to_html($requestType);
}
echo $OUTPUT->footer();

