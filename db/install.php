<?php


function xmldb_filter_screencast_install() {
    global $CFG;

    filter_set_global_state('filter/screencast', TEXTFILTER_ON);
}

