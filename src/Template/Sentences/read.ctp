<?php
  $Exploded_sentence = explode(" ",$zin);

  $servername = "localhost";
  $username = "emma";
  $password = "nBRj49UTSY3VjPnm";
  $dbname = "Emma";

  $names = [];
  $found_name = '';


  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }


  foreach ($Exploded_sentence as $value) {

    $sql = "SELECT * FROM Namen WHERE voornaam = '".$value."'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      while($row = $result->fetch_assoc()) {
        if($row['geslacht'] == 'M'){
          $geslacht = 'Man';
        }
        else{
          $geslacht = 'Vrouw';
        }
        // bedenken hoe het op te lossen is als het een man en vrouw kan zijn.

        array_push($names, strtolower($row['voornaam']));
      }
    } else {
    }
  }

$conn->close();

if(count($names) > 1){
  $names = array_unique($names);
  if(count($names) > 1){
    foreach ($names as $found => $name) {
      if($name == "ben"){
        unset($names[$found]);
      }
    }
    if(count($names) > 1){
      foreach ($Exploded_sentence as $value) {
        foreach ($names as $name) {
          if(strcmp($name, $value) == 0){
            $found_name = $name;
          }
        }
      }
    }
    else{
      $found_name = $name;
    }
  }
  else{
    $found_name = $names[0];
  }
}
else{
  $found_name = $names[0];
}

$data = array(
    "recipientId" => 15,
    "type" => "text",
    "message" => "Hallo " . $found_name . " leuk je te leren kennen",
	);

echo(json_encode($data));

// print_r($found_name);
?>