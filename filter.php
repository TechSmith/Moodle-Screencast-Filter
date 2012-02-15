<?php

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
