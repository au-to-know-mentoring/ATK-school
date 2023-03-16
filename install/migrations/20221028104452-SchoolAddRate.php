<?php

class SchoolAddRate extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('school_student', 'rate', 'decimal', ['precision' => 20, 'scale' => 2, 'default' => 0, 'null' => true]);

    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("school_student", 'rate');
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
