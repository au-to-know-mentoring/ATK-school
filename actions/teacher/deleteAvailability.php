<?php 
function deleteAvailability_ALL(Web $w){
    $p = $w->pathMatch('id');
    if (empty($p['id'])){
        $w->error('No id found', '/school-teacher/teachercalendar');
    }
    $availability = SchoolService::getInstance($w)->GetTeacherAvailabilityForId($p['id']);
    if (empty($availability)){
        $w->error('No availability found for id', '/school-teacher/teachercalendar');
    }
    if(AuthService::getInstance($w)->user()->hasRole('school_teacher')){
        $availability->delete();
        $w->msg("Item deleted", '/school-teacher/teachercalendar');
    }
    else {
        $w->msg("Permission denied", '/school-teacher/teachercalendar');
    }
}
