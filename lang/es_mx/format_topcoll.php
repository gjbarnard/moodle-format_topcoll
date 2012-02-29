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
 * @comment    A big thank you to Carlos Sánchez Martín (http://moodle.org/user/profile.php?id=743362) for the translation.
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
$string['nametopcoll']='Temas plegados';
$string['formattopcoll']='Temas plegados';

// Used in format.php
// Utilizado en format.php
$string['topcolltoggle']='Activar';
$string['topcolltogglewidth']='width: 32px;';

// Toggle all - Moodle Tracker CONTRIB-3190
$string['topcollall']='Cambiar';
$string['topcollopened']='Todos abiertos'; /*all open*/
$string['topcollclosed']='Todos cerrados'; /*all closed*/

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
// Moodle 2.0 Mejora - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
$string['sectionname'] = 'Tema';
$string['pluginname'] = 'Temas deplegables'; /*Topics that you can collapse*/
$string['section0name'] = 'General';

// MDL-26105
$string['page-course-view-topcoll'] = 'Cualquier página principal del curso en el formato temas desplegables';
$string['page-course-view-topcoll-x'] = 'Cualquier página del curso en el formato temas desplegables';

// Layout enhancenent - Moodle Tracker CONTRIB-3378
$string['setlayout'] = 'Establecer el formato';
$string['setlayout_default'] = 'Por defecto';
$string['setlayout_no_toggle_section_x'] = 'Sin tema x';
$string['setlayout_no_section_no'] = 'Sin número de sección';
$string['setlayout_no_toggle_section_x_section_no'] = 'Sin tema X ni número de sección';
$string['setlayout_no_toggle_word'] = 'Sin palabra toggle';
$string['setlayout_no_toggle_word_toggle_section_x'] = 'Sin toggle ni tema x';
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = 'Sin toggle, tema X ni número desección';
$string['setlayoutelements'] = 'Establecer el formato de los elementos';
$string['setlayoutstructure'] = 'Establecer el formato';
$string['setlayoutstructuretopic']='Temas';
$string['setlayoutstructureweek']='Semana';
$string['setlayoutstructurelatweekfirst']='Semana actual primero';
$string['setlayoutstructurecurrenttopicfirst']='Tema actual primero';
// Help
$string['setlayoutelements_help']='Cuánta información sobre las secciones / toggles quieres visualizar.';
$string['setlayoutstructure_help']="El formato o estructura del curso. Puedes elegir entre:

'Temas' - el cual presenta cada sección como un tema en orden.

'Semanas' - el cual presenta cada sección como una semana en orden ascendente.

'Semana actual primero' - el cual es igual que el formato 'Semanas' pero la semana actual ocupa el primer lugar, seguida de las semanas anteriores en orden descendente. Este comportamiento no se da mientras estás en editando el curso (edición activada), es decir, la estructura mientras editas el curso es la misma que 'Semanas'.

'Tema actual primero' - el cual es igual que el formato 'Temas', con la excepción de que el tema actual ocupa el primer lugar.";
?>