<?php 
  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentUpdate.php');

  $data = json_decode(file_get_contents("php://input"), true);
  print_r($data);
  
  try {
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    $upd_handler = new StudentUpdate($db);
    $student = 0;
  
    foreach($data as $d){
      $student = new Student($d['fname'], $d['lname'], $d['dob'], $d['marks']);
      $upd_handler->update_with_id($student, $d['id']);
    }
    
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