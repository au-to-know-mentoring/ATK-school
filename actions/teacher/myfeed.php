<?php

function myfeed_ALL(Web $w) {

    $w->setLayout(null);
    $p = $w->pathMatch('teacher_id');

    // var_dump($p);
    // var_dump($_REQUEST); die;

    $teacher_availability = [];
    if (empty($p['teacher_id'])) {
        $w->error('No teacher id provided');
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

    //var_dump($class_instances);

    $calendarEvents = [];

    if (!empty($class_instances)) {
        foreach ($class_instances as $class_instance) {
            // echo "<pre>";
            // var_dump($class_instance);
            // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
            $class_data = $class_instance->getClassData();
            $event = [
                'title'=> $class_instance->getCalendarTitle(), // a property!
                // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
                // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
                'start'=> formatDate($class_data->dt_class_date, 'Y-m-d H:i', $_SESSION['usertimezone']), // a property!
                'end'=> formatDate($class_data->dt_class_date->modify("+" . $class_data->duration . "hours"), 'Y-m-d H:i', $_SESSION['usertimezone']),
                'url'=> '/school-teacher/viewclassinstance/' . $class_instance->id,
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
                'className'=> $availability->type,
                'url'=> '/school-teacher/editavailability/teacher/' . $p['teacher_id'] . '/' . $availability->id,
                
            ];
            $calendarEvents[] = $event;
        }
    }

    $w->out(json_encode($calendarEvents));
}