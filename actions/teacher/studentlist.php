<?php

function studentlist_ALL(Web $w) {

    $w->ctx('title', 'Participants');

    $loggedInUser = AuthService::getInstance($w)->user();

    if ($loggedInUser->hasRole('school_manager')) {
        $students = SchoolService::getInstance($w)->GetAllStudents();
    } elseif ($loggedInUser->hasRole('school_teacher')) {
        $teacher = SchoolService::getInstance($w)->GetTeacherForUserId($loggedInUser->id);
        $students = SchoolService::getInstance($w)->GetAllStudentsForTeacherId($teacher->id);
    } else {
        $w->error('Cannot view page', '/school');
    }
    
    $table_headers = ['Participant Name', 'Status', 'Actions'];

    $table = [];

    if (!empty($students)) {
        foreach ($students as $student) {
            $row = [];
            $row[] = $student->getContact()->getFullName();
            $row[] = $student->getStatus();
            $actions = [];
            $actions[] = Html::b('/school-teacher/studentview/' . $student->id, 'View');
            if ($loggedInUser->hasRole('school_manager')) {
                $actions[] = Html::b('/school-manager/studentedit/' . $student->id, 'Edit');
            
                $actions[] = Html::b('/school-manager/studentdelete/' . $student->id, 'Delete', 'Are you sure you want to delete this student?', null, false, 'warning');
                $actions[] = Html::b('/school-manager/classdataedit/' . $student->id, 'Add New Class');
            }
            
            $row[] = implode('', $actions);
            $table[] = $row;
        }
    }

    $w->ctx('studentTable', Html::table($table, null, 'tablesorter', $table_headers));



}