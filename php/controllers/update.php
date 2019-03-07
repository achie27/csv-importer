<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentUpdate.php');

  $d = json_decode(file_get_contents("php://input"), true);
  
  try {
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    $upd_handler = new StudentUpdate($db);
  
    $student = new Student($d['fname'], $d['lname'], $d['dob']);
    $upd_handler->update($student, $d['marks']);
    echo json_encode([
      'status' => $upd_handler->getUpdateStatus()
    ]);
    http_response_code(200);
  } 
  
  catch (Exception $e){
    echo "<br>".$e."<br>";
    http_response_code(500);
  }
?>