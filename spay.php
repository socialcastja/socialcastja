<?php
/*
 * Plugin Name: socialPay_api
 * Plugin URI: https://socialcastja.com/plugins
 * Description: Take credit card payments on your store.
 * Author: Kristopher Kerr
 * Author URI: http://socialcastja.com
 * Version: 1.5
 *
 /*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
//include_once( 'base.php' );
 require __DIR__ . '/vendor/autoload.php';


/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_socialpayja_usd() {

    if ( ! class_exists( 'Appsero\Client' ) ) {
      require_once __DIR__ . '/appsero/src/Client.php';
    }

    $client = new Appsero\Client( '7635e9f5-908f-42a7-b540-88fd2e284ba8', 'Socialpayja-usd', __FILE__ );

    // Active insights
    $client->insights()->init();

    // Active automatic updater
    $client->updater();

}

appsero_init_tracker_socialpayja_usd(


 /* This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'spay_add_gateway_class' );
function spay_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_spay_Gateway'; // your class name is here
	return $gateways;
}
// foreach(glob('/wp-content/plugins/socialPay/certs/*.key') as $key){
   // echo $key."<br/>";
//}
//foreach(glob('/wp-content/plugins/socialPay/certs/*.pem') as $pem){
  //  echo $pem."<br/>";
//}
/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'spay_init_gateway_class' );
function spay_init_gateway_class() {
 
	class WC_spay_Gateway extends WC_Payment_Gateway {
 
 		/**
 		 * Class constructor, more about it in Step 3
 		 */
