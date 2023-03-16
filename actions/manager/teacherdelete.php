<?php

function teacherdelete_ALL(Web $w) {
    $p = $w->pathMatch("id");

    if (empty($p['id'])) {
        $w->error("No Teacher Id Provided", "/school-teacher/list");
    }

    $teacher = SchoolService::getInstance($w)->GetTeacherForId($p['id']);

    if (empty($teacher)) {
        $w->error("No Teacher found for id", "/school-teacher/list");
    }

    $teacher->delete();

    $w->msg("Teacher Deleted", "/school-teacher/list");


}