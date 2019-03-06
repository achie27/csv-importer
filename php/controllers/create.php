<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentInsert.php');
  
  $d = json_decode(file_get_contents("php://input"), true);
    
  try {
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    $new_student = new Student($d['fname'], $d['lname'], $d['dob'], $d['marks']);
    $insert_handler = new StudentInsert($db);
    $insert_handler->insert($new_student);
    http_response_code(200);
  } 
  
  catch (Exception $e){
    echo "<br>".$e."<br>";
    http_response_code(500);
  }
?>