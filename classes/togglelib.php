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
 * Collapsed Topics Information
 *
 * A topic based format that solves the issue of the 'Scroll of Death' when a course has many topics. All topics
 * except zero have a toggle that displays that topic. One or more topics can be displayed at any given time.
 * Toggles are persistent on a per browser session per course basis but can be made to persist longer by a small
 * code change. Full installation instructions, code adaptions and credits are included in the 'Readme.txt' file.
 *
 * @package    format_topcoll
 * @copyright  &copy; 2012-onwards G J Barnard in respect to modifications of standard topics format.
 * @author     G J Barnard - {@link https://moodle.org/user/profile.php?id=442195}
 * @link       https://docs.moodle.org/en/Collapsed_Topics_course_format
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace format_topcoll;

/**
 * Togglelib.
 */
class togglelib {
    /** @var string TOPCOLL_TOGGLE */
    const TOPCOLL_TOGGLE = 'topcoll_toggle';

    // Digits used = ":;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxy";
    // Note: An ':' is 58 Ascii so to go between six digit base 2 and this then add / subtract 58.
    // This has been chosen to avoid digits which are in the old method.

    /** @var int TOGGLE_6 */
    const TOGGLE_6 = 1;
    /** @var int TOGGLE_5 */
    const TOGGLE_5 = 2;
    /** @var int TOGGLE_4 */
    const TOGGLE_4 = 4;
    /** @var int TOGGLE_3 */
    const TOGGLE_3 = 8;
    /** @var int TOGGLE_2 */
    const TOGGLE_2 = 16;
    /** @var int TOGGLE_1 */
    const TOGGLE_1 = 32;

    /** @var string $toggles Toggles state */
    private $toggles;

    /**
     * Tells us the toggle state from the DB.
     * string $toggles - Toggles state.
     * returns nothing.
     */
    public function set_toggles($toggles) {
        $this->toggles = $toggles;
    }

    /**
     * Gets toggle state stored here.
     * returns $toggles - Toggles state.
     */
    public function get_toggles() {
        return $this->toggles;
    }

    // Note: http://php.net/manual/en/language.operators.bitwise.php very useful.
    /**
     * Gets the state of the requested Toggle number.
     * int $togglenum - The toggle number.
     * returns boolean.
     */
    public function get_toggle_state($togglenum) {
        $togglecharpos = self::get_toggle_pos($togglenum);
        $toggleflag = self::get_toggle_flag($togglenum, $togglecharpos);
        return ((self::decode_character_to_value($this->toggles[$togglecharpos - 1]) & $toggleflag) == $toggleflag);
    }

    /**
     * Sets the state of the requested Toggle number.
     * int $togglenum - The toggle number.
     * boolean $state - true or false.
     */
    public function set_toggle_state($togglenum, $state) {
        $togglecharpos = self::get_toggle_pos($togglenum);
        $toggleflag = self::get_toggle_flag($togglenum, $togglecharpos);
        $value = self::decode_character_to_value($this->toggles[$togglecharpos - 1]);
        if ($state == true) {
            $value |= $toggleflag;
        } else {
            $value &= ~$toggleflag;
        }
        $this->toggles[$togglecharpos - 1] = self::encode_value_to_character($value);
    }

    /**
     * Gets the string binary representation of the given toggle state.
     * string $toggles - Toggles state.
     * returns string.
     */
    public function decode_toggle_state($toggles) {
        $togglestate = '';
        $strlen = strlen($toggles);
        for ($chars = 0; $chars < $strlen; $chars++) {
            $charval = self::decode_character_to_value($toggles[$chars]);
            $togglestate .= (($charval & self::TOGGLE_1) == self::TOGGLE_1) ? '1' : '0';
            $togglestate .= (($charval & self::TOGGLE_2) == self::TOGGLE_2) ? '1' : '0';
            $togglestate .= (($charval & self::TOGGLE_3) == self::TOGGLE_3) ? '1' : '0';
            $togglestate .= (($charval & self::TOGGLE_4) == self::TOGGLE_4) ? '1' : '0';
            $togglestate .= (($charval & self::TOGGLE_5) == self::TOGGLE_5) ? '1' : '0';
            $togglestate .= (($charval & self::TOGGLE_6) == self::TOGGLE_6) ? '1' : '0';
        }
        return $togglestate;
    }

    /**
     * Tells us if the toggle state from the DB is an old one.
     * string $pref - Toggle state.
     * returns boolean old preference = true and not old preference = false.
     */
    public function is_old_preference($pref) {
        $retr = false;
        $firstchar = $pref[0];

        if (($firstchar == '0') || ($firstchar == '1')) {
            $retr = true;
        }

        return $retr;
    }

    /**
     * Tells us the number of digits we need to store the state for the number of toggles we have.
     * int $numtoggles - Number of toggles.
     * returns int - Number of digits required.
     */
    public function get_required_digits($numtoggles) {
        return self::get_toggle_pos($numtoggles);
    }

    /**
     * Tells us the minimum character used.
     * returns char - Digit positionS.
     */
    public function get_min_digit() {
        return ':'; // 58 ':'.
    }

    /**
     * Tells us the maximum character used.
     * returns char - Digit character.
     */
    public function get_max_digit() {
        return  'y'; // 58 'y'.
    }

    /**
     * Tells us digit postion for the toggle number.
     * int $togglenum - Toggle number.
     * returns int - Digit character.
     */
    private static function get_toggle_pos($togglenum) {
        return intval(ceil($togglenum / 6));
    }

    /**
     * Tells us the position of the bit within the digit for the given toggle number and digit position in the toggles state.
     * int $togglenum - Toggle number.
     * int $togglecharpos - Digit character position.
     * returns int - Digit flag.
     */
    private static function get_toggle_flag($togglenum, $togglecharpos) {
        $toggleflagpos = $togglenum - (($togglecharpos - 1) * 6);
        switch ($toggleflagpos) {
            case 1:
                $flag = self::TOGGLE_1;
                break;
            case 2:
                $flag = self::TOGGLE_2;
                break;
            case 3:
                $flag = self::TOGGLE_3;
                break;
            case 4:
                $flag = self::TOGGLE_4;
                break;
            case 5:
                $flag = self::TOGGLE_5;
                break;
            case 6:
                $flag = self::TOGGLE_6;
                break;
        }
        return $flag;
    }

    /**
     * Converts a character to a value so that its toggle state 'bit's can be read / set.
     * char $char - Digit.
     * returns int - Character value.
     */
    private static function decode_character_to_value($char) {
        return ord($char) - 58;
    }

    /**
     * Converts a value to a digit so that can be stored in the DB.
     * int $val - Value.
     * returns char - Digit.
     */
    private static function encode_value_to_character($val) {
        return chr($val + 58);
    }

    /**
     * Returns test result as HTML.
     */
    public function test() {
        $retr = '<h1>A=' . self::decode_character_to_value('A') . ' - back:' .
            self::encode_value_to_character(7) . '</h1><br /><p>';
        for ($i = 0; $i < 64; $i++) {
            $curr = self::encode_value_to_character($i);
            $val = self::decode_character_to_value($curr);
            $back = self::encode_value_to_character($val);
            $retr .= $curr . '=' . $val . '=' . $back . ' ';
        }
        $retr .= '</p>';

        // Toggles:...
        $this->toggles = 'GjB'; // 001101 110000 001000 = 18 toggles.
        $retr .= '<p>Toggle string of GjB which is 001101 110000 001000 is:</p><p>';
        for ($j = 1; $j <= 18; $j++) {
            $retr .= 'TG: ' . $j . ' = ' . (int)$this->get_toggle_state($j) . ' - ';
        }
        $retr .= '</p>';
        $retr .= '<p>Now set 5, 12, 15 (already set) and 18 and clear 3 and 7 is:</p><p>';
        $this->set_toggle_state(5, true);
        $this->set_toggle_state(12, true);
        $this->set_toggle_state(15, true);
        $this->set_toggle_state(18, true);
        $this->set_toggle_state(3, false);
        $this->set_toggle_state(7, false);
        for ($j = 1; $j <= 18; $j++) {
            $retr .= 'TG: ' . $j . ' = ' . (int)$this->get_toggle_state($j) . ' - ';
        }
        $retr .= '</p>';

        return $retr;
    }

    /**
     * Returns a required_param() toggle value for the named user preference.
     *
     * @param string $parname the name of the user preference we want
     * @return mixed
     * @throws coding_exception
     */
    public static function required_topcoll_param($parname) {
        if (empty($parname)) {
            throw new coding_exception('required_topcoll_param() requires $parname to be specified');
        }
        $param = required_param($parname, PARAM_RAW);

        return self::clean_topcoll_param($param);
    }

    /**
     * Used by required_topcoll_param to clean the toggle parameter.
     *
     * @param string $param the variable we are cleaning
     * @return mixed
     * @throws coding_exception
     */
    public static function clean_topcoll_param($param) {
        if (is_array($param)) {
            throw new coding_exception('clean_topcoll_param() cannot process arrays.');
        } else if (is_object($param)) {
            if (method_exists($param, '__toString')) {
                $param = $param->__toString();
            } else {
                throw new coding_exception('clean_topcoll_param() cannot process objects.');
            }
        }

        $chars = strlen($param);
        for ($i = 0; $i < $chars; $i++) {
            $charval = ord($param[$i]);
            if (($charval < 58) || ($charval > 121)) {
                return false;
            }
        }
        return $param;
    }
}
