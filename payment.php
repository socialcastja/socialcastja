<? include("ipg-utils.php"); ?>
     <html>
     <head><title>Processing</title></head>
       <body>
<form id="checkoutform" method="post" action="<?php echo getEndpoint(); ?>">
   <input type="hidden" name="txntype" value="sale">
   <input type="hidden" name="timezone" value="America/Jamaica"/>
   <input type="hidden" name="txndatetime" value="<?php echo getDateTime();?>"/>
   <input type="hidden" name="hash_algorithm" value="SHA256"/>
   <input type="hidden" name="hash" value="<?php echo createHash(getChargeTotal(), getCurrency()); ?>"/>
   <input type="hidden" name="storename" value="<?php echo getStoreId(); ?>" />
   <input type="hidden" name="oid" value="<?php echo $_GET['oid']; ?>" />
   <input type="hidden" name="bname" value="<?php echo $_GET['bname']; ?>" />
   <input type="hidden" name="hosteddataid" value="<?php echo $_GET['email']; ?>"/>
   <input type="hidden" name="customerid" value="<?php echo $_GET['customerid']; ?>"/>
   <input type="hidden" name="email" value="<?php echo $_GET['email']; ?>" />
   <input type="hidden" name="currency" value="<?php echo getCurrency(); ?>" />
   <input type="hidden" name="chargetotal" value="<?php echo getChargeTotal(); ?>"/>
   <input type="hidden" name="checkoutoption" value="combinedPage"/>
   <input type="hidden" name="language" value="en_US"/>
	<input type="hidden" name="dynamicMerchantName" value="SocialPay Billing"/>
	<input type="hidden" name="recurringInstallmentCount" value="<?php echo $_GET['payment_period']; ?>"/>
	<input type="hidden" name="recurringInstallmentPeriod" value="month"/>
	<input type="hidden" name="recurringInstallmentFrequency" value="1"/>
	<input type="hidden" name="recurringComments" value="Social Pay"/>
   <input type="hidden" name="responseFailURL" value="<?php echo urldecode($_GET['cancel_url']) . "\n";?>"/>
   <input type="hidden" name="responseSuccessURL" value="<?php echo urldecode($_GET['return_url']) ."\n";?>"/>
<!--<input type="submit" value="Submit">-->
</form>

<script type="text/javascript">
    document.getElementById('checkoutform').submit();
</script>
</body>
</html>