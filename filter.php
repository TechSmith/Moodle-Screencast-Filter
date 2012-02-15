<?php
// This file is part of Moodle-Screencast-Filter
//
// Moodle-Screencast-Filter is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle-Screencast-Filter is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle-Screencast-Filter.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Filter for component 'filter_screencast'
 *
 * @package   filter_screencast
 * @copyright 2012 Mark Schall, TechSmith Corporation (link: http://techsmith.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/filelib.php');

class filter_screencast extends moodle_text_filter {

    function filter($text, array $options = array()) {
        global $CFG;
		
        if (!is_string($text) or empty($text)) {
            // non string data can not be filtered anyway
            return $text;
        }
        if (stripos($text, '</a>') === false) {
            // performance shortcut - all regexes bellow end with the </a> tag,
            // if not present nothing can match
            return $text;
        }

        $newtext = $text; // we need to return the original value if regex fails!

		//http://www.screencast.com/t/kCUZQnqP8fV
		$search = '/<a\s[^>]*href="((https?:\/\/(www\.)?screencast\.com)\/t\/(.*?))"(.*?)>(.*?)<\/a>/is';
        $newtext = preg_replace_callback($search, 'filter_screencast_callback', $newtext);

        if (empty($newtext) or $newtext === $text) {
            // error or not filtered
            unset($newtext);
            return $text;
        }

        return $newtext;
    }
}

function filter_screencast_callback($link) {
    global $CFG;

    $url = $link[1];
	
	$screencastContents = file_get_contents( $url );
		
    preg_match( '/(<iframe(.*?)>(.*?)<\/iframe>)/ms', $screencastContents, $output );

    return $output[1];
}
