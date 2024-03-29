<?php $body = "
<env:Envelope
    xmlns:xsd='http://www.w3.org/2001/XMLSchema'
    xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
    xmlns:v1='http://ipg-online.com/ipgapi/schemas/ipgapi'
    xmlns:env='http://schemas.xmlsoap.org/soap/envelope/'>
  <env:Body>
    <ipgapi:IPGApiOrderRequest
        xmlns:v1='http://ipg-online.com/ipgapi/schemas/v1'
        xmlns:ipgapi='http://ipg-online.com/ipgapi/schemas/ipgapi'>
      <v1:Transaction>
        <v1:CreditCardTxType>
          <v1:Type>return</v1:Type>
        </v1:CreditCardTxType>
        <v1:Payment>
          <v1:ChargeTotal>$refund_amount</v1:ChargeTotal>
          <v1:Currency>840</v1:Currency>
        </v1:Payment>
        <v1:TransactionDetails>
          <v1:OrderId>$order_id</v1:OrderId>
        </v1:TransactionDetails>
      </v1:Transaction>
    </ipgapi:IPGApiOrderRequest>
  </env:Body>
</env:Envelope>";
?>