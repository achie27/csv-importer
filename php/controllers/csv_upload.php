<?php
  require_once('./DatabaseHandler.php');
  require_once('../models/Student.php');
  require_once('./StudentRead.php');
  require_once('./StudentUpdate.php');
 
  try{
    if(isset($_FILES['csv'])){
      
      $conn = new DatabaseHandler('intern', 'achie27', '');
      $db = $conn->getHandle();
      
      $upd_handler = new StudentUpdate($db);
      $read_handler = new StudentRead($db);
      
      $thecsv = file($_FILES['csv']['tmp_name']);
      $students_not_updated = [];
      $tmp_file = fopen('updated_rows.tmp', 'w');

      foreach($thecsv as $line){
        $stu = str_getcsv($line); 
        $stud = new Student($stu[0], $stu[1], $stu[2], $stu[3]);
        $ret = $upd_handler->update($stud, $stu[3]);

        if($upd_handler->getUpdateStatus())
          fwrite($tmp_file, $stu[0].','.$stu[1].','.$stu[2].','.$stu[3]);
        else {
          $students_not_updated[] = $stud;
        }
      }
      
      fclose($tmp_file);
      
      $res = [];
      foreach($students_not_updated as $student){
        $st_data = $student->getData();
        $st_preds = $read_handler->predict($st_data['fname'], $st_data['lname'], $st_data['dob']);
        
        $preds = [];
        foreach($st_preds as $st_pred){
          $preds[] = $st_pred->getData();
        }
        
        if(count($preds) > 0){
          $res[] = [
            "csv_row" => $st_data,
            "predictions" => $preds
          ];
        }
      } 
      
      echo json_encode($res);
      http_response_code(200);
    }
  }
  
  catch (Exception $e) {
    echo $e;
    http_response_code(500);
  }
  
?>