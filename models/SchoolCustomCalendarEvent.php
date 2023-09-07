<?php
class SchoolCustomCalendarEvent extends DbObject {
    public $custom_calendar_id;
    public $event_name;  // String 
    public $event_description; // String - text area
    public $timezone;
    public $dt_start_time; // Date picker Time field
    public $dt_end_time; // Date picker Time field
    public $status; // select box
    public $is_repeat; // checkbox
    public $repeat_interval; // select box

    // ADD TIMEZONE LIKE IN ManagerSettings
}