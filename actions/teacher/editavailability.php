<?php

use Egulias\EmailValidator\Warning\Warning;

function editavailability_GET(Web $w) {

    $p = $w->pathMatch("object_type","object_id", "availability_id");
    if (empty($p['object_type'])) {
        $w->error("No object type provided", "/school");
    }

    if (empty($p['object_id'])) {
        $w->error("No object id provided", "/school");
    }

    if (empty($p['availability_id'])) {
        $availability = new SchoolTeacherAvailability($w);
    } else {
        $availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForId($p['availability_id']);
    }
    
    
    if ($p['object_type'] == "teacher") {
        $object = SchoolService::getInstance($w)->GetTeacherForId($p['object_id']);
    } elseif($p['object_type'] == "student") {
        $object = SchoolService::getInstance($w)->GetStudentForId($p['object_id']);
    }
    
    if (empty($object)) {
        $w->error("No object found for id", "/school");
    }

    $w->ctx('title', 'Edit Availability for ' . $object->getFullName());





    $loginUserId = AuthService::getInstance($w)->user()->id;

     if ($p['object_type'] == "teacher" && $loginUserId != $object->user_id) {
        $w->error("Teacher IDs don't match", "/school");
     }



    $form = [
        "details" => [
            [
                ["Type", "select", "type", $availability->type, ["Unavailable", "Available"]],
                ["Day", "select", "week_day", $availability->getDay(), ["Monday","Tuesday","Wednesday","Thursday","Friday"]]
            ],
            [
                (new \Html\Form\InputField([
                    "id|name"        => "start_time",
                    "value"            => $availability->getStartTime(),
                    "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                    "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                    "required"        => "true"
                ]))->setLabel('Start Time')
                ],
                [
                    (new \Html\Form\InputField([
                        "id|name"        => "end_time",
                        "value"            => $availability->getEndTime(),
                        "pattern"        => "^(0?[0-9]|1[0-9]|2[0-3]):[0-5][0-9](\s+)?(AM|PM|am|pm)?$",
                        "placeholder"    => "12hr format: 11:30pm or 24hr format: 23:30",
                        "required"        => "true"
                    ]))->setLabel('End Time')
                ]
        ]
    ];

    $w->ctx("form", Html::multiColForm($form, "/school-teacher/editavailability/" . $p['object_type'] . "/" . $object->id . "/" . $availability->id, 'POST', 'Save', null, null, Html::b('/school-teacher/deleteAvailability/' . $availability->id, 'Delete', 'Are you sure you want to delete?', null, false, 'warning')));


}

function editavailability_POST(Web $w) {
    $p = $w->pathMatch("object_type", "object_id", "availability_id");

    if (empty($p['object_type'])) {
        $w->error("No object type provided", "/school");
    }

    if (empty($p['object_id'])) {
        $w->error("No object id provided", "/school");
    }

    if (empty($p['availability_id'])) {
        $availability = new SchoolTeacherAvailability($w);
    } else {
        $availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForId($p['availability_id']);
    }

    $availability->object_type = $p['object_type'];
    $availability->object_id = $p['object_id'];
    $availability->type = $_POST["type"];

    if ($p['object_type'] == 'teacher') {
        $object = SchoolService::getInstance($w)->GetTeacherForId($p['object_id']);
    } else if ($p['object_type'] == 'student') {
        $object = SchoolService::getInstance($w)->GetStudentForId($p['object_id']);
    }

    try {
        $start_date = date("Y/m/d", strtotime("this " . $_POST["week_day"]));
        // var_dump($start_date); die;
        $time_object = new DateTime(str_replace('/', '-', $start_date) . ' ' . $_POST['start_time']);
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error('Invalid start date or time', $redirect ?: '/school');
    }
    $availability->dt_start_time = $time_object->format('Y-m-d H:i:s');

    try {
        $start_date = date("Y/m/d", strtotime("this " . $_POST["week_day"]));
        $time_object = new DateTime(str_replace('/', '-', $start_date) . ' ' . $_POST['end_time']);
    } catch (Exception $e) {
        LogService::getInstance($w)->setLogger("SCHOOL")->error($e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $w->error('Invalid start date or time', $redirect ?: '/school');
    }
    $availability->dt_end_time = $time_object->format('Y-m-d H:i:s');

    $availability->insertOrUpdate();

    if (AuthService::getInstance($w)->user()->hasRole('school_manager')) {
        $return_url = '/school-manager/calendar?calendar__teacher-id=' . $p['teacher_id'];
    } else {
        $return_url = "/school-teacher/teachercalendar/" . $p['teacher_id'];
    }

    $w->msg("availability updated", $return_url);
}