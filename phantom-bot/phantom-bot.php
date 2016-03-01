<?php
/*
Plugin Name: Phantombot Points
Description: Insert Phantom Bot Point Ranking to your Site
Version: 1.0.0
Author: Hans Böse
Author URI:
License: GPL v2

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

define('PHANTOM_BOT_BASE', plugin_dir_path(__FILE__));
define('PHANTOM_BOT_VER', '1.0.0');
define('PHANTOM_BOT_URL', plugins_url('/' . basename(dirname(__FILE__))));

include_once(PHANTOM_BOT_BASE . 'includes/PhantomBotConnector.class.php');
include_once(PHANTOM_BOT_BASE . 'includes/phantom-bot-options.php');


function load_css() {
    wp_enqueue_style('phantom-bot-style',  PHANTOM_BOT_URL .'/css/phantom-bot-style.css', array(), PHANTOM_BOT_VER);
}
add_action('wp_enqueue_scripts','load_css');

function phantom_bot_connector()
{
    return new PhantomBotConnector(get_option('phantom_bot_ip'), get_option('phantom_bot_oauth'));
}

function remove_banned_users($table)
{
    $users = get_option('phantom_bot_banned');
    $user_array = explode(',', $users);

    foreach ($user_array as $user => $name) {
        unset($table[$name]);
    }

    return $table;
}

function phantom_bot_points($value)
{
    extract(shortcode_atts(array(
        'value' => 'value'
    ), $value));

    $full_data = phantom_bot_connector()->getTable('points');
    $points_data = $full_data[0];
    natsort($points_data);
    $sorted_points = array_reverse($points_data);
    $filtered_points = remove_banned_users($sorted_points);
    $output = array_slice($filtered_points, 0, $value);

    ob_start();
    echo '
    <table>
        <thead>
        <tr>
            <th width="20%">Platz</th>
            <th width="50%">User</th>
            <th width="30%">Punkte</th>
        </tr>
        </thead>
        <tbody>';

    $i = 1;
    foreach ($output as $user => $points) {
        echo '
        <tr>
            <td>' . $i . '</td>
            <td>' . $user . '</td>
            <td>' . $points . '</td>
        </tr>';
        $i++;
    }

    echo '
        </tbody>
    </table>';

    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}
add_shortcode('phantom_bot_points', 'phantom_bot_points');


function phantom_bot_time($value)
{
    extract(shortcode_atts(array(
        'value' => 'value'
    ), $value));

    $full_data = phantom_bot_connector()->getTable('time');
    $time_data = $full_data[0];
    natsort($time_data);
    $sorted_time = array_reverse($time_data);
    $filtered_time = remove_banned_users($sorted_time);
    $output = array_slice($filtered_time, 0, $value);

    ob_start();
    echo '
    <table>
        <thead>
        <tr>
            <th width="20%">Platz</th>
            <th width="50%">User</th>
            <th width="30%">Zeit</th>
        </tr>
        </thead>
        <tbody>';

    $i = 1;
    foreach ($output as $user => $time) {
        echo '
        <tr>
            <td>' . $i . '</td>
            <td>' . $user . '</td>
            <td>' . sec_to_time((int)$time) . '</td>
        </tr>';
        $i++;
    }

    echo '
        </tbody>
    </table>';

    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}
add_shortcode('phantom_bot_time', 'phantom_bot_time');


function sec_to_time($sekunden) {
    if (!($sekunden >= 60)) {
        return $sekunden . ' sek';
    }

    $minuten    = bcdiv($sekunden, '60', 0);
    $sekunden   = bcmod($sekunden, '60');

    if (!($minuten >= 60)) {
        return $minuten . ' min ' . $sekunden . ' sek';
    }

    $stunden    = bcdiv($minuten, '60', 0);
    $minuten    = bcmod($minuten, '60');

    if (!($stunden >= 24)) {
        return $stunden . ' std ' . $minuten . ' min ' . $sekunden . ' sek';
    }

    $tage       = bcdiv($stunden, '24', 0);
    $stunden    = bcmod($stunden, '24');

    return $tage . ' t ' . $stunden . ' std ' . $minuten . ' min ' . $sekunden . ' sek';
}