<?php

class SchoolAddAmendedInvoice extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
                ->setType('biginteger')
                ->setIdentity(true);


                $this->addColumnToTable('school_invoice', 'is_ammended', 'boolean', ['default' => false]);
                $this->addColumnToTable('school_invoice_line', 'duration', 'decimal', ['null' => true, 'precision' => 20, 'scale' => 10]);
                $this->addColumnToTable('school_invoice_line', 'dt_class_date', 'datetime', ['null' => true]);
                $this->addColumnToTable('school_invoice_line', 'rate', 'decimal', ['null' => true, 'precision' => 20, 'scale' => 2]);
                $this->addColumnToTable('school_class_instance', 'duration', 'decimal', ['null' => true, 'precision' => 20, 'scale' => 10]);
                $this->changeColumnInTable('school_class_data', 'duration', 'decimal', ['null' => true, 'precision' => 20, 'scale' => 10]);
                $this->addColumnToTable('school_invoice_line', 'invoice_line_item', 'string', ['null' => true]);
                //invoice_line_item
    }

    public function down()
    {
        // DOWN
        $this->removeColumnFromTable('school_invoice', 'is_ammended');
        $this->removeColumnFromTable('school_invoice_line', 'duration');
        $this->removeColumnFromTable('school_invoice_line', 'dt_class_date');
        $this->removeColumnFromTable('school_invoice_line', 'rate');
        $this->removeColumnFromTable('school_class_instance', 'duration');
        $this->removeColumnFromTable('school_invoice_line', 'invoice_line_item');
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
