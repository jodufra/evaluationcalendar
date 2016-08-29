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

$string['pluginname'] = 'Evaluations calendar';
$string['synchronize'] = 'Synchronize';
$string['settings'] = 'Settings';
$string['reports'] = 'Reports';
$string['is_required'] = '{$a} is required';

// Synchronize
$string['synchronize_info'] = '<h2>Manual synchronization.</h2>';
$string['synchronize_help'] = '<ul>' .
        '<li><b>Last updated evaluations</b>: Only synchronizes evaluations updated or created after the last synchronization.</li>' .
        '<li><b>All evaluations</b>: Synchronizes all evaluations. Be wary that a full synchronization might take a while.</li>' .
        '<li><b>Schedules</b>: Synchronizes and maps the subject schedules to moodle course groups.</li>' .
        '</ul>' .
        '<p>The options <b>Last updated evaluations</b> and <b>All evaluations</b> only synchronize evaluations from published ' .
        'calendars if the <b>development mode</b> setting is disabled.</p>';
$string['synchronize_schedules'] = 'Synchronize Schedules';
$string['synchronize_last_updated_evaluations'] = 'Synchronize Last Updated Evaluations';
$string['synchronized_nothing'] = 'Nothing to Synchronize';
$string['synchronized_schedules'] = 'Schedules Synchronized';
$string['synchronized_evaluations'] = 'Evaluations Synchronized';
$string['synchronized_all_evaluations'] = 'All Evaluations Synchronized';
//
$string['schedules'] = 'Schedules';
$string['last_updated_evaluations'] = 'Last updated evaluations';
$string['all_evaluations'] = 'All evaluations';
$string['inserts'] = 'Inserts';
$string['cleaned'] = 'Cleaned';
$string['updates'] = 'Updates';
$string['errors'] = 'Errors';
$string['deleted'] = 'Deleted';
$string['submit'] = 'Submit';

// Settings
$string['config_info'] = '<h2>Change or restore this plugin configuration.</h2>';
$string['config_defaults_restored'] = 'The configuration values were restored to it\'s defaults';
$string['config_changes_saved'] = 'The configuration changes were saved';
$string['config_error_saving'] = 'Error saving configuration changes';
//
$string['api_authorization_header_key'] = 'API Authorization Key';
$string['api_authorization_header_key_help'] = '';
$string['api_authorization_header_value'] = 'API Authorization Value';
$string['api_authorization_header_value_help'] = '';
$string['api_host'] = 'API Host';
$string['api_host_help'] = '';
$string['api_paths'] = 'API Url Paths';
$string['api_paths_help'] = '';
$string['calendars'] = 'Calendars';
$string['calendars_help'] = '';
$string['evaluations'] = 'Evaluations';
$string['evaluations_help'] = '';
$string['evaluations_ucs'] = 'UC Evaluations';
$string['evaluations_ucs_help'] = '';
$string['evaluation_types'] = 'Evaluation types';
$string['evaluation_types_help'] = '';
$string['evaluation_type'] = 'Evaluation type';
$string['evaluation_type_help'] = '';
$string['schedule_csv_url'] = 'Schedule CSV URL';
$string['schedule_csv_url_help'] = '';
$string['schedule_csv_dirty_src'] = 'Schedule CSV Dirty Source';
$string['schedule_csv_dirty_src_help'] = '';
$string['schedule_csv_delimiter'] = 'Schedule CSV Delimiter';
$string['schedule_csv_delimiter_help'] = '';
$string['schedule_csv_encoding'] = 'Schedule CSV Encoding';
$string['schedule_csv_encoding_help'] = '';
$string['school_year'] = 'School Year';
$string['school_year_help'] = '';
$string['development_mode'] = 'Development Mode';
$string['development_mode_help'] = '';
$string['restore_defaults'] = 'Restore Defaults';
$string['save'] = 'Save';
