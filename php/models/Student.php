<?php
  class Student {
    private $fname = "";
    private $lname = "";
    private $dob = "";
    private $marks = 0;
    private $id = 0;
    
    function __construct($first, $last, $dateb, $mark = 0, $id = -1){
      $this->fname = $first;
      $this->lname = $last;
      $this->dob = $dateb;
      $this->marks = $mark;
      $this->id = $id;
    }
    
    public function getData(){
      return [
        "fname" => $this->fname,
        "lname" => $this->lname,
        "dob" => $this->dob,
        "marks" => $this->marks,
        "id" => $this->id
      ];
    }
  }
?>