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
 * @comment    Thank you to Luiggi Sansonetti (http://moodle.org/user/profile.php?id=1297063) for the translation.
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

// French Translation of Collapsed Topics Course Format
// Traduction française du Format du cours Collapsed Sujets

// Used by the Moodle Core for identifing the format and displaying in the list of formats for a course in its settings.
// Utilisée par le noyau de Moodle pour une indication des format et l'affichage dans la liste des formats pour un cours de ses paramètres.
$string['nametopcoll']='Sections réduites';
$string['formattopcoll']='Sections réduites';

// Used in format.php
// Employée au format.php
$string['topcolltoggle']='Basculer';
$string['topcolltogglewidth']='width: 34px;';

// Toggle all - Moodle Tracker CONTRIB-3190
$string['topcollall']='toutes les sections.';
$string['topcollopened']='Ouvrir';
$string['topcollclosed']='Fermer';

// Moodle 2.0 Enhancement - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
// Moodle 2.0 Amélioration - Moodle Tracker MDL-15252, MDL-21693 & MDL-22056 - http://docs.moodle.org/en/Development:Languages
$string['sectionname'] = 'Section';
$string['pluginname'] = 'Sections réduites';
$string['section0name'] = 'Général';

// MDL-26105
$string['page-course-view-topcoll'] = 'Toutes les pages du cours principal au format sections réduites';
$string['page-course-view-topcoll-x'] = 'Toutes les pages du cours au format sections réduites';

// Layout enhancement - Moodle Tracker CONTRIB-3378
$string['setlayout'] = 'Disposition';
$string['setlayout_default'] = 'Par défaut';
$string['setlayout_no_toggle_section_x'] = 'Pas de section x à basculer';
$string['setlayout_no_section_no'] = 'Pas de numéro de section';
$string['setlayout_no_toggle_section_x_section_no'] = 'Pas de section x à basculer ni de numéro de section';
$string['setlayout_no_toggle_word'] = 'Pas de mot à basculer';
$string['setlayout_no_toggle_word_toggle_section_x'] = 'Pas de mot ni de section x à basculer';
$string['setlayout_no_toggle_word_toggle_section_x_section_no'] = 'Pas de mot, pas de section x ni de numéro de section à basculer';
$string['setlayoutelements'] = 'Eléments';
$string['setlayoutstructure'] = 'Structure';
$string['setlayoutstructuretopic']='Section';
$string['setlayoutstructureweek']='Semaine';
$string['setlayoutstructurelatweekfirst']='Dernière semaine en premier';
$string['setlayoutstructurecurrenttopicfirst']='Section actuelle en premier';
// Help
$string['setlayoutelements_help']='Combien d\'informations sur les éléments et les sections souhaitez-vous afficher ?';
$string['setlayoutstructure_help']="Structure et disposition de la page.  Vous pouvez choisir entre :

'Format thématique' - ce format est organisé en sections thématiques numérotées.

'Format hebdomadaire' - le cours est organisé par semaine avec des dates de début et de fin.

'Dernière semaine en premier' - basé sur le 'format hebdomadaire', la semaine en cours est affichée en haut et les semaines précédentes dans un ordre descendant, sauf en mode édition où la structure revient au format initial hebdomadaire.

'Section actuelle en premier' - basé sur le 'format thématique', la secion actuelle est affichée en haut si elle a été fixée.";
?>