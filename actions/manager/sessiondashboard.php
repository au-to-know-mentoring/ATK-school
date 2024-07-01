<?php

function sessiondashboard_ALL(Web $w)
{   

    $class_Instances = SchoolService::getInstance($w)->GetAllClassInstances();
    $teachers = SchoolService::getInstance($w)->GetAllTeachers();
    $students = SchoolService::getInstance($w)->GetAllStudents();
    
    $TeacherSelect = [];
    $StudentSelect = [];

    // date picker in filter, sent results back to self 
   
    $w->ctx('TeacherSelect', Html::select('Filter', $teachers, '', 'TeacherFilter', '', "--Select Teacher--"));
    $w->ctx('StudentSelect', Html::select('Filter', $students, '', 'StudentFilter', '', "--Select Student--"));

    
    

    $InstaceTable = [];
    $InstaceTableHeaders = ['Mentor', 'Paticipent', 'Date', 'Start Time', 'Class Duration', 'Actions'];
    foreach ($class_Instances as $class_Instance){

            $class_data = $class_Instance->getClassData();
            $row = [];
            $row[] = $class_Instance->getTeacher()->getContact()->getFullName();
            $row[] = $class_Instance->getStudent()->getContact()->getFullName();
            $row[] = formatDate($class_Instance->dt_class_date, 'd/m/Y h:i', $class_Instance->class_date_timezone);
            $row[] = $class_data->duration;
            // $row[] = $class_data->rate;
            
            $actions = [];
            $actions[] = Html::b('/school-manager/classinstanceedit/' . $class_Instance->id, 'Edit');

            $row[] = implode(" ", $actions);
            $InstaceTable[] = $row;
    }

    $w->ctx('InstaceTable', Html::table($InstaceTable, null, 'tablesorter', $InstaceTableHeaders));


}