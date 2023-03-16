<?php

class SchoolMoveRateToClassData extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('school_class_data', 'rate', 'decimal', ['precision' => 20, 'scale' => 2, 'default' => 0, 'null' => true]);
        $this->removeColumnFromTable("school_student", 'rate');

    }

    public function down()
    {
        // DOWN
        $this->addColumnToTable('school_student', 'rate', 'decimal', ['precision' => 20, 'scale' => 2, 'default' => 0, 'null' => true]);
        $this->removeColumnFromTable("school_class_data", 'rate');
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
