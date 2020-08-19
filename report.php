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
 * Report users accessing via IE.
 *
 * @package    local_apocalypsie
 * @copyright  2020 Dan Marsden
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$download = optional_param('download', '', PARAM_ALPHA);

admin_externalpage_setup('local_apocalypsie_report', '', null, '', array('pagelayout' => 'report'));

$PAGE->set_pagelayout('admin');

$table = new \local_apocalypsie\output\report_table('apocalypsie');
$table->is_downloading($download, 'apocalypsie');
$columns  = ['fullname', 'lastaccess'];
$headers = [get_string('name'), get_string('lastaccess')];
$table->define_columns($columns);
$table->define_headers($headers);
if (!$table->is_downloading()) {
    // Only print headers if not asked to download data
    echo $OUTPUT->header();
}

// Work out the sql for the table.
$fields = "u.id,up.value,".get_all_user_name_fields(true, 'u');
$table->set_sql($fields, "{user} u JOIN {user_preferences} up ON up.userid = u.id", " up.name = 'local_apocalypsie_access'");

$table->define_baseurl($CFG->wwwroot.'/report/apocalypsie/report.php');

$table->out(40, true);

if (!$table->is_downloading()) {
    echo $OUTPUT->footer();
}