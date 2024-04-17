<?php

function myfeed_ALL(Web $w) {

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
    // var_dump($classes);

    $class_instances = [];

    foreach ($classes as $class) {
        $ci = $class->GetInstanceForRange($_REQUEST);
        if (!empty($ci)) {
            $class_instances[] = $ci;
        }
    }

    // var_dump($class_instances);

    $calendarEvents = [];

    if (!empty($class_instances)) {
        foreach ($class_instances as $class_instance) {
            // echo "<pre>";
            // var_dump($class_instance);
            // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date)));
            $class_data = $class_instance->getClassData();
            //$startDate = 
            if (empty($p['teacher_id'])) {
                $time = new DateTime();
                //var_dump($time);
                $time->setTimestamp($class_instance->dt_class_date);
                $start = $time->format('Y-m-d H:i');
                $time->setTimestamp($class_instance->dt_class_date + ($class_data->duration * 60 * 60));
                $end = $time->format('Y-m-d H:i');
            } else {
                $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['teacher_id']);
                $dtz = new DateTimeZone($teacher->timezone);
                //var_dump($class_instance->dt_class_date); die;
                $time = new DateTime(null, $dtz);
                //var_dump($time);
                $time->setTimestamp($class_instance->dt_class_date);
                $start = $time->format('Y-m-d H:i');
                $time->setTimestamp($class_instance->dt_class_date + ($class_data->duration * 60 * 60));
                $end = $time->format('Y-m-d H:i');
                //var_dump($start); die;
            }
            //  var_dump($class_instance);
            //var_dump($time->format('T')); echo "<br>";
            // Us dumb Americans can't handle millitary time
            //ampm = $time->format('H') > 12 ? ' ('. $time->format('g:i a'). ')' : '';
            $event = [
                'title'=> $class_instance->getCalendarTitle(), // a property!
                // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
                // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
                'start'=> $start, // date('Y-m-d H:i', $class_instance->dt_class_date), // a property!
                'end'=> $end, //date('Y-m-d H:i', $class_instance->dt_class_date + ($class_data->duration * 60 * 60)),
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