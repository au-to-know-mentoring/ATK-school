<?php

class SchoolCustomCalendarSettings extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

        if (!$this->hasTable("school_custom_calendar_settings")) {
            $this->table("school_custom_calendar_settings", [
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
                ->addIdColumn('user_id')
                ->addStringColumn('custom_calendar_name')
                ->addBooleanColumn('is_view_calendar')
                ->addStringColumn('colour')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();
        }
    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_custom_calendar_settings') ? $this->dropTable('school_custom_calendar_settings') : null;
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
