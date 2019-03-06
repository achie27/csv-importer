<?php

  require_once("./DatabaseHandler.php");
  require_once("../models/Student.php");
  
  class StudentInsert {
    private $db = 0;
    private $stmt = "";
    
    function __construct($conn){
      $this->db = $conn;
      $this->stmt = $this->db->prepare("
        INSERT INTO student (fname, lname, dob, marks) VALUES (:fname, :lname, :dob, :marks)
      ");
    }
    
    function insert(Student $stud){
      
      $student = $stud->getData();
      
      $this->stmt->bindParam(":fname", $student['fname']);
      $this->stmt->bindParam(":lname", $student['lname']);
      $this->stmt->bindParam(":dob", $student['dob']);
      $this->stmt->bindParam(":marks", $student['marks']);
      $this->stmt->execute();
    }
    
    function insertMany($students){
      foreach($students as $stu){
        $this->insert($stu);
      }
    }
  }
?>