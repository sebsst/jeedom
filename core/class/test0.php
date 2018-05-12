<?php
public static function daemon() {


 log::add('worxLandroidS', 'info', 'client id: ' . config::byKey('mqtt_client_id', 'worxLandroidS'));


// init first connection
   if(config::byKey('mqtt_client_id', 'worxLandroidS') ==  ""){


   log::add('worxLandroidS', 'info', 'Paramètres utilisés, Host : ' . config::byKey('worxLandroidSAdress', 'worxLandroidS', '127.0.0.1') . ', Port : ' . config::byKey('worxLandroidSPort', 'worxLandroidS', '1883') . ', ID : ' . config::byKey('worxLandroidSId', 'worxLandroidS', 'Jeedom'));


     $email = config::byKey('email', 'worxLandroidS');
     $passwd = config::byKey('passwd', 'worxLandroidS');
     $resource_path = realpath(dirname(__FILE__) . '/../../resources/');

     $certfile = $resource_path.'/cert.pem';
     $pkeyfile = $resource_path.'/pkey.pem';
     $root_ca = $resource_path.'/vs-ca.pem';
     // get mqtt config
     $url =  "https://api.worxlandroid.com:443/api/v1/users/auth";

     $token = "qiJNz3waS4I99FPvTaPt2C2R46WXYdhw";
     $content = "application/json";
     $ch = curl_init();
     $data = array("email" => $email, "password" => $passwd, "uuid" => "uuid/v1" , "type"=> "app" , "platform"=> "android");
     $data_string = json_encode($data);

     $ch = curl_init($url);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
     curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       'Content-Type: application/json',
       'Content-Length: ' . strlen($data_string),
       'x-auth-token:' . $token
     )
   );



       $result = curl_exec($ch);
       log::add('worxLandroidS', 'info', 'Connexion result :'.$result);
       $json = json_decode($result,true);
       if (is_null($json))
       {
         log::add('worxLandroidS', 'info', 'Connexion KO for '.$equipement.' ('.$ip.')');
         //$this->checkAndUpdateCmd('communicationStatus',false);
         //return false;
       } else
       {
         //		config::save('created_at', $json['created_at'],'worxLandroid');
         //		config::save('api_token', $json['api_token'],'worxLandroid');
         //		config::save('mqtt_client_id', $json['mqtt_client_id'],'worxLandroid');
         //		config::save('mqtt_endpoint', $json['mqtt_endpoint'],'worxLandroid');
         //		config::save('id', $json['id'],'worxLandroid');


         // get certificate
         $url =  "https://api.worxlandroid.com:443/api/v1/users/certificate";
         $api_token = $json['api_token'];
         $token = $json['api_token'];
         //$token = "qiJNz3waS4I99FPvTaPt2C2R46WXYdhw";

         $content = "application/json";
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'x-auth-token:' . $api_token
         )
       );

       $result = curl_exec($ch);
       log::add('worxLandroidS', 'info', 'Connexion result :'.$result);

       $json2 = json_decode($result,true);


       if (is_null($json2))
       {
       } else
       {
         $pkcs12 = base64_decode($json2['pkcs12']);
         openssl_pkcs12_read( $pkcs12, $certs, "" );
         file_put_contents($certfile, $certs['cert']);
         file_put_contents($pkeyfile, $certs['pkey']);

         // get product item (mac address)
         $url =  "https://api.worxlandroid.com:443/api/v1/product-items";

         $content = "application/json";
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'x-auth-token:' . $api_token
         )
       );

       $result = curl_exec($ch);
       log::add('worxLandroidS', 'info', 'Connexion result :'.$result);

       $json3 = json_decode($result,true);


       if (is_null($json3))
       {
       } else
       {

         config::save('mac_address', $json3[0]['mac_address'],'worxLandroidS');
         log::add('worxLandroidS', 'info', 'mac_address '.$json3[0]['mac_address']);
       }


       // test client2
       config::save('mqtt_client_id', $json['mqtt_client_id'],'worxLandroidS');
       config::save('mqtt_endpoint', $json['mqtt_endpoint'],'worxLandroidS');
       log::add('worxLandroidS', 'info', 'mqtt_client_id '.$json['mqtt_client_id']);



}

}







}
else
{
  sleep(30);
/*


   $client = new Mosquitto\Client(config::byKey('mqtt_client_id', 'worxLandroidS'));
   $client->onConnect('worxLandroidS::connect');
   $client->onDisconnect('worxLandroidS::disconnect');
   $client->onSubscribe('worxLandroidS::subscribe');
   $client->onMessage('worxLandroidS::message');
   $client->onLog('worxLandroidS::logmq');
   $client->setWill('/jeedom', "Client died :-(", 1, 0);

   try {

     $client->connect(config::byKey('mqtt_endpoint', 'worxLandroidS', '127.0.0.1'), 8883 , 60);
//      $client->connect('a1optpg91s0ydf-2.iot.eu-west-1.amazonaws.com', '8883', 60);

     $topic = 'DB510/'.config::byKey('mac_address','worxLandroidS').'/commandOut';
       $client->subscribe($topic, 0, 1)); // !auto: Subscribe to root topic
       log::add('worxLandroidS', 'debug', 'Subscribe to topic ' . $topic, 'worxLandroidS', '#'));
     //$client->loopForever();
     while (true) { $client->loop(); }
   }
   catch (Exception $e){
     log::add('worxLandroidS', 'error', $e->getMessage());
   }
*/
sleep(90);


}






}
