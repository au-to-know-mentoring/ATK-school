<?php

class SchoolAddTimezoneData extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('school_teacher', 'state', 'string', ['default' => null, 'null' => true]);
        $this->addColumnToTable('school_teacher', 'timezone', 'string', ['default' => null, 'null' => true]);

        $this->addColumnToTable('school_student', 'state', 'string', ['default' => null, 'null' => true]);
        $this->addColumnToTable('school_student', 'timezone', 'string', ['default' => null, 'null' => true]);

    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("school_teacher", 'state');
        $this->removeColumnFromTable("school_teacher", 'timezone');

        $this->removeColumnFromTable("school_student", 'state');
        $this->removeColumnFromTable("school_student", 'timezone');
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
