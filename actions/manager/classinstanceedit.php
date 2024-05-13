<?php

function classinstanceedit_GET(Web $w) {
    $p = $w->pathMatch('class_instance_id');

    if (empty($p['class_instance_id'])) {
        $w->error('No class instance Id found TEST', '/school-manager/calendar');
    }

    $class_instance = SchoolService::getInstance($w)->GetClassInstancesForId($p['class_instance_id']);

    if (empty($class_instance)) {
        $w->error('No class instance found id', '/school-manager/calendar');
    }

    $class_data = $class_instance->getClassData();

    $student = $class_data->getStudent();
    $student_contact = $student->getContact();
    $teacher = $class_instance->getTeacher();
    $teacher_contact = $teacher->getContact();
    $main_contact = $student->getMainContact();

    $w->ctx('title', 'Editing Session Instance - Participant: ' . $student_contact->getFullName());

    $table = [
        'Class Details' => [
            [
                ['Topic', 'text', 'topic', $class_data->topic],
                // ['Date', 'text', 'date', date('l d/m/Y', $class_instance->dt_class_date)],
                // ['Time', 'text', 'time', date('H:i', $class_instance->dt_class_date)]
            ],
            [
                ['Link', 'text', 'link', $class_data->link],
                // ['Status', 'text', 'status', $class_instance->status]
            ],
        ], 
        'Participant Details' => [
            [
                ['Participant Name', 'text', 'student_name', Html::a("/school-manager/studentview/" . $student->id, $student_contact->getFullName()) ],
                ['Participant Phone', 'text', 'student_number', $student_contact->mobile],
            ]
        ],
        'Participant Main Contact' => [
            [
                ['Main Contact', 'text', 'student_main_contact', $main_contact->getFullName()],
                ['Main Contact Phone', 'text', 'main_contact_number', $main_contact->mobile]
            ]
        ],
        'Mentor Details' => [
            [
                ['Mentor Name', 'text', 'teacher_name', Html::a("/school-manager/teacherview/" . $teacher->id, $teacher_contact->getFullName()) ],
                ['Mentor Phone', 'text', 'teacher_number', $teacher_contact->mobile],
                
            ],
        ],
        'Notes' => [
            // [
            //     ['Session Notes', 'text', 'notes', $class_instance->teachers_notes]
            // ],
            [
                ['Class Notes', 'text', 'notes', $class_data->notes]
            ]
        ]
    ];


    $w->ctx('detailsTable', Html::multiColTable($table));

    $statusSelectArray = ['Scheduled', 'Late Cancel', 'Canceled', 'Completed'];

    $form = [
        'Session Instance Edit' => [
            
            [
                ['Timezone', 'select', 'timezone', 'Australia/Sydney', SchoolService::getInstance($w)->GetTimeZoneSelectOptions()]
            ],
            [
                ['Session Date', 'date', 'start_date', $class_instance->dt_class_date->format('d/m/Y')], 
                //['Class Start Time', 'text', 'start_time', $class_data->getStartTime()],
                (new \Html\Form\InputField([
                    "id|name"        => "start_time",
                    "value"            =>  formatDate($class_instance->dt_class_date,'H:i', $_SESSION['usertimezone']), //change this !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                    "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                    "required"        => "true"
                ]))->setLabel('Session Start Time')
            ],
            [
                ['Status', 'select', 'status', $class_instance->status, $statusSelectArray],
                ['Substitute Teacher', 'select', 'substitute_teacher_id', $class_instance->substitute_teacher_id ? $class_instance->substitute_teacher_id : null, SchoolService::getInstance($w)->GetAllTeachers()]
            ],
            [
                ['Notes', 'textarea', 'notes', $class_instance->teachers_notes]
            ]
                  
        ]
    ];

    // if (empty($p['class_data_id'])) {
    //     $form_url = '/school-manager/classdataedit/' . $student->id;
    // } else {
    //     $form_url = '/school-manager/classdataedit/' . $student->id . '/' . $class_data->id;
    // }


    $w->ctx('form', Html::multiColForm($form, '/school-manager/classinstanceedit/' . $class_instance->id));

}

function classinstanceedit_POST(Web $w) {
    $p = $w->pathMatch('class_instance_id');

    if (empty($p['class_instance_id'])) {
        $w->error('No class instance id provided', '/school-manager/calendar');
    }

    $class_instance = SchoolService::getInstance($w)->GetClassInstancesForId($p['class_instance_id']);

    if (empty($class_instance)) {
        $w->error('No class instance found for id', '/school-manager/calendar');
    }

    $class_instance->fill($_POST);
    $class_instance->teachers_notes = $_POST['notes'];

    $redirect = '/school-manager/viewclassinstance/' . $class_instance->id;
    try {
        if ($_POST['timezone'] == 'Australia/Sydney') {
            $time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST['start_time']);
        } else {
            $time_object = new DateTime(str_replace('/', '-', $_POST['start_date']) . ' ' . $_POST['start_time'], new DateTimeZone($_POST['timezone']));
        }
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error('Invalid start date or time', $redirect ?: '/school');
    }
    //echo "<pre>";
    //var_dump(new DateTime($time_object->getTimestamp())->format('Y-m-d H:i:s')); die;
    // $dt_object = new DateTime();
    // $dt_object->setTimestamp($time_object->getTimestamp());
    $class_instance->dt_class_date = $time_object;
    if ($class_instance->substitute_teacher_id == '') {
        $class_instance->substitute_teacher_id = null;
    }
    $class_instance->insertOrUpdate();

    $w->msg('Session updated', '/school-manager/viewclassinstance/' . $class_instance->id);
    

}
