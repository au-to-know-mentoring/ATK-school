<?php

class SchoolTeacher extends DbObject {

    public $user_id;
    public $max_students;
    public $state;
    public $timezone;
    public $d_acquired_date; // working with children check
    public $input_certnumber; // working with children check
    


    public function getDateAcquired() {
        //return formatDate($this->dt_class_date, 'H:i');
        if (!empty($this->d_acquired_date)) {
            return date('d/m/Y', $this->d_acquired_date);
        }
        return null;
    }
    public function getUser() {
        return AuthService::getInstance($this->w)->getUser($this->user_id);
    }

    public function getContact() {
        return $this->getUser()->getContact();
    }

    public function getFullName() {
        return $this->getContact()->getFullName();
    }

    public function getClassesCount() {
        $where = [
            "teacher_id"=>$this->id,
            "is_recurring"=>true,
            "is_deleted"=>false
        ];
        $classes = SchoolService::getInstance($this->w)->getObjects("SchoolClassData",$where);
        return empty($classes) ? '0' : Count($classes);
    }

    public function getSelectOptionTitle() {
        return $this->getFullName();
    }

}