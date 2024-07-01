<?php

class SchoolClassData extends DbObject {

    public $student_id;
    public $teacher_id;
    public $dt_class_date;
    public $class_date_timezone;
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
            
            return formatDate($this->dt_class_date, 'H:i', $_SESSION['usertimezone']);
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
                $date = formatDate($this->dt_class_date, 'l d/m/Y', $_SESSION['usertimezone']); 
                break;
            case "active":
                //find next date with same day name
                
                //$date = date('l d/m/Y', strtotime("next " . date('l', $this->dt_class_date)));
                $date = formatDate($this->dt_class_date, 'l d/m/Y', $_SESSION['usertimezone']); 
              
                //  date('l d/m/Y', strtotime("next " . date('l', $this->dt_class_date)));
                
                break;
            case "on hold":
            case "completed":
        }
        
        
        return $date;
    }

    public function getStartDate() {
        if (!empty($this->dt_class_date)) {

            return formatDate($this->dt_class_date, 'd/m/Y', $_SESSION['usertimezone']);
        }
        return null;
    }

    public function getEndDate() {
        if (!empty($this->dt_end_date)) {
            return formatDate($this->dt_end_date, 'd/m/Y', $_SESSION['usertimezone']);
        }
        return null;
    }

    public function GetInstanceForRange($dateArray) {
        //first check if class is running within the date range
        //  var_dump($dateArray);
        // echo "<br>";
        // echo 

        // if ($this->dt_class_date > $dateArray['end'] || (!empty($this->dt_end_date) && $this->dt_end_date < $dateArray['start'])) {
        //     return null;
        // }

        // var_dump($dateArray);
        // var_dump("hi"); die;
      

        $instances = SchoolService::getInstance($this->w)->GetObjects('SchoolClassInstance',['is_deleted'=> 0, "class_data_id"=> $this->id, "dt_class_date >= ? "=> $dateArray['start'], "dt_class_date <= ? "=> $dateArray['end']]);

        // var_dump($instances); die;


        $instance = '';
        if (empty($instances)) {
            //check if the class has started or is starting this week
            // echo "dt class date = <br><pre>";
            // var_dump($this->dt_class_date);
            // echo "</pre><br>next sunday = <br><pre>";
            // var_dump(strtotime('next sunday')); 
            

            $instance = new SchoolClassInstance($this->w);
            $instance->class_data_id = $this->id;
            $weekdayName = $this->dt_class_date->format('l Y/m/d');
            $weekdayNumber = $this->dt_class_date->format('%w');

            $dt_start = new DateTime($dateArray['start']);
            // $dt_start->setTimezone(new DateTimeZone("utc"));
            $dt_class = $this->dt_class_date;
            
            $daysDifference = $dt_class->format('w') - $dt_start->format("w");
        
            $startRangeOffset = $dt_start->format("Y-m-d") . " " . $dt_class->format("H:i:s"); 

            $dt_startRangeOffset = new DateTime($startRangeOffset);// create DateTime object from string

            $dt_OffsetPlusDiff = $dt_startRangeOffset->modify("+" . $daysDifference . "days"); 
            
            // var_dump($dt_OffsetPlusDiff);
            // var_dump($dt_OffsetPlusDiff->getTimezone());

            
            $instance->dt_class_date = $dt_OffsetPlusDiff;
           
            

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
        $dt_lastSunday = new DateTime("last sunday");
        $dt_nextSunday = new DateTime('next sunday');

        $instances = SchoolService::getInstance($this->w)->GetObjects('SchoolClassInstance',['is_deleted'=> 0, "class_data_id"=> $this->id, "dt_class_date >= ? "=>  $dt_lastSunday->format('Y-m-d 00:00:00'), "dt_class_date <= ? "=> $dt_nextSunday->format('Y-m-d 00:00:00')]);

        //var_dump(date('Y-m-d 23:59:59',strtotime('next sunday')));
       
        
        $instance = '';
        if (empty($instances)) {

            //check if the class has started or is starting this week
            // echo "dt class date = <br><pre>";
            // var_dump($this->dt_class_date);
            // echo "</pre><br>next sunday = <br><pre>";
            // var_dump(strtotime('next sunday')); 
            // if ($this->dt_class_date <= new DateTime('next sunday'))
            $dt_test = new DateTime("next sunday");
            // var_dump($dt_test->diff($this->dt_class_date)); die;

            if ($this->dt_class_date <= new DateTime('next sunday')) {
                $instance = new SchoolClassInstance($this->w);
                $instance->class_data_id = $this->id;
                
              
                $dt_today = new DateTime('now', New DateTimeZone($_SESSION['usertimezone']));

                $instance->dt_class_date = $dt_today->format('Y-m-d H:i:s') . " " . $this->dt_class_date->format('l H:i:s') . " this week";
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