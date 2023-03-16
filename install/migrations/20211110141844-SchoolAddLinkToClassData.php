<?php

class SchoolAddLinkToClassData extends CmfiveMigration
{
    public function up()
    {
        // UP
        $this->addColumnToTable('school_class_data', 'link', 'string', ['default' => null, 'null' => true]);

    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable("school_class_data", 'link');
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
