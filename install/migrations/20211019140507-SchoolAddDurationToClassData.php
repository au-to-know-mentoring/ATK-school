<?php

class SchoolAddDurationToClassData extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('school_class_data', 'duration', 'integer', ['default' => null, 'null' => true]);
        $this->addColumnToTable('school_class_data', 'status', 'string', ['default' => null, 'null' => true]);

    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("school_class_data", 'duration');
        $this->removeColumnFromTable("school_class_data", 'status');
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
