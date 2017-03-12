<?php
namespace App\Controller;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

class SentencesController extends AppController
{
  public $components = array('RequestHandler');
  public function initialize()
  {
      parent::initialize();
      $this->loadComponent('RequestHandler');
  }

  public function index()
    {
        $this->set('sentences', $this->Sentences->find('all'));

    }

    public function add()
      {
          $this->set('sentences', $this->Sentences->find('all'));
      }

    public function read($sentence)
    {

          $names = [];
          $found_name = '';

          $Exploded_sentence = explode(" ",$sentence);
          $Names = TableRegistry::get('Names');

          $query = $Names->find();
          $query->where([
            'Names.voornaam' => $Exploded_sentence
          ], [
            'Names.voornaam' => 'string[]'
          ]);

          $voornaam = $query->all();

          foreach ($voornaam as $row) {
                if($row['geslacht'] == 'M'){
                  $geslacht = 'Man';
                }
                else{
                  $geslacht = 'Vrouw';
                }
                echo $row['voornaam'] . " " . $geslacht . "<br>";
                // array_push($names['voornaam']=strtolower($row['voornaam']));
          }

          print_r($names);
    }

    public function test(){
      $value = $this->request;
    }

    public function listen($sentence){
      $this->set('sentence', $sentence);
    }

    public function registerUser(){
      $value = $this->request;
        if($value->data('platform') == "facebook"){
          $connection = ConnectionManager::get('default');
          $connection->insert('Registred_users', [
              'name' => $value->data('first_name') . " " . $value->data('last_name'),
              'gender' => $value->data('gender'),
              'locale' => $value->data('locale'),
              'timezone' => $value->data('timezone'),
              'created' => new \DateTime('now')
          ], ['created' => 'datetime']);

          $users = TableRegistry::get('Registred_users');
          $query = $users->find();
          $query->where([
            'Registred_users.name' => $value->data('first_name') . " " . $value->data('last_name')
          ]);

          $json_encode  = $value->data('recipientId');

          $id = json_encode($json_encode);

          $connection->insert('facebook', [
              'user_id' => $query->first()->id,
              'facebook_id' => $id,
              'profile_pic' => $value->data('profile_pic')
          ]);

          $data = Array(
              "recipientId" => $value->data('recipientId'),
              "registered_completed" => true,
            );

          $this->set('data', $data);
        }
      }

    public function showUserInfo(){
      $value = $this->request;
      $json_encode  = $value->data('recipientId');

      $id = json_encode($json_encode);

      $users = TableRegistry::get('Registred_users');
      $linked_table = TableRegistry::get('company_user_loyal');
      $company = TableRegistry::get('company');
      $facebook = TableRegistry::get('facebook');

      $query = $facebook->find();
      $query->where([
        'facebook.facebook_id' => $id
      ]);

      $query_2 = $users->find();
      $query_2->where([
        'Registred_users.id' => $query->first()->user_id
      ]);

      $query2 = $linked_table->find();
      $query2->where([
        'company_user_loyal.user_id' => $query_2->first()->id
      ]);

      $query3 = $company->find();
      $query3->where([
        'company.id' => $query2->first()->company_id
      ]);

      $query4 = $company->find();
      $query4->where([
        'company.id' => $query2->first()->company_id
      ]);

      $test_user = $query2->first()->percentage;
      $found_user = $query_2->first();
      $found_company = $query3->first();
      $facebook_info = $query->first();

      $data = Array(
            "user_info" => $found_user,
            "facebook_info" => $facebook_info,
            "company_info" => $found_company,
            "percentage_loyal" => $test_user,
        );

      $this->set('data', $data);
    }



    public function checkUser(){
      $value = $this->request;
      $json_encode = $value->data('recipientId');

      $id = json_encode($json_encode);

      $users = TableRegistry::get('Registred_users');
      $facebook = TableRegistry::get('facebook');

      $query = $facebook->find();
      $query->where([
        'facebook.facebook_id' => $id
      ]);

      if ($query->first() == null) {
        $data = Array(
                "recipientId" => $value->data('recipientId'),
                "registered" => false,
            );
            $this->set('data', $data);
      }
      else{
        $query_2 = $users->find();
        $query_2->where([
          'Registred_users.id' => $query->first()->user_id
        ]);

        $found_user = $query_2->first();
        $data = Array(
                "recipientId" => $value->data('recipientId'),
                "registered" => true,
            );
            $this->set('data', $data);
      }
    }

