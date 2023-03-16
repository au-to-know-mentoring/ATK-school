<?php

class SchoolAddInvoice extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

                if (!$this->hasTable("school_invoice")) { //it can be helpful to check that the table name is not used
                    $this->table("school_invoice", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addBigIntegerColumn('invoice_number')
                    ->addIdColumn('student_id')
                    ->addStringColumn('status')
                    ->addMoneyColumn('total_charge')
                    ->addDateTimeColumn('dt_sent')
                    ->addDateTimeColumn('dt_paid')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }

                if (!$this->hasTable("school_invoice_class")) { //it can be helpful to check that the table name is not used
                    $this->table("school_invoice_class", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addIdColumn('invoice_id')
                    ->addIdColumn('class_instance_id')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }

    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_invoice') ? $this->dropTable('school_invoice') : null;
        $this->hasTable('school_invoice_class') ? $this->dropTable('school_invoice_class') : null;
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
