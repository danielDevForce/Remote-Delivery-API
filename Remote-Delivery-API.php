<?php
/*
Plugin Name: Remote Delivery API
Plugin URI: devforce.co.il
Description: Remote Delivery API
Author: Devforce
Version: 1.0.0
*/
require __DIR__ . '/vendor/autoload.php';
use Automattic\WooCommerce\HttpClient\HttpClientException;
use Automattic\WooCommerce\Client;

    class RemoteOrder {
        /* Member variables */
        public  $id;
        public  $shareToken;
        public  $items;
        //add charges
        public  $comment;
        public  $created;
        public  $modified;
        public  $deliveryDate;
        public  $formatCreatedDate;      //// formatted yyyy-MM-dd HH:mm:ss
        public  $formatDeliveryDate;     //// formatted yyyy-MM-dd HH:mm:ss
        public  $status;
        public  Contact $contact;
        public  $deliveryType;          //0-delivery 1-takeout
        public  $servingType;           //(0- SIT, 1-TA, 2-DELIVERY) not mandatory, default value is 2-DELIVERY
        public  Address $address;
        //add payments

        function __construct($id, $shareToken, $items, $formatCreatedDate, $formatDeliveryDate , $status, Contact $contact, Address $address, $deliveryType = 0, $servingType = 2 ) {
            $this->id           = $id;
            $this->shareToken   = $shareToken;
            $this->items        = $items;
            $this->formatCreatedDate  = $formatCreatedDate;
            $this->formatDeliveryDate = $formatDeliveryDate;
            $this->status       = $status;
            $this->contact = $contact;
            $this->address = $address;
          }
    }

    class Contact{

        public  $id;
        public  $firstName;
        public  $lastName;
        public  $email;
        public  $phone;
        public  $fax;

        function __construct($firstName, $lastName, $phone, $email="", $fax="") {
            //$this->id        = $id;
            $this->firstName = $firstName;
            $this->lastName  = $lastName;
            $this->email     = $email;
            $this->phone     = $phone;
            $this->fax       = $fax;
        }
        public function __toString()
        {
            $str = "id:" . $this->id 
            . "\nfirstName:" . $this->firstName 
            . "\nlastName:" . $this->lastName
            . "\nemail:" . $this->email
            . "\nphone:" . $this->phone;
            empty($this->fax) ? "" :  $str.="\nfax:" . $this->fax;
            
            return  $str;
        }

    }  
    class Item{
        public $id;
        public $desc;
        //public $group;
        public $price;
        public Variation $variations;
        public $status;
        //public $comment;
        public $count;
        public $discountable;
        public $type;
        public $discountAmountRuleType;
        public $discountAmount;

        function __construct($id, $desc, $price, $variations, $status, $count = 0, $discountable = false, $type=0, $discountAmountRuleType = 0, $discountAmount=0) {
            $this->id           = $id;
            $this->desc         = $desc;
            $this->price        = $price;
            $this->variations   = $variations;
            $this->status       = $status;
            $this->count        = $count;
            $this->discountable = $discountable;
            $this->type         = $type;
            $this->discountAmountRuleType = $discountAmountRuleType;
            $this->discountAmount = $discountAmount;
          }

          


    }
    class Variation{
        public  $items = array();
        public $level;
        public $maxNumAllowed;

        function __construct($items = array(), $level="", $maxNumAllowed=1){
            $this->items = $items;
            $this->level = $level;
            $this->maxNumAllowed = $maxNumAllowed;
        }
        function add_item_to_variation($item){
            array_push($this->items, $item);
        }
    }
    class Address{
        public  $country;
        public  $city;
        public  $street;
        public  $apt;
        public  $entrance;
        public  $comment;
        public  $lat;
        public  $lng;
        public  $postalCode;

        function __construct($city, $street, $apt, $postalCode=0, $country = "IL", $lat="", $lng="") {
            $this->city       = $city;
            $this->street     = $street;
           // $this->number     = $number;
            $this->apt        = $apt;
            //$this->floor      = $floor;
            //$this->entrance   = $entrance;
            $this->country    =$country;
            $this->postalCode = $postalCode;
            $this->lat        = $lat;
            $this->lng        = $lng;

          }
    }

    abstract class PaymentMethod{
        const cash = 0;
        const credit_card = 1;
        const cibus = 2;
        const tenBis = 3;
        const multipass = 4;
        const paypal = 5;
        const paidit = 6;
        const beengo = 7;
        const bitetech = 8;
    }

    class Payment{
        public  $paymentMethod;
        public  $amount; //in agorot
        public CreditCard $card;

        function __construct($paymentMethod, $amount, CreditCard $card) {
            $this->paymentMethod =    PaymentMethod:: $paymentMethod;
            $this->amount          = $amount;
            $this->card            = $card;
          }
        
          public function __toString()
          {
              return $this->$paymentMethod;
          }

    }

    class CreditCard{
        public  $number;
        public  $expireMonth;
        public  $expireYear;
        public  $holderId;
        public  $holderName;
        public  $billingAddress;
        public  $billingPostalCode;
        public  $CVV;
        public  $token;

        public function __construct($number, $expireMonth, $expireYear, $CVV, $token, $holderId="", $holderName="", $billingAddress="", $billingPostalCode="") {
            $this->number            = $number;
            $this->expireMonth       = $expireMonth;
            $this->expireYear        = $expireYear;
            $this->holderId          = $holderId;
            $this->holderName        = $holderName;
            $this->billingAddress    = $billingAddress;
            $this->billingPostalCode = $billingPostalCode;
            $this->CVV               = $CVV;
            $this->token             = $token;
          }
        public function __toString()
            {
              $str = "number:" . $this->number 
              . "\nexpireMonth:" . $this->expireMonth 
              . "\nexpireYear:" . $this->expireYear
              . "\nCVV:" . $this->CVV;
              empty($this->holderId) ? "" :  $str.="\nholderId:" . $this->holderId;
              empty($this->holderName) ? "" : $str.="\nholderName:" . $this->holderName;
              empty($this->billingAddress) ? "" : $str.="\nbillingAddress:" . $this->billingAddress ;
              empty($this->billingPostalCode) ?  "" : $str.="\nbillingPostalCode:" . $this->billingPostalCode  ;
              empty($this->token) ? "" : $str.="\ntoken:" . $this->token ;
              echo $this->token ;
              return  $str;

          }
    }
    class group{
        public  $id;
        public  $name;

        public function __construct($id, $name){
            $this->id = $id;
            $this->name = $name;
        }
    }
