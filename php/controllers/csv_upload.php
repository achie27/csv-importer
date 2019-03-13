<?php
  require_once('./DatabaseHandler.php');
  require_once('../models/Student.php');
  require_once('./StudentRead.php');
  require_once('./StudentUpdate.php');

  try{
    if(isset($_FILES['csv'])){
  
      // Checking if the file really is a CSV
      $mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv', 'application/octet-stream');
      if(!in_array($_FILES['csv']['type'], $mimes)){
        http_response_code(400);
        die("Sorry, mime type not allowed");
      }
      
      // Getting the handle of DB
      $conn = new DatabaseHandler('intern', 'achie27', '');
      $db = $conn->getHandle();
      
      $upd_handler = new StudentUpdate($db);
      $read_handler = new StudentRead($db);
      
      // Creating the temporary file 
      $date = date_create();
      $tmp_file = fopen('updated_rows_'.date_format($date, 'Y_m_d_H_i_s').'.tmp', 'w');

      $thecsv = file($_FILES['csv']['tmp_name']);
      $students_not_updated = [];

      foreach($thecsv as $line){
        $stu = str_getcsv($line);

        if(count($stu) !== 4) {
          http_response_code(400);
          die("Wrong CSV format");
        }

        $stud = new Student($stu[0], $stu[1], $stu[2], $stu[3]);
        $ret = $upd_handler->update($stud, $stu[3]);

        // Add to the temp file if the update took place
        // otherwise add to the list which will sent to
        // the user
        if($upd_handler->getUpdateStatus())
          fwrite($tmp_file, $stu[0].','.$stu[1].','.$stu[2].','.$stu[3]);
        else {
          $students_not_updated[] = $stud;
        }
      }
      
      fclose($tmp_file);
      
      // Get table record suggestions for mismatched CSV rows
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
      
    } else {
      http_response_code(400);
      die();
    }
  }
  
  catch (Exception $e) {
    echo $e;
    http_response_code(500);
  }
  
?>