<?php echo Html::b('/school-manager/invoiceSend/' . $invoice_id, 'Send Invoice', 'Are you sure this invoice is correct?'); 
echo Html::box('/school-manager/renderInvoice/' . $invoice_id, 'Render Invoice');
?>
<?php echo $invoice_data; ?>
<?php echo $lines_table; ?>