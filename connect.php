<?php

//
// initializing cURL with the IPG API URL:
if($this->testmode = 'no'){
 $curl = curl_init("https://www2.ipg-online.com/ipgapi/services");
}
    else{
        $curl = curl_init("https://test.ipg-online.com/ipgapi/services");
  
};

// setting the request type to POST:
curl_setopt($curl, CURLOPT_POST, true);

// setting the content type:
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

// setting the authorization method to BASIC:
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

// supplying your credentials:
curl_setopt($curl, CURLOPT_USERPWD, $this->publishable_key);

// filling the request body with your SOAP message:
curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

// telling cURL to verify the server certificate:
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);

// setting the path where cURL can find the certificate to verify the
// received server certificate against:
//curl_setopt($curl, CURLOPT_CAINFO, './tlstrust.pem');

// setting the path where cURL can find the client certificate:
curl_setopt($curl, CURLOPT_SSLCERT, ABSPATH.'/wp-content/plugins/socialPay/certs/WS7439420270019._.1.pem');

// setting the path where cURL can find the client certificateís
// private key:
curl_setopt($curl, CURLOPT_SSLKEY, ABSPATH.'/wp-content/plugins/socialPay/certs/WS7439420270019._.1.key');

// setting the key password:
curl_setopt($curl, CURLOPT_SSLKEYPASSWD, $this->private_key);

// telling cURL to return the HTTP response body as operation result
// value when calling curl_exec:
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

//LOG DE CONEXÃO CURL - Cria um arquivo TXT com o log da conexão
//curl_setopt($curl, CURLOPT_VERBOSE, true);
//$verbose = fopen('temp.txt', 'w+');
//curl_setopt($curl, CURLOPT_STDERR, $verbose);
if(isset($_POST)){
$result = curl_exec($curl);
//Erros e informação de conexão
//print_r(curl_errno($curl));
//print_r(curl_error($curl));
//print_r(curl_getinfo($curl), 1);

}
?>
