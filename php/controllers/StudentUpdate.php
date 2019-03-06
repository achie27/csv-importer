<?php

  require_once("./DatabaseHandler.php");
  require_once("../models/Student.php");
  
  class StudentUpdate {
    private $db = 0;
    private $stmt = "";

    function __construct($conn){
      $this->db = $conn;
      $this->stmt = $this->db->prepare("
        UPDATE student 
        SET `marks` = :marks 
        WHERE `fname`= :fname and `lname`= :lname and `dob`= :dob
      ");
    }
    
    function update(Student $stu, $marks){
      
      $student = $stu->getData();
      
      $this->stmt->bindParam(":fname", $student['fname']);
      $this->stmt->bindParam(":lname", $student['lname']);
      $this->stmt->bindParam(":dob", $student['dob']);
      $this->stmt->bindParam(":marks", $marks);
      
      $this->stmt->execute();
    }
  }
?>