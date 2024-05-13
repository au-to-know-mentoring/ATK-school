<?php

function calendar_ALL(Web $w) {

    $user = AuthService::getInstance($w)->user();
    $w->ctx("user", $user);
    $teacher = '';
    $classes = [];
    $reset = $w->sessionOrRequest('reset');

    if ($user->hasRole('school_manager')) {
        //$students = SchoolService::getInstance($w)->GetAllStudents();
        $classes = SchoolService::getInstance($w)->GetAllClassData();
        $teacher_availability = [];
    } else {
        $w->error('Cannot view page');
    }


    if (empty($classes)) {
        $w->error('no classes found');
    }

    if (empty($reset)) {
        $teacher_id = $w->sessionOrRequest("calendar__teacher-id");
    } else {
        $w->sessionUnset("calendar__teacher-id");
    }
    
    if (!empty($teacher_id)) {
        $teacher = SchoolService::getInstance($w)
        ->GetTeacherForId($teacher_id);
        $w->ctx('title', 'Viewing Calendar for ' . $teacher->getFullName() . ',  Timezone: ' . $teacher->timezone);
        $w->ctx('teacher_id', $teacher_id);
        //$classes = SchoolService::getInstance($w)->GetAllClassDataForTeacherId($teacher_id);
        //$teacher_availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForTeacherId($teacher_id);
    }

    //create filters
    $filter = [
        ["Filter By Menotor", "select", "calendar__teacher-id",  !empty($teacher_id) ? $teacher_id : null, SchoolService::getInstance($w)->GetAllTeachers()],
    ];
    $w->ctx("filter_data", $filter);




    $class_instances = [];

    foreach ($classes as $class) {
        $ci = $class->GetInstanceForCurrentWeek();
        if (!empty($ci)) {
            $class_instances[] = $ci;
        }
    }

  //  var_dump($class_instances);

    $calendarEvents = [];

    if (!empty($class_instances)) {
        foreach ($class_instances as $class_instance) {
            
            // echo "<pre>";
            // var_dump($class_instance);
            // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
            $class_data = $class_instance->getClassData();
            $dt_end = $class_instance->dt_class_date->modify("+" . $class_data->duration . "hours");
            $event = [
                'title'=> $class_instance->getCalendarTitle(), // a property!
                // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
                // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
                'start'=> $class_instance->dt_class_date->format('Y-m-d H:i'), // a property!
                
                'end'=> $dt_end->format('Y-m-d H:i'),
                
            ];
            $calendarEvents[] = $event;
        }
    }

   // get availabiliity for teacher
    if (!empty($teacher_availability)) {
        foreach ($teacher_availability as $availability) {
            // echo "<pre>";
            // var_dump($class_instance);
            // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
            $event = [
                'title'=> $availability->type, // a property!
                // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
                // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
                'start'=> $availability->getStartForCurrentWeek(), // a property!
                'end'=> $availability->getEndForCurrentWeek(),
                'className'=> $availability->type,
                
            ];
            $calendarEvents[] = $event;
        }
    }
  //  echo "<pre>";
   // var_dump($calendarEvents);


    $w->ctx('events', json_encode($calendarEvents));
    // var_dump(json_encode($calendarEvents)); die;

}