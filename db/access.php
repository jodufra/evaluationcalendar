<?php
// This file is part of a local Moodle plugin
//
// You can redistribute it and/or modify it under the terms of the
// GNU General Public License as published by the Free Software Foundation,
// either version 3 of the License, or (at your option) any later version.
//
// This plugin is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Capability definitions for this module.
 *
 * @package local_pfc
 * @copyright 2016, Duarte Mateus <2120189@my.ipleiria.pt>, Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$capabilities = array(
    'local/pfc:executesynchronization' => array(
        'riskbitmask'  => RISK_DATALOSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'student'        => CAP_PROHIBIT,
            'teacher'        => CAP_PROHIBIT,
            'editingteacher' => CAP_PROHIBIT,
            'manager'          => CAP_ALLOW
        )
    ),
);
