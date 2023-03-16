<?php

class SchoolUpdateAvailability extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('school_teacher_availability', 'object_type', 'string', ['default' => null, 'null' => true]);
        $this->addColumnToTable('school_teacher_availability', 'object_id', 'biginteger', ['default' => null, 'null' => true]);
        $this->removeColumnFromTable("school_teacher_availability", 'teacher_id');
    }

    public function down()
    {
        // DOWN
        $this->addColumnToTable('school_teacher_availability', 'type', 'string', ['default' => null, 'null' => true]);
        $this->addColumnToTable('school_teacher_availability', 'teacher_id', 'biginteger', ['default' => null, 'null' => true]);
        $this->removeColumnFromTable("school_teacher_availability", 'object_type');
        $this->removeColumnFromTable("school_teacher_availability", 'object_id');
    }

    public function preText()
    {
        return null;
    }

    public function postText()
    {
        return null;
    }

    public function description()
    {
        return null;
    }
}
