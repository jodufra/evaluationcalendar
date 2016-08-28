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

// Full name of the plugin (used for diagnostics).
$plugin->component = 'local_evaluationcalendar';
// Declares the maturity level of this plugin version (MATURITY_ALPHA, MATURITY_BETA, MATURITY_RC or MATURITY_STABLE)
$plugin->maturity = MATURITY_BETA;
// Human readable version name that should help to identify each release of the plugin
$plugin->release = 'v0.5-b0';
// Requires this Moodle version.
$plugin->requires = 2014051200;
// The current module version (Date: YYYYMMDDXX).
$plugin->version = 2016082800;