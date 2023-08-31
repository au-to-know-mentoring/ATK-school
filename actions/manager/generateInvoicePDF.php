<?php

function generateInvoicePDF_ALL(Web $w) {

    $w->setLayout(null);
    //check if user is a manager
    $user = AuthService::getInstance($w)->user();
    if (!$user->hasRole("school_manaer")) {
        $w->error("you do not have permission to complate this action", "/school");
    }
    
    $p = $w->pathMatch("invoice_id");
    if (empty($p['invoice_id'])) {
        $w->error("no invoice id provided", "school-manager/invoices");
    }

    $invoice = SchoolService::getInstance($w)->getInvoiceForId($p['invoice_id']);
    if (empty($invoice)) {
        $w->error("no invoice found for id", "school-manager/invoices");
    }

    //get the student and billing contact
    $student = $invoice->getStudent();
    $billingContact = $student->getBillingContact();

    //get invoice line items
    $invoiceLineItems = $invoice->getLineItems();

    //get the template
    $template = TemplateService::getInstance($w)->findTemplate('school', 'invoice');

    //prepare the data
    $templateData = [];
    $invoiceTotal = 0;
    $templateData['invoice_number'] = $invoice->id;
    $templateData['billing_contact_name'] = $billingContact->getFullName();
    $templateData['billing_contact_email'] = $billingContact->email;
    $templateData['student_name'] = $student->getContact()->getFullName();
    $templateData['invoice_lines'] = [];
    foreach ($invoiceLineItems as $lineItem) {
        $class_instance = $lineItem->GetClassInstance();
        $class_data = $class_instance->getClassData();
        $line = [];
        $line['session_date'] = $class_instance->GetFormattedDate();
        $line['duration'] = $class_data->duration;
        $line['rate'] = $class_data->rate;
        $line['total'] = $lineItem->amount;
        $templateData['invoice_lines'][] = $line;
        $invoiceTotal += $lineItem->amount;
    }
    $templateData['invoice_total'] = $invoiceTotal;

    $invoice_filename = "Invoice_";
    //$invoice->invoice_number     00000     00001   00100
    $formattedInvoiceNumber = (string) $invoice->id;
    for ($i = 0; $i < 5 - strlen($formattedInvoiceNumber); $i++) {
        $invoice_filename .= "0";
    }

    $invoice_filename .= $invoice->id . ".pdf";


    class MYPDF extends TCPDF
    {
        // Page footer
        public function Footer()
        {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            // Set font
            $this->SetFont('helvetica', 'I', 8);
            // Page number
            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }

    $pdf = new MYPDF();
    $pdf->AddPage();
    $pdf->writeHTML($template->renderBody($templateData), true, false, true, false, '');

    //generate the file path
    $filesystemPath = "attachments/" . $invoice->getDbTableName() . '/' . date('Y/m/d') . '/' .             $invoice->id . '/';
    $filesystem = FileService::getInstance($w)->getFilesystem(FileService::getInstance($w)->getFilePath($filesystemPath));
    if (empty($filesystem)) {
        LogService::getInstance($w)->setLogger("SCHOOL_INVOICE")->error("Cannot save file, no filesystem returned");
        return null;
    }
    $fullpath = FILE_ROOT . str_replace(FILE_ROOT, "", $filesystemPath . $invoice_filename);
    //var_dump(FILE_ROOT . str_replace(FILE_ROOT, "", $filesystemPath . $invoice_filename)); 
    //$fullpath = ltrim($fullpath, '/');

    //ob_clean();
    //$pdf->Output($fullpath, 'F');
    $attachments = FileService::getInstance($w)->getAttachments($invoice);
    if (!empty($attachments)) {
        //remove attachments with matching name
        foreach ($attachments as $attachment) {
            // echo $attachment->filename;
            // echo '</br>';
            // echo $invoice_filename;
            // echo '</br>';
            if ($attachment->filename == $invoice_filename) {
                //delete attachment
                $attachment->delete();
            }
        }
    }
    

    FileService::getInstance($w)->saveFileContent($invoice, $pdf->Output("", 'S'), $invoice_filename, $type_code = null, $content_type = null, $description = null);

    $w->msg("Invoice PDF Generated", "/school-manager/viewInvoice/" . $invoice->id . "#attachments");
}