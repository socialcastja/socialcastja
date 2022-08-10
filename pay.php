<?php	

$protocols = array('http://', 'https://', 'https://www', '.', 'org', 'com', 'http://www.', 'www.');
$merchantsource= str_replace($protocols, '', get_bloginfo('wpurl'));
$spaycardnumber= str_replace(' ', '', $_POST['spay-card-number']);//"kris";//collects card number here
$exp = $_POST['spay-card-expiry'];//expiration date
$expd = explode("/", $exp);
$mm = str_replace(' ', '', $expd[0]);
$yyyy = str_replace(' ', '', $expd[1]);
$ccv=  $_POST['spay-card-cvc'];
$amt= WC()->cart->total;
$currency="840";
$bname=WC()->cart->get_customer()->get_billing_first_name()." ".WC()->cart->get_customer()->get_billing_last_name();
$bcountry = WC()->cart->get_customer()->get_shipping_country();
$bemail = WC()->cart->get_customer()->get_billing_email();
$order_id = $order->get_id();
//soap handling
$body = "<soapenv:Envelope 
            xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' 
            xmlns:ipgapi='http://ipg-online.com/ipgapi/schemas/ipgapi'>
<soapenv:Header/>
<soapenv:Body>
    <ipgapi:IPGApiOrderRequest 
        xmlns:ipgapi='http://ipg-online.com/ipgapi/schemas/ipgapi' 
        xmlns:v1='http://ipg-online.com/ipgapi/schemas/v1'>
       <v1:Transaction>
            <v1:CreditCardTxType>
                <v1:Type>sale</v1:Type>
            </v1:CreditCardTxType>
            <v1:CreditCardData>
                <v1:CardNumber>$spaycardnumber</v1:CardNumber>
                <v1:ExpMonth>$mm</v1:ExpMonth>
                <v1:ExpYear>$yyyy</v1:ExpYear>
                <v1:CardCodeValue>$ccv</v1:CardCodeValue>
            </v1:CreditCardData>
            <v1:Payment>
                <v1:ChargeTotal>$amt</v1:ChargeTotal>
                <v1:Currency>$currency</v1:Currency>
            </v1:Payment>
            <v1:TransactionDetails>
            <v1:OrderId>$order_id</v1:OrderId>
            <v1:InvoiceNumber>
            $this->d.$order_id
            </v1:InvoiceNumber>
            </v1:TransactionDetails>
                <v1:Billing>
                    <v1:CustomerID>$merchantsource</v1:CustomerID> 
                    <v1:Name>$bname</v1:Name> 
                    <v1:Country>$bcountry</v1:Country> 
                    <v1:Email>$bemail</v1:Email>
            </v1:Billing>
        </v1:Transaction>
            </ipgapi:IPGApiOrderRequest>
</soapenv:Body>
</soapenv:Envelope>";