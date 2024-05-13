<?php

class SchoolSchoolAddZoomAccount extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

                if (!$this->hasTable("school_zoom_account")) { //it can be helpful to check that the table name is not used
                    $this->table("school_zoom_account", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addStringColumn('user_name')
                    ->addStringColumn('password')
                    ->addStringColumn('account_number')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }   
        



    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_zoom_account') ? $this->dropTable('school_zoom_account') : null;
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