/** 
 * connects to the woocommece api, returns a woocommerce object
 * 
 * @param $url - the sites url
 * @param $publicKey - public key of woocommerce api
 * @param $secretKey - secret key of woocommerce api
 */
function connect_to_woocommerce_api($url, $publicKey, $secretKey ){
    $woocommerce = new Client(
        $url, 
        $publicKey, 
        $secretKey,
        [
            'version' => 'wc/v3',
        ]
        
    );
    return $woocommerce;
}


/**
 *  returns an address object from the order
 * 
 * @param array $order - the whole order gotten from woocommerce api
 */
function get_address_from_order($order) :Address
{
    empty($order[0]->shipping->first_name) ? $billing = $order[0]->billing : $billing = $order[0]->shipping;
    
    $address = new Address($billing->city,$billing->address_1,empty($billing->address_2) ? "" : $billing->address_2,$billing->postcode);
    //var_dump($address);
    return $address;

}
/**
 *  returns an contact object from the order
 * 
 * @param array $order - the whole order gotten from woocommerce api
 */
function get_contact_from_order($order) :Contact {
    $billing = $order[0]->billing;
    $contact = new Contact($billing->first_name, $billing->last_name, $billing->phone, $billing->email);
    //var_dump($contact);
    return $contact;
}

/**
 *  returns an item object from the order
 * 
 * @param array $order - the whole order gotten from woocommerce api
 */
function get_items_from_order($order)// : Item
 {
    $level = 1;
    $newItems = array();
    $items = $order[0]->line_items;
    foreach($items as $item){
        $new_item = new Item(

            $item->product_id,
            $item->name,
            $item->total,
            get_variations_from_order($item->meta_data, $level),
            0,
            $item->quantity
        );
        array_push($newItems, $new_item);
        $level++;
    }
    //print_r($newItems);
    return $newItems;
}
/**
 *  returns an contact object from the order
 * 
 * @param array $items - the items array from the order
 * @param $level - level
 */
function get_variations_from_order($items, $level){// : Item
    $variations = array();
    $items = $items[0]->value;
    foreach($items as $val){
        $new_item = new Item(
            0000,
            $val->value,
            $val->price,
            new Variation(), //empty variation array
            0, 
            $val->quantity
        );
        array_push($variations, $new_item);
    }
    $variation = new Variation($variations, $level);

   // print_r($variations);
    return $variation;
}
 
function create_Remote_order($order, $shareToken) :RemoteOrder {
    $main = $order[0];
    $address = get_address_from_order($order);
    $contact = get_contact_from_order($order);
    $items = get_items_from_order($order);
    $remoteOrder = new RemoteOrder(
        $main->id,
        $shareToken,
        $items,
        $main->date_created,
        "",
        0,
        $contact,
        $address,
    );
    return $remoteOrder;
}



$card = new CreditCard(1,2,3,4,5);
//$p = new Payment(cash, 1, $card);
//$q = new Payment(cash,1,$card);
//var_dump($q);
//echo $card;
//echo($a);
//var_dump($a);
//echo $card;

$woocommerce = connect_to_woocommerce_api('https://freshit-order.ussl.blog',  'ck_d2bc995ba20f60ebaf241bac002e2699bb90bff7', 'cs_636f8e9f01a63eb072b768c73f92256d3f8ba56d');
$results = $woocommerce->get('orders');
// $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
// fwrite($myfile, print_r($results, true));
//get_address_from_order($results);
//get_contact_from_order($results);
//$a = get_items_from_order($results);
$myObj= create_Remote_order($results, "1234");
$myJSON = json_encode($myObj);
$myfile = fopen("newfilew.txt", "w") or die("Unable to open file!");
fwrite($myfile, $myJSON);


