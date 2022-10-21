<?php
  class HydroponicTestSystemDatabaseConnection{

    private $dbIp;
    private $username;
    private $password;
    
    private $connection;

    function __construct(string $dbIp, string $username, string $password){
      $this->dbIp = $dbIp;
      $this->username = $username;
      $this->password = $password;

      set_error_handler("error_handler", E_WARNING);

      $this->connection = new mysqli("mariadb:3306", $this->username, $this->password, 'hydroponic_test_system');

      restore_error_handler();
    }

    function connected(){
      return $this->connection != false;
    }

    function getParameterFromDatabase(string $parameter_name) : string {
      $sql = "SELECT * FROM `parameters` WHERE parameter_name = '$parameter_name'";

      $result = mysqli_query($this->connection, $sql);

      if(gettype($result) == "boolean"){
        return "SQL error";
      }

      $row = $result->fetch_assoc();

      return (string) $row["parameter_value"];      
    }

    function updateParameterToDatabase(string $parameter_name, string $parameter_value){
      $sql = "UPDATE parameters SET `parameter_value`='$parameter_value' WHERE parameter_name='$parameter_name'";

      $result = mysqli_query($this->connection, $sql); 
    }

    function getLogFromDatabase() : mysqli_result {
      $sql = "SELECT * FROM log WHERE log_date > SUBDATE(NOW(), INTERVAL 7 DAY) ORDER BY log_date DESC";
      
      return mysqli_query($this->connection, $sql);
    }

    function writeLogToDatabase(string $log_date, string $log_type, string $log_message){
      $sql = "INSERT INTO log (log_date, log_type, log_message) VALUES('$log_date', '$log_type', '$log_message')";
      
      $result = mysqli_query($this->connection, $sql);
    }
  }

  function error_handler($errno, $errstr) { 
    throw new Exception('No DB access', 100);
  }
?>