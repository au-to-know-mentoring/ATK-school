<?php

class SchoolCalendarEvent extends DbObject {

    public $custom_calendar_id;
    public $event_name;
    public $event_description;
    public $dt_start_time;
    public $dt_end_time;
    public $status;
    public $is_repeat;
    public $repeat_interval;

}