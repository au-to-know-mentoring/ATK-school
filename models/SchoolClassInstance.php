<?php

class SchoolClassInstance extends DbObject {

    public $class_data_id;
    public $substitute_teacher_id;
    public $dt_class_date;
    public $status; // 'Scheduled', 'Late Cancel', 'Canceled', 'Completed'
    public $teachers_notes;
    
    public function getClassData() {
        return SchoolService::getInstance($this->w)->GetClassDataForId($this->class_data_id);
    }

    public function getTeacher() {
        if (!empty($this->substitute_teacher_id)) {
            return SchoolService::getInstance($this->w)->GetTeacherForId($this->substitute_teacher_id);
        } else {
            return $this->getClassData()->getTeacher();
        }
    }

    public function getStudent() {
        return $this->getClassData()->getStudent();
    }

    public function getStudentId() {
        return $this->getClassData()->getStudent()->id;
    }

    public function getStudentArrayKey() {
        $studentContact = $this->getStudent()->getContact();
        return $studentContact->firstname . $studentContact->lastname . $this->getStudentId();
    }

    public function getCalendarTitle() {
        // calendar title is form of "student name with teacher name"
        $class_data = $this->getClassData();
        $title = '';
        if ($this->status == 'Late Cancel') {
            $title .= 'LC ';
        } else if ($this->status == 'Scheduled') {
        } else if ($this->status == 'Canceled') {
            $title .= 'CANCELLED ';
        } else if ($this->status == 'Completed') {
            $title .= 'COMPLETED ';
        }
        // } elif ($this->status == 'scheduled')
        $title .= $class_data->getStudent()->getContact()->getFullName() . ' with ' . $this->getTeacher()->getContact()->getFullName();
        return $title; 
    }

    public function is_invoiced() {
        $invoice_link = SchoolService::getInstance($this->w)->getInvoiceLinkForClassINstanceId($this->id);
        if (!empty($invoice_link)) {
            return true;
        }
        return false;
    }

}