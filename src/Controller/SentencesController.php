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
      // $data = Array(
      //       "recipientId" => $value->data('recipientId'),
      //       "Name" => $value->data('first_name') . $value->data('last_name'),
      //       "type" => "text",
      //       "message" => "Hallo " . $value->data('first_name') . " Leuk je te leren kenen",
      //
      //   );
        // $this->set('data', $data);
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

      $query_2 = $users->find();
      $query_2->where([
        'Registred_users.id' => $query->first()->user_id
      ]);

      $found_user = $query_2->first();

      if (!is_object($found_user)) {
        $data = Array(
              "recipientId" => $value->data('recipientId'),
              "registered" => false,
          );
          $this->set('data', $data);
      }else{
        $data = Array(
              "recipientId" => $value->data('recipientId'),
              "registered" => true,
          );
          $this->set('data', $data);
      }
    }




    public function getResponse(){
      $value = $this->request;
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

      if(is_object($found_user)){
        $fullname = $query_2->first()->name;
        $split_name = explode(" ", $fullname);
        $firstname = $split_name[0];
        $data = Array(
              "recipientId" => $value->data('recipientId'),
              "type" => "text",
              "message" => "Hallo " . $firstname . " leuk je weer te zien!"
          );
          $this->set('data', $data);
      }
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
