<?php

class SchoolInvoiceLine extends DbObject {

    public $invoice_id;
    public $class_instance_id;
    public $dt_class_date;
    public $duration;
    public $rate;
    public $amount;
    public $invoice_line_item;

    public function GetClassInstance() {
        return SchoolService::getInstance($this->w)->GetClassInstancesForId($this->class_instance_id);
    }


}