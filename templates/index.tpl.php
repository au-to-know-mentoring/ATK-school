
<?php
    $user = AuthService::getInstance($w)->user();
    if ($user->hasRole('school_manager')) {
        echo Html::b("/school-manager/calendar","View Calendar");
        echo "<br><br>";
        echo Html::b("/school-manager/teacheredit","Add New Mentor");
        echo Html::b("/school-manager/teacherlist","Manage Mentors");
        echo "<br><br>";
        echo Html::b("/school-manager/studentedit","Add New Participant");

    }
    echo Html::b("/school-teacher/studentlist","Manage Participants");

?>