public function __construct() {
 
	$this->id = 'spay'; // payment gateway plugin ID
	$this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
	$this->has_fields = true; // in case you need a custom credit card form
	$this->method_title = 'SPAY Gateway';
	$this->method_description = 'Description of spay payment gateway'; // will be displayed on the options page
 
	// gateways can support subscriptions, refunds, saved payment methods,
	// but in this tutorial we begin with simple payments
/*	$this->supports = array(
		'products'
	);*/
 $this->supports = array(
            'subscriptions',
            'products',
            'subscription_cancellation',
            'subscription_reactivation',
            'subscription_suspension',
            'subscription_amount_changes',
            'subscription_payment_method_change',
            'subscription_date_changes',
            'default_credit_card_form',
            'refunds'
);

 
	// Method with all the options fields
	$this->init_form_fields();
 
	// Load the settings.
	$this->init_settings();
	$this->title = $this->get_option( 'title' );
	$this->description = $this->get_option( 'description' );
	$this->enabled = $this->get_option( 'enabled' );
	$this->testmode = 'yes' === $this->get_option( 'testmode' );
	$this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
	$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
 
	// This action hook saves the settings
	add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
 
	// We need custom JavaScript to obtain a token
	add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
 
	// You can also register a webhook here
	// add_action( 'woocommerce_api_{webhook name}', array( $this, 'webhook' ) );
 }
 
		/**
 		 * Plugin options, we deal with it in Step 3 too
 		 */
 	public function init_form_fields(){
 
	$this->form_fields = array(
		'enabled' => array(
			'title'       => 'Enable/Disable',
			'label'       => 'Enable spay Gateway',
			'type'        => 'checkbox',
			'description' => '',
			'default'     => 'no'
		),
		'title' => array(
			'title'       => 'Title',
			'type'        => 'text',
			'description' => 'This controls the title which the user sees during checkout.',
			'default'     => 'Credit Card',
			'desc_tip'    => true,
		),
		'description' => array(
			'title'       => 'Description',
			'type'        => 'textarea',
			'description' => 'This controls the description which the user sees during checkout.',
			'default'     => 'Pay with your credit card via our super-cool payment gateway.',
		),
		'testmode' => array(
			'title'       => 'Test mode',
			'label'       => 'Enable Test Mode',
			'type'        => 'checkbox',
			'description' => 'Place the payment gateway in test mode using test API keys.',
			'default'     => 'yes',
			'desc_tip'    => true,
		),
		'test_publishable_key' => array(
			'title'       => 'Test Publishable Key',
			'type'        => 'text'
		),
		'test_private_key' => array(
			'title'       => 'Test Private Key',
			'type'        => 'text',
		),
		'publishable_key' => array(
			'title'       => 'storename and Password',
			'type'        => 'text'
		),
		'private_key' => array(
			'title'       => 'Live Private Key',
			'type'        => 'text'
		)
	);
}
 
		/**
		 * You will need it if you want your custom credit card form, Step 4 is about it
		 */
	public function payment_scripts() {
 
	// we need JavaScript to process a token only on cart/checkout pages, right?
	if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
		return;
	}
 
	// if our payment gateway is disabled, we do not have to enqueue JS too
	if ( 'no' === $this->enabled ) {
		return;
	}
 
	// no reason to enqueue JavaScript if API keys are not set
	if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
		return;
	}
 
	// do not work with card detailes without SSL unless your website is in a test mode
	if ( ! $this->testmode && ! is_ssl() ) {
		return;
	}
 
	// let's suppose it is our payment processor JavaScript that allows to obtain a token
	//wp_enqueue_script( 'spay_js', 'https://socialcastja.com/wp-content/plugin/socialPay/includes/config/payment.php' );
 
	// and this is our custom JS in your plugin directory that works with token.js
	//wp_register_script( 'woocommerce_spay', plugins_url( 'spay.js', __FILE__ ), array( 'jquery', 'spay_js' ) );
 
	// in most payment processors you have to use PUBLIC KEY to obtain a token
	wp_localize_script( 'woocommerce_spay', 'spay_params', array(
		'publishableKey' => $this->publishable_key
	) );
 
	wp_enqueue_script( 'woocommerce_spay' );
 
}
 
		/*
		 * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
		 */
	public function payment_fields() {
 
	// ok, let's display some description before the payment form
	if ( $this->description ) {
		// you can instructions for test mode, I mean test card numbers etc.
		if ( $this->testmode ) {
			$this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
			$this->description  = trim( $this->description );
		}
		// display the description with <p> tags etc.
		echo wpautop( wp_kses_post( $this->description ) );
	}
 
	// I will echo() the form, but you can close PHP tags and print it directly in HTML
	echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';
 
	// Add this action hook if you want your custom payment gateway to support it
	do_action( 'woocommerce_credit_card_form_start', $this->id );
 
	// I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
      $cc_form = new WC_Payment_Gateway_CC();
      $cc_form->id       = $this->id;
      $cc_form->supports = $this->supports;
      $cc_form->form();
}

 
		/*
 		 * Fields validation, more in Step 5
		 */
public function validate_fields(){
 
	if( empty( $_POST[ 'billing_first_name' ]) ) {
		wc_add_notice(  'First name is required!', 'error' );
		return false;
	}
	return true;
 
}
 
		/*
		 * We're processing the payments here, everything about it is in Step 5
		 */

public function process_payment( $order_id ) {
 
	global $woocommerce;
 
	// we need it to get any order detailes
	$order = wc_get_order( $order_id );
 
 
	/*
 	 * Array with parameters for API interaction
	 */
	
	
	
	if ( is_checkout() ) {

    include ABSPATH.'/wp-content/plugins/socialPay/pay.php';
	}	
//	else 
//
// initializing cURL with the IPG API URL:


 $curl = curl_init("https://www2.ipg-online.com/ipgapi/services");
 
foreach(glob(ABSPATH.'wp-content/plugins/socialPay/certs/*.key') as $key){
   // echo $key."<br/>";
}
foreach(glob(ABSPATH.'wp-content/plugins/socialPay/certs/*.pem') as $pem){
  //  echo $pem."<br/>";
}
// setting the request type to POST:
curl_setopt($curl, CURLOPT_POST, true);

// setting the content type:
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));

// setting the authorization method to BASIC:
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

// supplying your credentials:
curl_setopt($curl, CURLOPT_USERPWD, "WS7439420270019._.1:Get@life123");

