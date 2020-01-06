<?php
/*
Plugin Name: Remote Delivery API
Plugin URI: devforce.co.il
Description: Remote Delivery API
Author: Devforce
Version: 1.0.0
*/

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

        function __construct($id, $firstName, $lastName, $email, $phone, $fax) {
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
            $this->paymentMethod   :: $paymentMethod;
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

        function __construct($number, $expireMonth, $expireYear, $holderId, $holderName, $billingAddress, $billingPostalCode, $CVV, $token) {
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
              empty($this->token) ? $str.="\ntoken:" . $this->token  : "";
              return  $str;

          }
    }
    class group{
        public  $id;
        public  $name;
    }
    