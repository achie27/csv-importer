<?php

  require_once("./DatabaseHandler.php");
  require_once("../models/Student.php");
  
  class StudentDelete {
    private $db = 0;
    private $stmt = "";
    
    function __construct($conn){
      $this->db = $conn;
      $this->stmt = $this->db->prepare("
        DELETE FROM student WHERE `fname`=:fname and `lname`=:lname and `dob`=:dob
      ");
    }
    
    function delete(Student $stu){
      
      $student = $stu->getData();
      
      $this->stmt->bindParam(":fname", $student['fname']);
      $this->stmt->bindParam(":lname", $student['lname']);
      $this->stmt->bindParam(":dob", $student['dob']);

      $this->stmt->execute();
    }
  }
?>