    public function getResponse(){
      $data = array();
      $value = $this->request;
      function check_user_response($value){
        $json_encode = $value->data('recipientId');

        $id = json_encode($json_encode);

        $users = TableRegistry::get('Registred_users');
        $facebook = TableRegistry::get('facebook');

        $query = $facebook->find();
        $query->where([
          'facebook.facebook_id' => $id
        ]);

        $query_2 = $users->find();
        $query_2->where([
          'Registred_users.id' => $query->first()->user_id
        ]);

        $found_user = $query_2->first();
        return $found_user;
      }

      $found_user = check_user_response($value);
      $sent_message = strtolower($value->data('message'));

      if(strpos($sent_message, 'doen') !== false AND strpos($sent_message, 'leuks') !== false AND strpos($sent_message, 'ik') !== false){
        $this->leuks_doen($sent_message,$value);
      }

      elseif(strpos($sent_message, 'kiest bioscoop') !== false){
        $this->bioscoop($sent_message,$value);
      }

      elseif(strpos($sent_message, 'kiest karten') !== false){
        $this->karten($sent_message,$value);
      }

      elseif(strpos($sent_message, 'kiest restaurant') !== false){
        $this->restaurant($sent_message,$value);
      }

      elseif(strpos($sent_message, 'what is love') !== false){
        $data = Array(
          "recipientId" => $value->data('recipientId'),
          "type" => "text",
          "message" => "Baby don't hurt me, baby don't hurt me no more!"
        );

        $this->set('data', $data);
      }

      elseif(strpos($sent_message, 'hallo') !== false OR strpos($sent_message, 'hoi') !== false OR strpos($sent_message, 'hee') !== false OR strpos($sent_message, 'heey') !== false){
        $this->groeten($sent_message,$value,$found_user);
      }
      else{
        $this->nietbegrijpenvraag($sent_message,$value);
      }
    }

    public function groeten($sent_message,$value,$found_user){
      $fullname = $found_user->name;
      $split_name = explode(" ", $fullname);
      $firstname = $split_name[0];

      $data = Array(
        "recipientId" => $value->data('recipientId'),
        "type" => "text",
        "message" => "Hallo " . $firstname . " leuk je te zien!"
      );

      $this->set('data', $data);
    }

    public function nietbegrijpenvraag($sent_message,$value){
      $data = Array(
        "recipientId" => $value->data('recipientId'),
        "type" => "text",
        "message" => "Ik begrijp het niet zo goed. Ik zal al mijn vrije tijd erin steken om je beter te begrijpen"
      );

      $this->set('data', $data);
    }

    public function leuksdoenmogelijkheden($sent_message,$value){
      $data = Array(
        "recipientId" => $value->data('recipientId'),
        "type" => "rich",
        "data" => [Array(
          "title" => "Bioscoop",
          "subtitle" => "Avondje naar de film",
          "image_url" => "http://www.diarioonline.com.br/img/noticias/original/destaque-357681-cinema-460x235.jpg",
          "buttons" => [Array(
                              "type" => "postback",
                              "title" => "Bios klinkt goed!",
                              "payload" => "kiest bioscoop"
                              )]
          ),Array(
            "title" => "Restaurant",
            "subtitle" => "Avondje uit eten",
            "image_url" => "http://www.gobignews.com/wp-content/images/2016/10/McDonald.jpg",
            "buttons" => [Array(
              "type" => "postback",
              "title" => "Restaurant klinkt goed!",
              "payload" => "kiest restaurant"
            )]
          ),Array(
            "title" => "Karten",
            "subtitle" => "Racen",
            "image_url" => "http://www.diarioonline.com.br/img/noticias/original/destaque-378340-unnamed.jpg",
            "buttons" => [Array(
              "type" => "postback",
              "title" => "Karten klinkt goed!",
              "payload" => "kiest karten"
            )]
          )]
        );
      $this->set('data', $data);
    }

