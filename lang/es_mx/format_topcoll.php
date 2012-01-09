<?php
/**
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    course/format
 * @subpackage topcoll
 * @version    See the value of '$plugin->version' in version.php.
 * @copyright  &copy; 2009-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @link       http://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Spanish Mexican Translation of Collapsed Topics Course Format
// Traducción en español de formato plegado Curso de Temas

// Used by the Moodle Core for identifing the format and displaying in the list of formats for a course in its settings.
// Utilizado por el Núcleo de Moodle identificando el formato y la visualización en la lista de formatos para un curso en su configuración.
$string['nametopcoll']='Se derrumbo Temas';
$string['formattopcoll']='Se derrumbo Temas';

// Used in format.php
// Utilizado en format.php
$string['topcolltoggle']='Activar';
$string['topcolltogglewidth']='width: 26px;';

// Toggle all - Moodle Tracker CONTRIB-3190
$string['topcollall']='cambia.';
$string['topcollopened']='Abierto todo';
$string['topcollclosed']='Cierre todas las';

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
// Moodle 2.0 Mejora - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
$string['sectionname'] = 'Tema';
$string['pluginname'] = 'Se derrumbo Temas';
$string['section0name'] = 'General';
?>