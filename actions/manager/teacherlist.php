<?php

function teacherlist_ALL(Web $w)
{
    $w->ctx("title", "Manage Mentors");

    $teachers = SchoolService::getInstance($w)->GetAllTeachers();

    $headers = ["Name","Number of Classes","Remaining Capacity","Actions"];

    $tableData = [];

    if (!empty($teachers)) {
        foreach ($teachers as $teacher) {
            $row = [];
            $row[] = $teacher->getFullName();
            $classesCount = $teacher->getClassesCount();
            $row[] = $classesCount;
            $row[] = $teacher->max_students - $classesCount;
            $actions = [];
            $actions[] = Html::b("/school-manager/teacheredit/" . $teacher->id, "Edit");
            $actions[] = Html::b("/school-manager/teacherview/" . $teacher->id, "View Details");
            $actions[] = Html::b("/school-manager/teacherdelete/" . $teacher->id, "Delete", "Are you sure you want to delete this Mentor?", null, false, "warning");
            $row[] = implode("", $actions);
            $tableData[] = $row;

        }
    }
// var_dump($tableData); die;
    $w->ctx("teacherTable", Html::table($tableData, null, "tablesorter", $headers));

    
}