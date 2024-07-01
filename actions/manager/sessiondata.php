<?php

function sessiondata_GET(Web $w)
{   
    if (!empty($_GET['teacher']))
    {
        $teacher = SchoolService::getInstance($w)->GetTeacherForId($_GET['teacher']);
        $class_Instances = $teacher->GetAllClassInstances();

    }else if(!empty($_GET['student']))
    {
        $student = SchoolService::getInstance($w)->GetStudentForId($_GET['student']);

        $class_Instances = $student->GetAllClassInstances();
    }else 
    {
        $class_Instances = SchoolService::getInstance($w)->GetAllClassInstances();
    }
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