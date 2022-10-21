<?php
  $autoReloadIntervalSeconds = "5";
?>

<html>
  <head>
    <!-- Auto reload -->
    <meta http-equiv="refresh" content="<?php echo $autoReloadIntervalSeconds?>;URL='<?php echo $_SERVER['PHP_SELF']?>'">

    <!--link rel="stylesheet" href="css/stylesheet.css"-->
<style>  
:root {
  --mainbuttoncolor: lightgray;
}

.main_switch{
  display: inline-block;
  padding: 15px 25px;
  font-size: 24px;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  outline: none;
  color: #fff;
  background-color: var(--mainbuttoncolor);
  border: none;
  border-radius: 15px;
  box-shadow: 0 9px #999;
}

.main_switch:hover {background-color: var(--mainbuttoncolor);}

.main_switch:active{	
  background-color: var(--mainbuttoncolor);
  box-shadow: 0 5px #666;
  transform: translateY(4px);
}

#log_table {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#log_table td, #log_table th {
  border: 1px solid #ddd;
  padding: 8px;
}

#log_table tr:nth-child(even){background-color: #f2f2f2;}

#log_table tr:hover {background-color: #ddd;}

#log_table th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04AA6D;
  color: white;
}
</style>
    
  </head>
  <body>
    <h1>Hydroponic Test System</h1>
    <form action="" method="post">
      <button type="submit" class="main_switch" name="main_switch" id="main_switch">Main switch</button>
    </form>
    <br>

    <?php
    include("HydroponicTestSystemDatabaseConnection.php");

    $serveraddress = $_SERVER['SERVER_ADDR'];
    $dbIp = "mariadb";
    if($serveraddress == null){
      $serveraddress = 'localhost';
    }

    try{
      $hydroponicTestSystemDatabaseConnection = new HydroponicTestSystemDatabaseConnection($dbIp, 'root', 'root');
    }
    catch(Exception $e){
      die("<b>No DB connection to $dbIp:3306</b>");
    }
    
    // ========== log messages ==========

    if(isset($_POST['log_date']) && isset($_POST['log_type']) && isset($_POST['log_message'])){
      $log_date = urldecode($_POST['log_date']);
      $log_type = urldecode($_POST['log_type']);
      $log_message = urldecode($_POST['log_message']);

      $hydroponicTestSystemDatabaseConnection->writeLogToDatabase($log_date, $log_type, $log_message);
    }

    // ========== outside db access via post ==========
  
    if(isset($_POST['parameter_name']) && isset($_POST['parameter_value'])){
      extract($_POST);

      $hydroponicTestSystemDatabaseConnection->updateParameterToDatabase($parameter_name, $parameter_value);
    }

    // ========== MainButton clicked event ==========

    if(isset($_POST['main_switch'])){
      // Toggle button state
      $newState = $hydroponicTestSystemDatabaseConnection->getParameterFromDatabase('state') == 0 ? '1' : '0';

      // database insert SQL code
      $hydroponicTestSystemDatabaseConnection->updateParameterToDatabase('state', $newState);
    }

    // ========== Visualization ==========

    $currentState = $hydroponicTestSystemDatabaseConnection->getParameterFromDatabase('state');

    ?>
    <script>
      currentState = <?= json_encode($currentState); ?>;

      if(currentState === '0'){
        document.documentElement.style.setProperty('--mainbuttoncolor', 'lightgray');
      }
      if(currentState === '1'){
        document.documentElement.style.setProperty('--mainbuttoncolor', '#04AA6D');
      }
    </script>

    <h2>Log messages last 7 days</h2>

    <!-- =========== log table ========== -->
    <table id="log_table">
    <?php

    $results = $hydroponicTestSystemDatabaseConnection->getLogFromDatabase();

    while ($fieldInfo = mysqli_fetch_field($results)) {
    ?>
        <th> <?php echo $fieldInfo->name; ?> </th>
        <?php 
        $colNames[] = $fieldInfo->name;
        ?>
    <?php }

    while ($row = mysqli_fetch_array($results)) { ?>
      <tr>
      <?php for ($i=0; $i<sizeof($colNames); $i++) { ?>
          <td><?php echo $row[$colNames[$i]] ?>
      <?php } ?>
      </tr>
  <?php } ?>
  </table>
  <script>
    var table = document.getElementById('log_table');
    var rows = table.rows;

    for (var i=1, lenr=rows.length; i<lenr; i++){
      var log_type = rows[i].cells[1].innerHTML.trim();

      if (log_type === "WARNING"){
        rows[i].style.backgroundColor = 'yellow';
      }
      else if (log_type === "ERROR"){
        rows[i].style.backgroundColor = 'red';
      }
    }
  </script>
  </body>
</html>