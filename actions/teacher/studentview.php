<?php

function studentview_ALL(Web $w) {
    $p = $w->pathMatch('student_id','class_data_id');

    $loggedInUser = AuthService::getInstance($w)->user();
    

    if ($loggedInUser->hasRole('school_manager')) {
        $w->msg("",'/school-manager/studentview/' . $p['student_id']);
    }
    
    $user = null;
    if ($loggedInUser->hasRole('school_teacher')){
        $user = SchoolService::getInstance($w)->GetTeacherForUserId($loggedInUser->id);
    }

    if (empty($p['student_id'])) {
        $w->error('No Student Id found', '/school-teacher/studentlist');
    }

    $student = SchoolService::getInstance($w)->GetStudentForId($p['student_id']);
    $w->ctx('student_id', $student->id);

    $w->ctx('title', $student->getContact()->getFullName());

    if (empty($student)) {
        $w->error('No Student found for id', '/school-teacher/studentlist');
    }

    $student_contact = $student->getContact();
    $main_contact = $student->getMainContact();

    //smaple time for student
    $time = null;
    if (!empty($student->timezone)) {
        $time = new DateTime("now", new DateTimeZone($student->timezone));
    }
    


    $studentData = [
        "Student Details" => [
            [
                ["Home Phone", "text", "student_homephone", $student_contact->homephone],
                ["Mobile", "text", "student_mobile", $student_contact->mobile],
                ["Email", "text", "student_email", $student_contact->email]
            ],
            [
                ["State", "text", "state", $student->state],
                ["Timezone", "text", "timezone", $time ? $student->timezone . ' - ' . $time->format('H:i') : '']
            ]
        ],
        "Main Contact Details" => [
            [
                ["First Name", "text", "main_contact_firstname", $main_contact->firstname],
                ["Last Name", "text", "main_contact_lastname", $main_contact->lastname]
            ],
            [
                ["Home Phone", "text", "main_contact_homephone", $main_contact->homephone],
                ["Mobile", "text", "main_contact_mobile", $main_contact->mobile],
                ["Email", "text", "main_contact_email", $main_contact->email]
            ]
        ]
    ];
    $w->ctx('detailsTable', Html::multiColTable($studentData));

    $classes_table = [];
    $classes = SchoolService::getInstance($w)->GetClassDataForStudentId($student->id);
    if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
        $classes_table_headers = ['teacher','next date','time','frequency', 'Status','actions'];
    } else {
        $classes_table_headers = ['teacher','next data', 'time', 'link'];
    }
    
    if (!empty($classes)) {
        foreach ($classes as $class) {
            

            $dt_Object = new DateTime(formatDate($class->dt_class_date, "H:i"), new DateTimeZone($class->timezone));
            $dt_Object->setTimezone(new DateTimeZone($user->timezone));

            $row = [];
            $row[] = $class->getTeacher()->getContact()->getFullName();
            $row[] = $class->getNextDate();
            $row[] = $dt_Object->format("H:i");
            if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
                $row[] = $class->frequency;
                $row[] = $class->status;
                $actions = [];
            
                $actions[] = Html::b('/school-manager/classdataedit/' . $student->id . '/' . $class->id, 'Edit');
                //$actions[] = Html::b('/school-teacher/viewclassdata/' . $class->id, 'View');
                $row[] = implode($actions);
            } else {
                $row[] = $class->link;
            }
            
            
            $classes_table[] = $row;
        }
    }

    $w->ctx('classes_table', Html::table($classes_table, null, "tablesorter", $classes_table_headers));

}