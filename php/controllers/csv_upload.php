<?php
  require_once('../models/Student.php');
  require_once('./DatabaseHandler.php');
  require_once('./StudentRead.php');
  require_once('./StudentUpdate.php');
  

  if(isset($_FILES['csv'])){
    
    $conn = new DatabaseHandler('intern', 'achie27', '');
    $db = $conn->getHandle();
    
    $upd_handler = new StudentUpdate($db);
    $read_handler = new StudentRead($db);
    
    $csv = file($_FILES['csv']['tmp_name']);
    $students_not_updated = [];
    
    foreach($csv as $line)
        $stu = str_getcsv($line);
        $student = new Student($stu[0], $stu[1], $stu[2]);
        print_r($stu);
        $upd_handler->update($student, $stu[3]);
        if(!($upd_handler->getUpdateStatus()))
            $students_not_updated[] = $student;
        
    
    $res = [];
    foreach($students_not_updated as $stud){
      $st_data = $stud->getData();
      $st_preds = $read_handler->predict($st_data['fname'], $st_data['lname'], $st_data['dob']);
      
      $preds = [];
      foreach($st_preds as $st_pred){
        $preds[] = $st_pred->getData();
      }
      
      $res[] = [
        "csv_row" => $st_data,
        "predictions" => $preds
      ];
    } 
    
    echo json_encode($res);
    http_response_code(200);
  }
?>