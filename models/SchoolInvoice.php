<?php

class SchoolInvoice extends DbObject {

    public $invoice_number;
    public $student_id;
    public $status; // 'New', 'Sent', 'Paid'
    public $total_charge;
    public $dt_sent;
    public $dt_paid;
    
    public function getStudent() {
        return SchoolService::getInstance($this->w)->GetStudentForId($this->student_id);
    }

    public function getLineItems() {
        return SchoolService::getInstance($this->w)->getInvoiceLinesForInvoiceId($this->id);
    }

    public function updateTotal() {
        $invoice_lines = $this->getLineItems();
        $newTotal = 0;
        foreach ($invoice_lines as $line) {
            $newTotal += $line->amount;
        }
        $this->total_charge = $newTotal;
        $this->Update();
    }

}