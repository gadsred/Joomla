<?php
/**
 * @version     1.0.0
 * @package     com_financial
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      gadiel_Rojo <gadsred@gmail.com> - http://
 */
// no direct access
defined('_JEXEC') or die;

$canEdit = JFactory::getUser()->authorise('core.edit', 'com_financial');
if (!$canEdit && JFactory::getUser()->authorise('core.edit.own', 'com_financial')) {
	$canEdit = JFactory::getUser()->id == $this->item->created_by;
}

$invoice=$this->item->statement;
?>


<?php if ($this->item) : ?>

    <div class="item_fields hidden">
        <table class="table">
            <tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_ID'); ?></th>
			<td><?php echo $this->item->id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_STATE'); ?></th>
			<td>
			<i class="icon-<?php echo ($this->item->state == 1) ? 'publish' : 'unpublish'; ?>"></i></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_CREATED_BY'); ?></th>
			<td><?php echo $this->item->created_by_name; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_INVOICE_ID'); ?></th>
			<td><?php echo $this->item->invoice_id; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_DATE'); ?></th>
			<td><?php echo $this->item->date; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_CHARGES'); ?></th>
			<td><?php echo $this->item->charges; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_PAYMENTS'); ?></th>
			<td><?php echo $this->item->payments; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_PAID'); ?></th>
			<td><?php echo $this->item->paid; ?></td>
</tr>
<tr>
			<th><?php echo JText::_('COM_FINANCIAL_FORM_LBL_PASTINVOICE_PAID_ON'); ?></th>
			<td><?php echo $this->item->paid_on; ?></td>
</tr>

        </table>
    </div>
	
    <?php if($canEdit): ?>
		
	<?php endif; ?>
	<?php if(JFactory::getUser()->authorise('core.delete','com_financial')):?>
		
	<?php endif; ?>
    <?php
		else:
			echo JText::_('COM_FINANCIAL_ITEM_NOT_LOADED');
		endif;
		?>
	<button class="btn" onClick="javascript:window.history.back();">Go Back to Invoices Page</button>
	<button class="btn" onClick="javascript:window.print();">Print Invoice</button>
<table style="width:100%;margin:0 auto;border:1px solid #e5e5e5;font-family:Arial;border-collapse: collapse;">
    <tr>
        <td style="text-align:center;"><img src="<?php echo JURI::base();?>images/logo.png" alt="Property Conveyancing Directory"></td>
    </tr>
    
    <tr>
        <td style="text-align:center;border-top:2px solid #f7f7f7; border-bottom:2px solid #f7f7f7;"> 
			<?php //echo $invoice->customer_organization.' Property Conveyancing Directory - PO BOX '. $invoice->customer_billing_address.', '. $invoice->customer_billing_city.' '.$invoice->customer_billing_state.' '.$invoice->customer_billing_zip.', '. $invoice->customer_billing_country;?>  
			Property Investors Only Pty Ltd (ACN 154 091 031) T/A Property Conveyancing Directory - PO BOX 517, Ascot Vale, Vic 3032
		</td>
    </tr>
   
    <tr>
        <td>&nbsp;</td>
    </tr>
    
    <tr>
        <td style="text-align:center;background-color:#e5e5e5;font-weight:bold;">
            Tax Invoice: <?php echo $invoice->id;?>
        </td>
    </tr>
    
    <tr>
        <td  style="text-align:right;">
            <?php echo $invoice->customer_first_name." ".$invoice->customer_last_name;?><br/>
            <?php echo $invoice->customer_organization;?><br/>
			<?php 
				$invalid_address = preg_match('/update/',$invoice->customer_billing_address);
				if($invoice->customer_billing_address!='-' && $invalid_address!=1)
					{?>
						<span style="font-style:italic;">
							<?php echo $invoice->customer_billing_address;?>
						</span><br/>
			<?php	}?>
			<?php 
				$invalid_city = preg_match('/update/',$invoice->customer_billing_city);
				$invalid_state = preg_match('/update/',$invoice->customer_billing_state);
				if(($invoice->customer_billing_city!='-' || $invoice->customer_billing_state!='-') && ($invalid_city!=1 && $invalid_state!=1))
					{?>
						<span style="font-style:italic;">
							<?php echo ($invoice->customer_billing_city=='-') ? '' : $invoice->customer_billing_city." "; echo ($invoice->customer_billing_state=='-') ? '' : $invoice->customer_billing_state;?>
						</span><br/>
			<?php	}?>
            <span style="font-style:italic;"><?php echo $invoice->customer_billing_country." "; echo ($invoice->customer_billing_zip=='0000') ? '' : $invoice->customer_billing_zip;?></span><br/>
        </td>
    </tr>
    
    <!--<tr>
        <td style="text-align:left;font-style:italic;">
        <?php //echo $invoice->customer_organization;?><br/>
       <?php //echo $invoice->customer_billing_address.', '. $invoice->customer_billing_city.' '.$invoice->customer_billing_state.' '.$invoice->customer_billing_zip.', '. $invoice->customer_billing_country;?>
        </td>        
    </tr>-->
    
    <tr>
        <td style="background-color:#e5e5e5;font-weight:bold;text-align:center;">
            Summary
        </td>
    </tr>
    
    <tr>
        <td  style="text-align:right;">
            <?php 
      			$date = date_create(substr($invoice->updated_at,0,10));
      			echo date_format($date,"F, d Y");
      		?>
      		<br/>
      		Reference: <?php echo $invoice->id;?>
        </td>
    </tr>
    
    <tr>
        <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin:0;padding:0;border:none;">
                <thead>
                    <tr  style="border-bottom:2px solid #e5e5e5;background-color:none;">
                        <th>Date</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $trans = $invoice->transactions;
                    $i = 0;
                    for ($i = 0; $i < count($trans) - 1; $i++) {
                        $tran = $trans[$i];
                        $date_at = strtotime($tran->created_at);
                        
                        echo '<tr>';
                        echo '<td>'.date("Y-m-d H:i:s T", $date_at).'</td>';
                        echo '<td>'.$tran->type.'</td>';
                        echo '<td>'.$tran->memo.'</td>';
                        echo '<td>$'.number_format($tran->amount_in_cents/100, 2, '.', '');
                        echo '</tr>';
                    }
                    $i = count($trans) - 1;
                    $tran = $trans[$i];
                    ?>
                    <tr style="background-color:#eeeeee;">
                        <td colspan="3" style="text-align:right;"><span style="font-weight:bold;padding-right:20px;">Total Charges:</span></td>
                        <td><span style="font-weight:bold;">$<?php echo number_format($tran->amount_in_cents/100, 2, '.', '');?></span></td>
                    </tr>
                    
                    <tr>
                        <td>
                        <?php
                        	$date_at = strtotime($tran->created_at);
                        	echo date("Y-m-d H:i:s T", $date_at);
                        ?></td>
                        <td><?php echo $tran->type;?></td>
                        <td><?php echo $tran->memo;?></td>
                        <td>$<?php echo number_format($tran->amount_in_cents/100, 2, '.', '');?></td>
                    </tr>
                    
                    <tr>
                        <td colspan="3" style="text-align:right;"><span style="font-weight:bold;padding-right:20px;">Total Payments:</span></td>
                        <td><span style="font-weight:bold;">$<?php echo number_format($tran->amount_in_cents/100, 2, '.', '');?></span></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
