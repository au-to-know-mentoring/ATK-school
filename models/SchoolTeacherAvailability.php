<?php

class SchoolTeacherAvailability extends DbObject {

    public $object_type; //either teacher or student
    public $object_id;
    public $dt_start_time;
    public $dt_end_time;
    public $type;

    public function getStartTime() {
        //return formatDate($this->dt_class_date, 'H:i');
        if (!empty($this->dt_start_time)) {
            return date('H:i', $this->dt_start_time);
        }
        return null;
    }

    public function getEndTime() {
        //return formatDate($this->dt_class_date, 'H:i');
        if (!empty($this->dt_end_time)) {
            return date('H:i', $this->dt_end_time);
        }
        return null;
    }

    public function getDay() {
        //return formatDate($this->dt_class_date, 'H:i');
        if (!empty($this->dt_start_time)) {
            return date('l', $this->dt_start_time);
        }
        return null;
    }

    public function getStartForCurrentWeek($dateArray) {

        $weekdayNumber = date('w', $this->dt_start_time);
        $daysDifference = date('w', $this->dt_start_time) - date('w', strtotime($dateArray['start']));
        //$availDate = date('Y-m-d', strtotime($dateArray['start'] . " + " . $daysDifference . " days"));
        $availDate = date('Y-m-d', strtotime($dateArray['start'] . " + 12 hours") + ($daysDifference * (60 * 60 * 24)));
        return $availDate . " " . date('H:i', $this->dt_start_time);
        //var_dump(date('l', $this->dt_start_time)); die;
        //date('Y-m-d 00:00:00',strtotime($dateArray['end']))
        // $currentweekday = strtotime('this ' . date('l', $this->dt_start_time));
        // if (date('d', $currentweekday) > date('d',strtotime($dateArray['end']))) {
        //     $currentweekday = strtotime('last ' . date('l', $this->dt_start_time));
        // }
        // return date('Y-m-d ', $currentweekday) . date('H:i', $this->dt_start_time);
    }

    public function getEndForCurrentWeek($dateArray) {

        $weekdayNumber = date('w', $this->dt_start_time);
        $daysDifference = date('w', $this->dt_start_time) - date('w', strtotime($dateArray['start']));
        //$availDate = date('Y-m-d', strtotime($dateArray['start'] . " + " . $daysDifference . " days"));
        $availDate = date('Y-m-d', strtotime($dateArray['start'] . " + 12 hours") + ($daysDifference * (60 * 60 * 24)));
        return $availDate . " " . date('H:i', $this->dt_end_time);

        //var_dump(date('l', $this->dt_start_time)); die;
        // $currentweekday = strtotime('this ' . date('l', $this->dt_start_time));
        // $currentweekday = strtotime('this ' . date('l', $this->dt_start_time));
        // if (date('d', $currentweekday) > date('d',strtotime($dateArray['end']))) {
        //     $currentweekday = strtotime('last ' . date('l', $this->dt_start_time));
        // }
        // return date('Y-m-d ', $currentweekday) . date('H:i', $this->dt_end_time);
    }
}