<?php

function viewclassinstance_ALL(Web $w) {

    $p = $w->pathMatch('id');

    if (empty($p['id'])) {
        $w->error('no class instance id provided', '/school');
    }

    $w->ctx('class_instance_id', $p['id']);

    $classInstance = SchoolService::getInstance($w)->GetClassInstancesForId($p['id']);

    if (empty($classInstance)) {
        $w->error('no class instance found for id', '/school');
    }

    $classData = $classInstance->getClassData();
    $w->ctx('class_data_id', $classData->id);
    $student = $classData->getStudent();
    $w->ctx('student_id', $student->id);
    $student_contact = $student->getContact();

    $main_contact = $student->getMainContact();

    $teacher = $classInstance->getTeacher();
    $w->ctx('teacher_id', $teacher->id);
    $teacher_contact = $teacher->getContact();

    $w->ctx("title", "Session Details: " . $student_contact->getFullName());

    $table = [
        'Session Details' => [
            [
                ['Topic', 'text', 'topic', $classData->topic],
                ['Date', 'text', 'date', formatDate($classData->dt_class_date, 'l d/m/Y', $_SESSION['usertimezone'])],
                ['Time', 'text', 'time', formatDate($classData->dt_class_date, 'H:i', $_SESSION['usertimezone'])]
                // ['Time', 'text', 'time', formatDate($classInstance->dt_class_date, 'H:i', $_SESSION['usertimezone'])]
            ],
            [
                ['Link', 'text', 'link', $classData->link],
                ['Status', 'text', 'status', $classInstance->status]
            ],
            [
                ['Mentor Name', 'text', 'teacher_name', Html::a("/school-manager/teacherview/" . $teacher->id, $teacher_contact->getFullName()) ],
                ['Mentor Phone', 'text', 'teacher_number', $teacher_contact->mobile],
                ['Participant Name', 'text', 'student_name', Html::a("/school-manager/studentview/" . $student->id, $student_contact->getFullName()) ],
                ['Participant Phone', 'text', 'student_number', $student_contact->mobile],
                ['Main Contact', 'text', 'student_main_contact', $main_contact->getFullName()],
                ['Main Contact Phone', 'text', 'main_contact_number', $main_contact->mobile]
            ],
            [
                ['Session Notes', 'text', 'notes', $classInstance->teachers_notes]
            ],
            [
                ['Class Notes', 'text', 'notes', $classData->notes]
            ]
        ]
    ];


    $w->ctx('detailsTable', Html::multiColTable($table));
}