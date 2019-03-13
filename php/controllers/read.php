<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentRead.php');
  
  $MAX_FNAME_LEN = 50;
  $MAX_LNAME_LEN = 50;
  $MAX_MARKS = 100;
  $MIN_MARKS = 0;
  
  $d = $_GET;
  
  try {
    
    // Get a handle to the db
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    
    $read_handler = new StudentRead($db);
    
    // If all the students are required
    if($d['type'] == 'all'){
      $stus = $read_handler->getAll();
  
      $op = [];
      foreach($stus as $stud){
        $stu = $stud->getData();
        array_push($op, [
          'id' => $stu['id'], 
          'fname' => $stu['fname'], 
          'lname' => $stu['lname'], 
          'dob' => $stu['dob'], 
          'marks' => $stu['marks'] 
        ]);
      }
      
      header('Content-type: application/json');
      echo json_encode($op);
  
    // If a single student is required
    } else {
      
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
      )) {
        http_response_code(400);
        die();
      }
      
      $student = new Student($d['fname'], $d['lname'], $d['dob']);
      $stu = $read_handler->get($student)->getData();
  
      header('Content-type: application/json');
      echo json_encode([
        'id' => $stu['id'], 
        'fname' => $stu['fname'], 
        'lname' => $stu['lname'], 
        'dob' => $stu['dob'], 
        'marks' => $stu['marks'] 
      ]);  
    }
  } 
  
  catch (Exception $e){
    echo "<br>".$e."<br>";
    http_response_code(500);
  }
?>