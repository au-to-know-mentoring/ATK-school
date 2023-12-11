<?php 
class SchoolCustomCalendarEventInstance extends DbObject{
    public $event_details_id;
    public $custom_calendar_id;
    public $event_name;
    public $event_description;
    public $timezone;
    public $status;
    public $dt_start_time;
    public $dt_end_time;
}