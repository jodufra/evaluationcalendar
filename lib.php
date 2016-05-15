<?php

/**
 * Library of functions and constants for local module pfc
 *
 * @package local_pfc
 * @copyright 2016, Duarte Mateus <2120189@my.ipleiria.pt>, Joel Francisco <2121000@my.ipleiria.pt>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the information if the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function pfc_supports($feature) {
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:
            // return true if Moodle 2 backup/restore system is implemented.
            return true;
        default:
            return null;
    }
}