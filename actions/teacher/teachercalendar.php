<?php

function teachercalendar_ALL(Web $w) {

    $user = AuthService::getInstance($w)->user();
    $w->ctx("user", $user);
    $teacher = '';
    $classes = [];

    if ($user->hasRole('school_manager')) {
        //$students = SchoolService::getInstance($w)->GetAllStudents();
        $classes = SchoolService::getInstance($w)->GetAllClassData();
        $teacher_availability = [];
    } elseif ($user->hasRole('school_teacher')) {
        $teacher = SchoolService::getInstance($w)->GetTeacherForUserId($user->id);
        $w->ctx("teacher_id", $teacher->id);
        // $classes = SchoolService::getInstance($w)->GetAllClassDataForTeacherId($teacher->id);
        //$students = SchoolService::getInstance($w)->GetAllStudentsForTeacherId($teacher->id);
        // $teacher_availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForTeacherId($teacher->id);
    } else {
        $w->error('Cannot view page');
    }

    $w->ctx('title', 'Calendar: ' . $teacher->getFullName() . "  Timezone: ". $teacher->timezone);

    // if (empty($classes)) {
    //     $w->error('no classes found');
    // }

    // $class_instances = [];

    // foreach ($classes as $class) {
    //     $ci = $class->GetInstanceForCurrentWeek();
    //     if (!empty($ci)) {
    //         $class_instances[] = $ci;
    //     }
    // }

    // //var_dump($class_instances);

    // $calendarEvents = [];

    // if (!empty($class_instances)) {
    //     foreach ($class_instances as $class_instance) {
    //         // echo "<pre>";
    //         // var_dump($class_instance);
    //         // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
    //         $class_data = $class_instance->getClassData();
    //         $event = [
    //             'title'=> $class_instance->getCalendarTitle(), // a property!
    //             // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
    //             // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
    //             'start'=> date('Y-m-d H:i', $class_instance->dt_class_date), // a property!
    //             'end'=> date('Y-m-d H:i', $class_instance->dt_class_date + ($class_data->duration * 60 * 60)),
                
    //         ];
    //         $calendarEvents[] = $event;
    //     }
    // }

    // //get availabiliity for teacher
    // if (!empty($teacher_availability)) {
    //     foreach ($teacher_availability as $availability) {
    //         // echo "<pre>";
    //         // var_dump($class_instance);
    //         // var_dump(date('Y-m-d', strtotime($class_instance->dt_class_date))); die;
    //         $event = [
    //             'title'=> $availability->type, // a property!
    //             // 'start'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date)), // a property!
    //             // 'end'=> date('Y-m-d H:i', strtotime($class_instance->dt_class_date) + ($class_data->duration * 60 * 60)),
    //             'start'=> $availability->getStartForCurrentWeek(), // a property!
    //             'end'=> $availability->getEndForCurrentWeek(),
    //             'className'=> $availability->type,
                
    //         ];
    //         $calendarEvents[] = $event;
    //     }
    // }



    // $w->ctx('events', json_encode($calendarEvents));
    // var_dump(json_encode($calendarEvents)); die;

}