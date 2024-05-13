
<div class="tabs">
    <div class="tab-head">
        <a href="#details">Invoice Details</a>
        <a href="#attachments">Attachments</a>
    </div>
    <div class="tab-body">
        <div id="details" class="clearfix">


            <?php 
            $invoice_popup = $invoice->status == "New" ? 'Are you sure this invoice is correct?' : 'THIS INVOICE HAS BEEN SENT. do you want to continue?';
             
            //echo Html::b('/school-manager/renderInvoice/' . $invoice_id, 'Render Invoice');
            ///file/atfile/10
            echo Html::b('/school-manager/generateInvoicePDF/' . $invoice_id, 'Generate PDF');
            echo !empty($attachment_id) ? Html::b('/file/atfile/' . $attachment_id, 'Preview Invoice', null, null, true) : '';
            echo !empty($attachment_id) ? Html::b('/school-manager/invoiceSend/' . $invoice_id, 'Send Invoice', $invoice_popup) : '';
            echo (!empty($attachment_id) && $invoice->status == "Sent") ? Html::b('/school-manager/amendinvoice/' . $invoice_id, 'Amend Invoice') : '';
            ?>
            <?php echo $invoice_data; ?>
            <?php echo $lines_table; ?>
        </div>
        <div id="attachments">
            <?php echo $w->partial("listattachments", ["object" => $invoice, "redirect" => "school-manager/viewInvoice/{$invoice->id}#attachments"], "file"); ?>
        </div>
    </div>
</div>