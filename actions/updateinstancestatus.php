<?php


function updateinstancestatus_ALL(Web $w) {
    //get all class instances that are in the past and set status to complete. 
    
    //start by getting all 'active' class data
    $active_classes = SchoolService::getInstance($w)->GetAllActiveClassData();

    $class_instances_to_update = [];
    foreach ($active_classes as $class_data) {
        $class_instances = SchoolService::getInstance($w)->getObjects('SchoolClassInstance', [
            'class_data_id' => $class_data->id,
            'status' => 'Scheduled',
            "dt_class_date <= ? "=> date('Y-m-d 00:00:00',strtotime('today')),
            'is_deleted' => 0
        ]);
        if (!empty($class_instances)) {
            foreach($class_instances as $class_instance) {
                $class_instances_to_update[] = $class_instance;
            }
        }
    }
    if (!empty($class_instances_to_update)) {
        foreach ($class_instances_to_update as $instance) {
            echo date('Y-m-d h:i', $instance->dt_class_date) . ' id = ' . $instance->id . ' classData id = ' . $instance->class_data_id . '</br>';
            $instance->status = 'Completed';
            $instance->Update();
        }
    }
    //var_dump($class_instances_to_update); die;
}