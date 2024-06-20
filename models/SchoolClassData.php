<?php

class SchoolClassData extends DbObject {

    public $student_id;
    public $teacher_id;
    public $dt_class_date;
    public $is_recurring;
    public $frequency;
    public $duration; //hours decimal
    public $status; //pending, active, on hold, completed
    public $link;
    public $dt_end_date;
    public $topic;
    public $notes;
    public $rate;


    // error a non well formed numeric value encountered unless strtotime used returned 10:33am when used in classdateedit
    public function getStartTime() {
        //return formatDate($this->dt_class_date, 'H:i');
        if (!empty($this->dt_class_date)) {
            return date('H:i', strtotime($this->dt_class_date));
        }
        return null;
    }

    public function getTeacher() {
        return SchoolService::getInstance($this->w)->GetTeacherForId($this->teacher_id);
    }

    public function getStudent() {
        return SchoolService::getInstance($this->w)->GetStudentForId($this->student_id);
    }

    public function getNextDate() {
        $date = '';
        switch ($this->status) {
            case "pending":
                $date = date('l d/m/Y', $this->dt_class_date);
                break;
            case "active":
                //find next date with same day name

                $date = date('l d/m/Y', strtotime("next " . date('l', $this->dt_class_date)));

                break;
            case "on hold":
            case "completed":
        }
        return $date;
    }

    // Class date edit front end form shows correct date but other page shows wrong date.
    public function getStartDate() {
        if (!empty($this->dt_class_date)) {
            return date('d/m/Y', $this->dt_class_date);
        }
        return null;
    }

    public function getEndDate() {
        if (!empty($this->dt_end_date)) {
            return date('d/m/Y', $this->dt_end_date);
        }
        return null;
    }

    public function GetInstanceForRange($dateArray) {
        //first check if class is running within the date range

        // Filter out if date is out of range of the request date
        if ($this->dt_class_date > strtotime($dateArray['end']) || (!empty($this->dt_end_date) && $this->dt_end_date < strtotime($dateArray['start']))) {
            return null;
        }

        // retrieve instances from request date range / if instance is edited we want to get it still so we dont recreate a new one
        $instances = SchoolService::getInstance($this->w)->GetObjects('SchoolClassInstance', ['is_deleted' => 0, /*'is_edited' => 0,*/ "class_data_id" => $this->id, "dt_class_date >= ? " => date('Y-m-d 00:00:00', strtotime($dateArray['start'])), "dt_class_date <= ? " => date('Y-m-d 00:00:00', strtotime($dateArray['end']))]);



        $instance = '';
        if (empty($instances)) {

            $createInstance = false;
            //Check if is_recurring is false and frequency is "one off"
            if (!$this->is_recurring && $this->frequency == "one off") {

                if ($this->dt_class_date  < strtotime($dateArray['start']) || $this->dt_class_date > strtotime($dateArray['end'])) {
                } else {
                    $createInstance = true;
                }
            } else if ($this->is_recurring && $this->frequency == "weekly") {
                $createInstance = true;
            } else if ($this->is_recurring && $this->frequency == "fortnightly") {
                $daysDifferenceForWeekStart = date('w', $this->dt_class_date) - date('w', strtotime($dateArray['start'])); // get days difference from start of week 
                $startRangeOffset = date('Y-m-d', strtotime($dateArray['start'])) . ' ' . date('H:i:s', $this->dt_class_date);
                $possibleClassDate = date('Y-m-d', strtotime($startRangeOffset . " + " . $daysDifferenceForWeekStart . " days"));  // set new class date
                $possibleClassDate = new DateTime($possibleClassDate);
                $initalClassDate = date('Y-m-d', $this->dt_class_date);
                $initalClassDate = new DateTime($initalClassDate);
                if (date_diff($initalClassDate, $possibleClassDate)->days % 14 == 0) {
                    $createInstance = true;
                }
            }

            // check if its recurring and frequency is 4 weekly (21days)
            else if ($this->is_recurring && $this->frequency == "four weekly") {

                $daysDifferenceForWeekStart = date('w', $this->dt_class_date) - date('w', strtotime($dateArray['start'])); // get days difference from start of week 
                $startRangeOffset = date('Y-m-d', strtotime($dateArray['start'])) . ' ' . date('H:i:s', $this->dt_class_date);
                $possibleClassDate = date('Y-m-d', strtotime($startRangeOffset . " + " . $daysDifferenceForWeekStart . " days"));  // set new class date

                $possibleClassDate = new DateTime($possibleClassDate);

                $initalClassDate = date('Y-m-d', $this->dt_class_date);
                $initalClassDate = new DateTime($initalClassDate);

                if (date_diff($initalClassDate, $possibleClassDate)->days % 28 == 0) {
                    $createInstance = true;
                }
            }

            if ($createInstance == true) {
                $instance = new SchoolClassInstance($this->w);
                $instance->class_data_id = $this->id;
                // $weekdayName = date('l Y/m/d', $this->dt_class_date);  // get class data weekday name
                // $weekdayNumber = date('w', $this->dt_class_date); // get class data weekdayNumber
                $daysDifference = date('w', $this->dt_class_date) - date('w', strtotime($dateArray['start'])); // get days difference from start of week
                $startRangeOffset = (date('Y-m-d', strtotime($dateArray['start'])) . ' ' . date('H:i:s', $this->dt_class_date));
                $classDate = date('Y-m-d', strtotime($startRangeOffset . " + " . $daysDifference . " days"));

                $instance->dt_class_date = $classDate . " " . date('H:i:s', $this->dt_class_date);
                $instance->status = 'Scheduled';
                $instance->insertOrUpdate();
                $instance = SchoolService::getInstance($this->w)->GetClassInstancesForId($instance->id);
            }
        } else {
            if (count($instances) == 1) {
                $instance = $instances[0];
            } else {
                var_dump($instances);
            }
        }
        return $instance;
    }


    public function GetInstanceForCurrentWeek() {
        $instances = SchoolService::getInstance($this->w)->GetObjects('SchoolClassInstance', ['is_deleted' => 0, "class_data_id" => $this->id, "dt_class_date >= ? " => date('Y-m-d 00:00:00', strtotime('last sunday')), "dt_class_date <= ? " => date('Y-m-d 23:59:59', strtotime('next sunday'))]);

        $instance = '';
        if (empty($instances)) {

            //check if the class has started or is starting this week
            if ($this->dt_class_date <= strtotime('next sunday')) {
                $instance = new SchoolClassInstance($this->w);
                $instance->class_data_id = $this->id;

                $instance->dt_class_date = date('Y-m-d H:i:s', strtotime("" . date('l H:i:s', $this->dt_class_date) . " this week"));

                $instance->insertOrUpdate();
                $instance = SchoolService::getInstance($this->w)->GetClassInstancesForId($instance->id);
            }
        } else {
            if (count($instances) == 1) {
                $instance = $instances[0];
            } else {
                echo "HELLO TEST <br>";
                var_dump($instances);
            }
        }
        return $instance;
    }
}
