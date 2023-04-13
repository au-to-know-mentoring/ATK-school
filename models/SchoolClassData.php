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
    
    public function getStartTime() {
        //return formatDate($this->dt_class_date, 'H:i');
        if (!empty($this->dt_class_date)) {
            return date('H:i', $this->dt_class_date);
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
        // var_dump($dateArray);
        // echo "<br>";
        // echo 

        if ($this->dt_class_date > strtotime($dateArray['end']) || (!empty($this->dt_end_date) && $this->dt_end_date < strtotime($dateArray['start']))) {
            return null;
        }

        $instances = SchoolService::getInstance($this->w)->GetObjects('SchoolClassInstance',['is_deleted'=> 0, "class_data_id"=> $this->id, "dt_class_date >= ? "=> date('Y-m-d 00:00:00',strtotime($dateArray['start'])), "dt_class_date <= ? "=> date('Y-m-d 00:00:00',strtotime($dateArray['end']))]);

        $instance = '';
        if (empty($instances)) {
            //check if the class has started or is starting this week
            // echo "dt class date = <br><pre>";
            // var_dump($this->dt_class_date);
            // echo "</pre><br>next sunday = <br><pre>";
            // var_dump(strtotime('next sunday')); 
            
            $instance = new SchoolClassInstance($this->w);
            $instance->class_data_id = $this->id;
            $weekdayName = date('l Y/m/d', $this->dt_class_date);
            $weekdayNumber = date('w', $this->dt_class_date);
            $daysDifference = date('w', $this->dt_class_date) - date('w', strtotime($dateArray['start']));
            $startRangeOffset = (date('Y-m-d', strtotime($dateArray['start'])) . ' ' . date('H:i:s', $this->dt_class_date));
            $classDate = date('Y-m-d', strtotime($startRangeOffset . " + " . $daysDifference . " days"));

            // echo "<pre>";
            // var_dump($weekdayName);
            // var_dump($weekdayNumber);
            // var_dump(date('l Y/m/d H:i',strtotime($dateArray['start'])));
            // var_dump(date('Y-m-d', strtotime($dateArray['start'])) . ' ' . date('H:i:s', $this->dt_class_date));
            // var_dump($daysDifference); 
            // var_dump($classDate);
            // die;

            
            $instance->dt_class_date = $classDate . " " . date('H:i:s', $this->dt_class_date);
            //$instance->dt_class_date = date('Y-m-d H:i:s', strtotime("next " . date('l', $this->dt_class_date) . '' . date('H:i:s', $this->dt_class_date)));
            $instance->status = 'Scheduled';
            $instance->insertOrUpdate();
            $instance = SchoolService::getInstance($this->w)->GetClassInstancesForId($instance->id);
            
            
        } else {
            if (count($instances) == 1) {
                //var_dump($instances);
                $instance = $instances[0];
            } else {
                var_dump($instances);
            }
        }
        return $instance;
    }

    public function GetInstanceForCurrentWeek() {
        $instances = SchoolService::getInstance($this->w)->GetObjects('SchoolClassInstance',['is_deleted'=> 0, "class_data_id"=> $this->id, "dt_class_date >= ? "=> date('Y-m-d 00:00:00',strtotime('last sunday')), "dt_class_date <= ? "=> date('Y-m-d 23:59:59',strtotime('next sunday'))]);

        //var_dump(date('Y-m-d 23:59:59',strtotime('next sunday')));

        $instance = '';
        if (empty($instances)) {
            //check if the class has started or is starting this week
            // echo "dt class date = <br><pre>";
            // var_dump($this->dt_class_date);
            // echo "</pre><br>next sunday = <br><pre>";
            // var_dump(strtotime('next sunday')); 
            if ($this->dt_class_date <= strtotime('next sunday')) {
                $instance = new SchoolClassInstance($this->w);
                $instance->class_data_id = $this->id;
                
                $instance->dt_class_date = date('Y-m-d H:i:s', strtotime("" . date('l H:i:s', $this->dt_class_date) . " this week"));
                //$instance->dt_class_date = date('Y-m-d H:i:s', strtotime("next " . date('l', $this->dt_class_date) . '' . date('H:i:s', $this->dt_class_date)));
                $instance->insertOrUpdate();
                $instance = SchoolService::getInstance($this->w)->GetClassInstancesForId($instance->id);
            }
            
        } else {
            if (count($instances) == 1) {
                //var_dump($instances);
                $instance = $instances[0];
            } else {
                echo "HELLO TEST <br>";
                var_dump($instances);
            }
        }
        return $instance;
    }

}