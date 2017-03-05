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

      $connection = ConnectionManager::get('default');
      $connection->insert('Registred_users', [
          'name' => $value->data('first_name') . " " . $value->data('last_name'),
          'recipientid' => $value->data('recipientId'),
          'gender' => $value->data('gender'),
          'created' => new \DateTime('now')
      ], ['created' => 'datetime']);
    }

    public function checkUser(){
      $value = $this->request;
      $json_encode  = $value->data('recipientId');

      $id = json_encode($json_encode);

      $users = TableRegistry::get('Registred_users');

      $query = $users->find();
      $query->where([
        'Registred_users.recipientid' => $id
      ]);

      $found_user = $query->first();

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
}
