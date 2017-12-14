<?php

//should be required for all database related php
//TODO: create new user instead of using root

  $user = 'root';
  $pass = '';
  $db_name = 'hseo_project_safety';

  $db = mysqli_connect('127.0.0.1', $user, $pass, $db_name) or die("Unable to connect to database");

?>
