<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * General lib.php for the apocalypsie plugin.
 *
 * @package    local_apocalypsie
 * @copyright  2020 Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Moodle native lib/navigationlib.php calls this hook allowing us to override UI.
 * Here we instruct Moodle website to issue custom HTTP response header Content-Security-Policy-Report-Only on every page.
 */
function local_apocalypsie_before_http_headers() {
    global $CFG;
    $config = get_config('local_apocalypsie');
    if (preg_match('~MSIE|Internet Explorer~i', $_SERVER['HTTP_USER_AGENT']) || preg_match('~Trident/7.0(; Touch)?; rv:11.0~',$_SERVER['HTTP_USER_AGENT'])) {
        if (!isguestuser()) {
            // Record daily user access.
            $lastaccess = get_user_preferences('local_apocalypsie_access');

            // Record last daily access.
            if (empty($lastaccess) || $lastaccess < time() - DAYSECS) {
                set_user_preference('local_apocalypsie_access', time());
            }
        }
        // Drop a warning into site header.
        if (!empty($config->enableheader) && !empty($config->header)) {
            $CFG->additionalhtmltopofbody = html_writer::div($config->header, 'local_apocalypsie_header').
                $CFG->additionalhtmltopofbody;
        }
    }
}
