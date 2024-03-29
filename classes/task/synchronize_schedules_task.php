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
 * @package   local_evaluationcalendar\task
 * @copyright 2016 Instituto Politécnico de Leiria <http://www.ipleiria.pt>
 * @author    Duarte Mateus <2120189@my.ipleiria.pt>
 * @author    Joel Francisco <2121000@my.ipleiria.pt>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_evaluationcalendar\task;

/**
 * Class synchronize_evaluation_calendars
 *
 * @category Class
 * @package  local_evaluationcalendar\task
 */
class synchronize_schedules_task extends \core\task\scheduled_task {
    /**
     * @return string
     * @throws \coding_exception
     */
    public function get_name() {
        // Shown in admin screens
        return get_string('synchronize_schedules', 'local_evaluationcalendar');
    }

    /**
     *
     */
    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/local/evaluationcalendar/lib.php');

        $evaluation_calendar = new \local_evaluationcalendar();
        $evaluation_calendar->synchronize_schedules();
    }
}