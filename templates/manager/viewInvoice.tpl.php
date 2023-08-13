
<div class="tabs">
    <div class="tab-head">
        <a href="#details">Invoice Details</a>
        <a href="#attachments">Attachments</a>
    </div>
    <div class="tab-body">
        <div id="details" class="clearfix">


            <?php echo Html::b('/school-manager/invoiceSend/' . $invoice_id, 'Send Invoice', 'Are you sure this invoice is correct?'); 
            echo Html::b('/school-manager/renderInvoice/' . $invoice_id, 'Render Invoice');
            echo Html::b('/school-manager/generateInvoicePDF/' . $invoice_id, 'Generate PDF');
            ?>
            <?php echo $invoice_data; ?>
            <?php echo $lines_table; ?>
        </div>
        <div id="attachments">
            <?php echo $w->partial("listattachments", ["object" => $invoice, "redirect" => "school-manager/viewInvoice/{$invoice->id}#attachments"], "file"); ?>
        </div>
    </div>
</div>