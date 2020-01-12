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
        //add items
        //add charges
        public  $comment;
        public  $created;
        public  $modified;
        public  $deliveryDate;
        public  $formatCreatedDate;      //// formatted yyyy-MM-dd HH:mm:ss
        public  $formatDeliveryDate;     //// formatted yyyy-MM-dd HH:mm:ss
        public  $status;
        //add contact
        public  $deliveryType;          //0-delivery 1-takeout
        public  $servingType;           //(0- SIT, 1-TA, 2-DELIVERY) not mandatory, default value is 2-DELIVERY
        //add Address
        //add payments
        public  $acceptableOnPayFail;


    }

    class Contact{

        public  $id;
        public  $firstName;
        public  $lastName;
        public  $email;
        public  $phone;
        public  $fax;

        function __construct($id, $firstName, $lastName, $phone, $email="", $fax="") {
            $this->id        = $id;
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
    class item{
        public $id;
        public $desc;
        public $group;
        public $price;
        public $variations = array();
        public $status;
        public $comment;
        public $count;
        public $discountable;
        public $type;
        public $discountAmountRuleType;
        public $discountAmount;

        function __construct($id, $desc, $group, $price, $variations, $status, $comment, $count = 0, $discountable = false, $type=0, $discountAmountRuleType = 0, $discountAmount=0) {
            $this->id           = $id;
            $this->desc         = $desc;
            $this->group        = $group;
            $this->price        = $price;
            $this->variations   = $variations;
            $this->status       = $status;
            $this->comment      =$comment;
            $this->count        = $count;
            $this->discountable = $discountable;
            $this->type         = $type;
            $this->discountAmountRuleType = $discountAmountRuleType;
            $this->discountAmount = $discountAmount;
          }




    }
    class Variation{

    }
    class Address{
        public  $country;
        public  $city;
        public  $street;
        public  $number;
        public  $apt;
        public  $floor;
        public  $entrance;
        public  $comment;
        public  $lat;
        public  $lng;
        public  $postalCode;

        function __construct($city, $street, $number, $apt, $floor, $entrance, $postalCode=0, $country = "IL", $lat="", $lng="") {
            $this->city       = $city;
            $this->street     = $street;
            $this->number     = $number;
            $this->apt        = $apt;
            $this->floor      = $floor;
            $this->entrance   = $entrance;
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
    class test{
        public  $a1;
        public  $a2;
        public $a3;
        public function __construct($a1, $a3='q',$a2 = 'a')
        {
            $this->a1=$a1;
            $this->a2=$a2;
            $this->a3=$a3;
        }
        public function __toString()
        {
            return a1 . "+" .a2;
        }
    }


    $card = new CreditCard(1,2,3,4,5);
    //$q = new Payment(cash,1,$card);
    //var_dump($q);
    //echo $card;
    //echo($a);
    //var_dump($a);
    //echo $card;
   

$woocommerce = new Client(
    'https://freshit-order.ussl.blog', 
    'ck_d2bc995ba20f60ebaf241bac002e2699bb90bff7', 
    'cs_636f8e9f01a63eb072b768c73f92256d3f8ba56d',
    [
        'version' => 'wc/v3',
        'ssl_verify' => false,
    ]
    
);
$woocommerce;
print_r($woocommerce);
$results = $woocommerce->get('orders');
print_r($results);
//print_r($woocommerce->get('orders'));


 