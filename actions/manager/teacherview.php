<?php

function teacherview_ALL(Web $w)
{
    $p = $w->pathMatch('id');

    if (empty($p['id'])) {
        $w->error('No Teacher Id given', '/school');
    }

    $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['id']);

    if (empty($teacher)) {
        $w->error('No Teacher found for Id', '/school');
    }

    $w->ctx('teacher_id', $teacher->id);

    $w->ctx('title', 'View Mentor Details: ' . $teacher->getFullName());

    $teacher_contact = $teacher->getContact();

    $table = [
        "contact details" => [
            [

                ['Name', 'text', 'name', $teacher->getFullName()],
                ['Phone', 'text', 'mobile', $teacher_contact->mobile],
                ['Email', 'text', 'email', $teacher_contact->email]

            ]
        ],
        "Mentor Data" => [
            [

                ['Max participants', 'text', 'max_students', $teacher->max_students],
                ['Current Number of Classes', 'text', 'classCount', (string) $teacher->getClassesCount()],
                ['State', 'text', 'state' ,$teacher->state],
                ['Time Zone', 'text', 'timezone', $teacher->timezone]
            ]
        ]
    ];

    $w->ctx('detailsTable', Html::multiColTable($table));

    //$students = SchoolService::getInstance($w)->GetAllStudentsForTeacherId($teacher->id);

    $classes_table = [];
    $classes = SchoolService::getInstance($w)->GetAllClassDataForTeacherId($teacher->id);
    $classes_table_headers = ['Participant','Next Class Date','Time','Frequency', 'Status','Actions'];
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

}
