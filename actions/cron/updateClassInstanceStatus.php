<?php

function updateClassInstanceStatus_ALL(Web $w) {

    //get all class instances that are scheduled and in the past
    $class_instances = SchoolService::getInstance($w)->getPastClassInstancesByStatus("Scheduled");

    foreach($class_instances as $instance) {
        // echo $instance->getStudent()->getContact()->getFullName();
        // echo "<br>";
        // echo date('Y-m-d 00:00:00', $instance->dt_class_date);
        // echo "<br>";
        // echo $instance->status;
        // echo "<br><br>";
        $instance->status = "Completed";
        $instance->Update();
    }
    $w->msg("Instance status updated", "/school-manager/invoices");

}