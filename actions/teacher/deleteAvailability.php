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
    var_dump($user->_roles[0]);
    // $availability->delete();
    // $w->msg("Item deleted", '/school-teacher/teachercalendar');
    // }
    // else {
    //     $w->msg("Permission denied", '/school-teacher/teachercalendar');
    // }
}




//     //check for permission
//     $user = AuthService::getInstance($w)->user();
//     if($user->hasRole("catalogue_admin")){
//         $item->delete();
//         $msg = "Item deleted";
//     } else {
//         $msg = "You do not have permission to delete";
//     }
//     $w->msg($msg, '/catalogue');
// }