// filling the request body with your SOAP message:
curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

// telling cURL to verify the server certificate:
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);

// setting the path where cURL can find the certificate to verify the
// received server certificate against:
//curl_setopt($curl, CURLOPT_CAINFO, './tlstrust.pem');

// setting the path where cURL can find the client certificate:
curl_setopt($curl, CURLOPT_SSLCERT, $pem);

// setting the path where cURL can find the client certificateís
// private key:
curl_setopt($curl, CURLOPT_SSLKEY, $key);

// setting the key password:
curl_setopt($curl, CURLOPT_SSLKEYPASSWD,"qM<n2c4(ju");

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
//wc_add_notice("pemfile: ".$pem." keyfile: ".$key." passpharse: ".$this->private_key."Publishkey:".$this->publishable_key." this is curl".curl_error($curl));

$trnsdetaild = explode(" ", $result);
$a=$trnsdetaild[0];
$b=$trnsdetaild[1];
$c=$trnsdetaild[2];
$d=$trnsdetaild[3];
$e=$trnsdetaild[4];
$f=$trnsdetaild[5];
$g=$trnsdetaild[6];
$h=$trnsdetaild[7];
$i=$trnsdetaild[8];
	/*
	 * Your API interaction could be built with wp_remote_post()
 	 */
	//$response = wp_remote_post( '{payment processor endpoint}', $args );
 
 
//if( !is_wp_error( $response ) ) {
 
//		 $result = json_decode( $response['body'], true );
//	 }
		 // it could be different depending on your payment processor
		 if (strpos($result, 'APPROVED') !== false) { 
 
			// we received the payment
			$order->payment_complete($transaction_id);
			$order->reduce_order_stock();
			//kris added
			$order->update_status( 'completed' );
 
			// some notes to customer (replace true with false to make it private)
			$order->add_order_note( 'Hey, your order is paid! Thank you!', true );
             $order->add_order_note(__('Name on Card: ' . $bname .  ' '));
             $order->add_order_note(__('Card Type: ' . $ccbrand .  ' '));
			// Empty cart
			$woocommerce->cart->empty_cart();
 
			// Redirect to the thank you page
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url( $order )
			);
		 
 
		 } 
		 
		 else {
                // Mark as on-hold (we're awaiting the payment)
                $order->update_status( 'on-hold', __( 'Awaiting payment from socialpay', 'wc-socialpay-hosted-gateway' ) );
			wc_add_notice('Transaction Declined Please check card or try another.', 'error' );
			return;
		}
	wc_print_notices('$result');

}


		/*
		 * In case you need a webhook, like PayPal IPN etc
		 */
		public function webhook() {
 
	$order = wc_get_order( $_GET['id'] );
    $order->payment_complete();
    //$order->payment_complete( $transaction_id );
	$order->reduce_order_stock();
	$order->add_order_note(__('Name on Card: ' . $bname[1] .  ' '));
    $order->add_order_note(__('Card Type: ' . $ccbrand[1] .  ' '));
 
	update_option('webhook_debug', $_GET);
}
 	}
}
add_filter ('woocommerce_gateway_icon', 'custom_woocommerce_icons');
 
function custom_woocommerce_icons() {
    $icon  = '<img src="/wp-content/plugins/socialPay/svg/visa.svg' . '" alt="Visa" style="width: 90px; display: inline-flex;" />';
    $icon .= '<img src="/wp-content/plugins/socialPay/svg/mastercard.svg' . '" alt="Mastercard" style="width: 90px; display: inline-flex;"/>';
    $icon .= '<img src="/wp-content/plugins/socialPay/svg/discover.svg' . '" alt="Discover" style="width: 90px; display: inline-flex;"/>';$icon .= '<img src="https://socialpayja.com/public/sites/8/13/images//uploaded/8/social%20pay.png' . '" alt="Spay" style="width: 90px; display: inline-flex;"/>';
 
    return $icon;
}
	);
