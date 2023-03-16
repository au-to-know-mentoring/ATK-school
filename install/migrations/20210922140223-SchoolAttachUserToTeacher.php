<?php

class SchoolAttachUserToTeacher extends CmfiveMigration
{
    public function up()
    {
        $this->addColumnToTable('school_teacher', 'user_id', 'integer', ['default' => null, 'null' => true]);
        $this->removeColumnFromTable("school_teacher", 'contact_id');
    }

    public function down()
    {
        // DOWN
        $this->addColumnToTable('school_teacher', 'contact_id', 'integer', ['default' => null, 'null' => true]);
        $this->removeColumnFromTable("school_teacher", 'user_id');
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
