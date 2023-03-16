<?php

function upcomingclasses_ALL(Web $w) {

    $user = AuthService::getInstance($w)->user();
    $teacher = '';
    $students = [];
    $classes = [];

    if ($user->hasRole('school_manager')) {
        $students = SchoolService::getInstance($w)->GetAllStudents();
        
    } elseif ($user->hasRole('school_teacher')) {
        $teacher = SchoolService::getInstance($w)->GetTeacherForUserId($user->id);
        $classes = SchoolService::getInstance($w)->GetAllClassDataForTeacherId($teacher->id);
        $students = SchoolService::getInstance($w)->GetAllStudentsForTeacherId($teacher->id);
    } else {
        $w->error('Cannot view page');
    }

    $classes_table = [];
    
    $classes_table_headers = ['Student','Next Class Date','Time','Frequency', 'Status','Actions'];
    if (!empty($classes)) {
        foreach ($classes as $class) {
            $row = [];
            $row[] = $class->getStudent()->getContact()->getFullName();
            $row[] = $class->getNextDate();
            $row[] = date('H:i', $class->dt_class_date);
            $row[] = $class->frequency;
            $row[] = $class->status;
            $actions = [];
            $actions[] = Html::b('/school-manager/classdataedit/' . $class->student_id . '/' . $class->id, 'Edit');
            $row[] = implode($actions);
            $classes_table[] = $row;
        }
    }

    $w->ctx('studentTable', Html::table($classes_table, null, 'tablesorter', $classes_table_headers));


    $w->ctx('studentTable', Html::table($table, null, 'tablesorter', ['Student Name', 'Status', 'Actions']));



}