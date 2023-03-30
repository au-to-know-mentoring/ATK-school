<?php

class SchoolInitialMigration extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);

            
              
          
        if (!$this->hasTable("school_teacher")) { //it can be helpful to check that the table name is not used
            $this->table("school_teacher", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addStringColumn('state')
            ->addStringColumn('timezone')
            ->addIdColumn('user_id')
            ->addIntegerColumn('max_students')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }   

       
        

    

        if (!$this->hasTable("school_student")) { //it can be helpful to check that the table name is not used
            $this->table("school_student", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addDecimalColumn('rate')
            ->addStringColumn('state')
            ->addStringColumn('timezone')
            ->addIdColumn('contact_id')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }

      
       

        if (!$this->hasTable("school_class_data")) { //it can be helpful to check that the table name is not used
            $this->table("school_class_data", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addDecimalColumn('rate')
            ->addStringColumn('topic')
            ->addStringColumn('notes')
            ->addDateTimeColumn('dt_end_date')
            ->addStringColumn('link')
            ->addIntegerColumn('duration')
            ->addStringColumn('status')
            ->addIdColumn('student_id')
            ->addIdColumn('teacher_id')
            ->addDateTimeColumn('dt_class_date')
            ->addBooleanColumn('is_recurring')
            ->addStringColumn('frequency')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();
        }

        if (!$this->hasTable("school_class_instance")) { //it can be helpful to check that the table name is not used
            $this->table("school_class_instance", [ // table names should be appended with 'ModuleName_'
                "id" => false,
                "primary_key" => "id"
            ])->addColumn($column) // add the id column
            ->addIdColumn('class_data_id')
            ->addIdColumn('substitute_teacher_id')
            ->addDateTimeColumn('dt_class_date')
            ->addStringColumn('status')
            ->addStringColumn('teachers_notes')
            ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
            ->create();


           
        

            if (!$this->hasTable("school_student_contact_mapping")) { //it can be helpful to check that the table name is not used
                $this->table("school_student_contact_mapping", [ // table names should be appended with 'ModuleName_'
                    "id" => false,
                    "primary_key" => "id"
                ])->addColumn($column) // add the id column
                ->addStringColumn('notes')
                ->addBooleanColumn('is_secondary_contact')
                ->addStringColumn('contact_relationship')    
                ->addBooleanColumn('is_billing_contact')
                ->addIdColumn('student_id')
                ->addIdColumn('contact_id')
                ->addBooleanColumn('is_main_contact')
                ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                ->create();

                

                
                

                if (!$this->hasTable("school_teacher_availability")) { //it can be helpful to check that the table name is not used
                    $this->table("school_teacher_availability", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addIdColumn('object_id')
                    ->addStringColumn('object_type')
                    ->addDateTimeColumn('dt_start_time')
                    ->addDateTimeColumn('dt_end_time')
                    ->addStringColumn('type')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }

                if (!$this->hasTable("school_manager_settings")) { //it can be helpful to check that the table name is not used
                    $this->table("school_manager_settings", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    ->addIdColumn('user_id')
                    ->addStringColumn('state')
                    ->addStringColumn('timezone')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }


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


                
                if (!$this->hasTable("school_invoice_line")) { //it can be helpful to check that the table name is not used
                    $this->table("school_invoice_line", [ // table names should be appended with 'ModuleName_'
                        "id" => false,
                        "primary_key" => "id"
                    ])->addColumn($column) // add the id column
                    //->addBigIntegerColumn('invoice_number')
                    ->addIdColumn('invoice_id')
                    ->addIdColumn('class_instance_id')
                    //->addStringColumn('status')
                    ->addMoneyColumn('amount')
                    ->addCmfiveParameters() // this function adds some standard columns used in cmfive. dt_created, dt_modified, creator_id, modifier_id, and is_deleted.
                    ->create();
                }
            }
        }





    }

    public function down()
    {
        // DOWN
        $this->hasTable('school_teacher') ? $this->dropTable('school_teacher') : null;
        $this->hasTable('school_student') ? $this->dropTable('school_student') : null;
        $this->hasTable('school_class_data') ? $this->dropTable('school_class_data') : null;
        $this->hasTable('school_class_instance') ? $this->dropTable('school_class_instance') : null;
        $this->hasTable('school_teacher_availability') ? $this->dropTable('school_teacher_availability') : null;
        $this->hasTable('school_manager_settings') ? $this->dropTable('school_manager_settings') : null;
        $this->hasTable('school_invoice') ? $this->dropTable('school_invoice') : null;
        $this->hasTable('school_student_contact_mapping') ? $this->dropTable('school_student_contact_mapping') : null;

        
        

        $this->hasTable('school_invoice_line') ? $this->dropTable('school_invoice_line') : null;
       
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
