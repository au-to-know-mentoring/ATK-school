<?php


function myfeed_ALL(Web $w) {
   


    // var_dump($_REQUEST); die;

    
    $w->setLayout(null);
    $p = $w->pathMatch('teacher_id');

    $teacher_availability = [];
    if (empty($p['teacher_id'])) {
        $classes = SchoolService::getInstance($w)->GetAllClassDataForDateRange($_REQUEST);
        
    } else {
        $classes = SchoolService::getInstance($w)->GetAllClassDataForTeacherIdAndDateRange($p['teacher_id'],$_REQUEST);
        $teacher_availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForTeacherId($p['teacher_id']);
    }
   
    // echo "<pre>";

    

    $class_instances = [];

    foreach ($classes as $class) {
        $ci = $class->GetInstanceForRange($_REQUEST);
        if (!empty($ci)) {
            $class_instances[] = $ci;
        }
    }

    
    
    $calendarEvents = [];

    if (!empty($class_instances)) {
        foreach ($class_instances as $class_instance) {
            // echo "<pre>";
            // var_dump($class_instance);
            // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
            $class_data = $class_instance->getClassData();
            //$startDate = 
            // if (empty($p['teacher_id'])) {
                
            //     $start = $class_instance->dt_class_date;
            //     $end = $class_instance->dt_class_date->modify("+" . $class_data->duration . "hours");
            // } else {
            //     $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['teacher_id']);
            //     //var_dump($class_instance->dt_class_date); die;
            //     $time = new DateTime("now", new DateTimeZone($teacher->timezone));    

            //     $start = $class_instance->dt_class_date;
                
            //     $end = $class_instance->dt_class_date->modify("+" . $class_data->duration . "hours");
                
            // }

            // $start = $class_instance->dt_class_date->modify("-" . $class_data->duration . "h");
            
            // $end = $class_instance->dt_class_date;

            
            // var_dump($class_instance->dt_class_date);

            $start = $class_instance->dt_class_date;
            
            $end = $class_instance->dt_class_date;

            
            // var_dump($class_instance->dt_class_date);
           

            // var_dump($end);

            $event = [
                'title'=> $class_instance->getCalendarTitle(), 
                // use formatDate() for all of these it works and is more understandable. 
                'start'=> formatDate($start, 'Y-m-d H:i', $_SESSION['usertimezone']), 
                'end'=> formatDate($end->add(new DateInterval("PT" . $class_data->duration . "H")), 'Y-m-d H:i', $_SESSION['usertimezone']), 
                'url'=> '/school-teacher/viewclassinstance/' . $class_instance->id,
                'className' => $class_instance->status,
            ];
            $calendarEvents[] = $event;
        }
    }
  
    //get availabiliity for teacher
    if (!empty($teacher_availability)) {
        foreach ($teacher_availability as $availability) {
            $event = [
                'title'=> $availability->type, // a property!
                'start'=> $availability->getStartForCurrentWeek($_REQUEST), // a property!
                'end'=> $availability->getEndForCurrentWeek($_REQUEST),
                'url'=> '/school-teacher/editavailability/' . "teacher/" . $p['teacher_id'] . '/' .  $availability->id,  ///////////////
                'className'=> $availability->type,
                
            ];
            $calendarEvents[] = $event;
        }
    }

    $w->out(json_encode($calendarEvents));
}