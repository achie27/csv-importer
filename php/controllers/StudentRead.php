<?php

  require_once("./DatabaseHandler.php");
  require_once("../models/Student.php");
  
  class StudentRead {
    private $db = 0;
    private $stmt = "";
    private $stmt_all = "";
    
    function __construct($conn){
      $this->db = $conn;
      $this->stmt = $this->db->prepare("SELECT * FROM student WHERE `fname`=:fname and `lname`=:lname and `dob`=:dob");
      $this->stmt_pred = $this->db->prepare("
        SELECT * FROM student 
        WHERE (
          (NOT (`fname`=:fname and `lname`=:lname and `dob`=:dob))
          and (
            (`fname`=:fname and `lname`=:lname) 
            or (`dob`=:dob and `lname`=:lname)
            or (`dob`=:dob and `fname`=:fname)
          )
        )
      ");
      $this->stmt_all = $this->db->prepare("SELECT * FROM student");
    }
    
    // Get the data for $stud
    function get(Student $stud){
      
      $student = $stud->getData();

      $this->stmt->bindParam(":fname", $student['fname']);
      $this->stmt->bindParam(":lname", $student['lname']);
      $this->stmt->bindParam(":dob", $student['dob']);
      
      $this->stmt->execute();
      $res = $this->stmt->fetch();
      $stu = new Student($student['fname'], $student['lname'], $student['dob'], $res['marks'], $res['id']);

      return $stu;
    }

    // Suggest students with similar fname, lname, or dob
    function predict($fname, $lname, $dob){
      $this->stmt_pred->bindParam(":fname", $fname);
      $this->stmt_pred->bindParam(":lname", $lname);
      $this->stmt_pred->bindParam(":dob", $dob);
      
      $this->stmt_pred->execute();
      $res = $this->stmt_pred->fetchAll();
      
      $op = [];
      foreach($res as $stu){
        $stud = new Student($stu['fname'], $stu['lname'], $stu['dob'], $stu['marks'], $stu['id']);
        array_push($op, $stud);
      }
      return $op;
  
    }
    
    function getAll(){
      $this->stmt_all->execute();
      $res = $this->stmt_all->fetchAll();
      $op = [];
      
      foreach($res as $stu){
        $student = new Student($stu['fname'], $stu['lname'], $stu['dob'], $stu['marks'], $stu['id']);
        array_push($op, $student);
      }
      
      return $op;
    }
  }
?>