<?php
class SchoolCustomCalendarEventDetails extends DbObject {
    public $custom_calendar_id;
    public $event_name;  // String 
    public $event_description; // String - text area
    public $timezone;
    public $status; // select box
    public $is_repeat; // checkbox
    public $repeat_interval; // select box

}