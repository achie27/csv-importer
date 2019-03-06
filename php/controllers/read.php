<?php

  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentRead.php');
  
  $d = $_GET;
  
  try {
    
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    $read_handler = new StudentRead($db);
    
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
  
    // } elseif ($d['type'] == 'pred') {
      
    //   $student = new Student($d['fname'], $d['lname'], $d['dob']);
    //   $stu = $read_handler->get($student)->getData();
  
    //   header('Content-type: application/json');
    //   echo json_encode([
    //     'id' => $stu['id'], 
    //     'fname' => $stu['fname'], 
    //     'lname' => $stu['lname'], 
    //     'dob' => $stu['dob'], 
    //     'marks' => $stu['marks'] 
    //   ]);
      
    } else {
    
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
      http_response_code(200);
  } 
  
  catch (Exception $e){
    echo "<br>".$e."<br>";
    http_response_code(500);
  }
?>