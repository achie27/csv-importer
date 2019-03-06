<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentUpdate.php');

  $d = 0;
  parse_str(file_get_contents("php://input"), $d);

  try {
    
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    $upd_handler = new StudentUpdate($db);
  
    $student = new Student($d['fname'], $d['lname'], $d['dob']);
    $stu = $upd_handler->update($student, $d['marks']);

    http_response_code(200);
  } 
  
  catch (Exception $e){
    echo "<br>".$e."<br>";
    http_response_code(500);
  }
?>