<?php

class SchoolInvoiceLine extends DbObject {

    public $invoice_id;
    public $class_instance_id;
    public $amount;

    public function GetClassInstance() {
        return SchoolService::getInstance($this->w)->GetClassInstancesForId($this->class_instance_id);
    }


}