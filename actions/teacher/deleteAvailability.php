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

    $user = AuthService::getInstance($w)->user();

    $redirectUrl = '';

    if($user->hasRole('school_manager')) {
        $redirectUrl = '/school-manager/calendar?calendar__teacher-id=' . $availability->object_id; 
    } else {
        $redirectUrl = '/school-teacher/teachercalendar';
    }




    $loginUser = AuthService::getInstance($w)->user();
    if(($user->hasRole('school_teacher') && $availability->object_id == SchoolService::getInstance($w)->GetTeacherForUserId($loginUser->id)->id) or $user->hasRole('school_manager')){
        $availability->delete();
        $w->msg("Item deleted", $redirectUrl);
    }
    else {
        $w->error("Permission denied", $redirectUrl);
    }
}
