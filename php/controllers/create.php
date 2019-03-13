<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentInsert.php');
  
  $MAX_FNAME_LEN = 50;
  $MAX_LNAME_LEN = 50;
  $MAX_MARKS = 100;
  $MIN_MARKS = 0;
  
  // Expects JSON
  $d = json_decode(file_get_contents("php://input"), true);
  
  
  // Validating sent data
  if((
      (!isset($d['fname'])) or
      (strlen($d['fname']) > $MAX_FNAME_LEN) or
      (strlen($d['fname']) == 0) or
      (!preg_match("/^[a-zA-Z ]*$/", $fname))
    ) or (
      (!isset($d['lname'])) or
      (strlen($d['lname']) > $MAX_LNAME_LEN) or
      (strlen($d['lname']) == 0) or
      (!preg_match("/^[a-zA-Z ]*$/", $lname))
    ) or (
      (!isset($d['dob']))
    ) or (
      (!isset($d['marks'])) or  
      (!is_numeric($d['marks'])) or
      ($d['marks'] > $MAX_MARKS) or
      ($d['marks'] < $MIN_MARKS)
    )) {
      http_response_code(400);
      die();
    }
  
  try {
    // Gets the DB handle
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