    public function leuks_doen($sent_message,$value){
      $data = array(
        "recipientId"=> $value->data('recipientId'),
        "type"=> "text",
        "message"=> "Ik heb verschillende opties voor je, wat wil je graag gaan doen?"
      );
      $data_string = json_encode($data);

      $ch = curl_init('https://emma-middleware.herokuapp.com/send');
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($data_string))
      );

      $result = curl_exec($ch);
      $this->leuksdoenmogelijkheden($sent_message,$value);
    }

    public function restaurant($sent_message,$value){
      $data = Array(
        "recipientId" => $value->data('recipientId'),
        "type" => "text",
        "message" => "Voor hoeveel mensen wil je dat ik reserveer ?"
      );

      $this->set('data', $data);
    }

    public function karten($sent_message,$value){
      $data = Array(
        "recipientId" => $value->data('recipientId'),
        "type" => "text",
        "message" => "Hoeveel rondes wil je gaan?"
      );

      $this->set('data', $data);
    }

    public function bank_hangen(){

    }

    public function bioscoop($sent_message,$value){
      $data = Array(
        "recipientId" => $value->data('recipientId'),
        "type" => "text",
        "message" => "Hoeveel kaartjes wil je dat ik reserveer?"
      );

      $this->set('data', $data);
    }

    public function showCoupons(){

      $value = $this->request;
      $id  = $value->data('company_id');

      $company = TableRegistry::get('Registred_users');
      $linked_table = TableRegistry::get('coupon_user');
      $company = TableRegistry::get('company');
      $facebook = TableRegistry::get('facebook');

      $query = $facebook->find();
      $query->where([
        'facebook.facebook_id' => $id
      ]);

      $query_2 = $users->find();
      $query_2->where([
        'Registred_users.id' => $query->first()->user_id
      ]);

      $query2 = $linked_table->find();
      $query2->where([
        'company_user_loyal.user_id' => $query_2->first()->id
      ]);

      $query3 = $company->find();
      $query3->where([
        'company.id' => $query2->first()->company_id
      ]);

      $query4 = $company->find();
      $query4->where([
        'company.id' => $query2->first()->company_id
      ]);

      $test_user = $query2->first()->percentage;
      $found_user = $query_2->first();
      $found_company = $query3->first();
      $facebook_info = $query->first();
    }
}


// echo json_encode($voornaam->toArray());

// echo json_encode($voornaam);
// if(count($names) > 1){
//   $names = array_unique($names);
//   if(count($names) > 1){
//     foreach ($names as $found => $name) {
//       if($name == "ben"){
//         unset($names[$found]);
//       }
//     }
//     if(count($names) > 1){
//       foreach ($Exploded_sentence as $value) {
//         foreach ($names as $name) {
//           if(strcmp($name, $value) == 0){
//             $found_name = $name;
//           }
//         }
//       }
//     }
//     else{
//       $found_name = $name;
//     }
//   }
//   else{
//     $found_name = $names[0];
//   }
// }
// else{
//   $found_name = $names[0];
// }

//
//
//   foreach ($Exploded_sentence as $value) {
//
//     $sql = "SELECT * FROM Namen WHERE voornaam = '".$value."'";
//     $result = $conn->query($sql);
//
//     if ($result->num_rows > 0) {
//       while($row = $result->fetch_assoc()) {
//         if($row['geslacht'] == 'M'){
//           $geslacht = 'Man';
//         }
//         else{
//           $geslacht = 'Vrouw';
//         }
//         // bedenken hoe het op te lossen is als het een man en vrouw kan zijn.
//
//         array_push($names, strtolower($row['voornaam']));
//       }
//     } else {
//     }
//   }
//
//
// if(count($names) > 1){
//   $names = array_unique($names);
//   if(count($names) > 1){
//     foreach ($names as $found => $name) {
//       if($name == "ben"){
//         unset($names[$found]);
//       }
//     }
//     if(count($names) > 1){
//       foreach ($Exploded_sentence as $value) {
//         foreach ($names as $name) {
//           if(strcmp($name, $value) == 0){
//             $found_name = $name;
//           }
//         }
//       }
//     }
//     else{
//       $found_name = $name;
//     }
//   }
//   else{
//     $found_name = $names[0];
//   }
// }
// else{
//   $found_name = $names[0];
// }

// $this->set('test', $test);
