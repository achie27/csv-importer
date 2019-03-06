<?php
  class DatabaseHandler {
    private $db = 0;
    
    function __construct($dbname, $user, $pass){
      $this->db = new PDO("mysql:host=localhost;dbname=".$dbname, $user, $pass);
      $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    function getHandle(){
      return $this->db;
    }
  }
?>