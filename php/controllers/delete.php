<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentDelete.php');

  $d = 0;
  parse_str(file_get_contents("php://input"), $d);

  try {
    
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    $del_handler = new StudentDelete($db);
  
    $student = new Student($d['fname'], $d['lname'], $d['dob']);
    $del_handler->delete($student);

    http_response_code(200);
  } 
  
  catch (Exception $e){
    echo "<br>".$e."<br>";
    http_response_code(500);
  }
?>