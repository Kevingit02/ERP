<?php
    session_start();
    if (empty($_SESSION['uid'])){
      echo "Inte inloggad än, gå tillbaka till <a href='login.php'>inloggningssidan</a>";
    }else{
      $uid = $_SESSION['uid'];
      $name = $_SESSION['name'];
    }
    if (!empty($uid)){
?>
<!DOCTYPE html>
<html lang="sv">
  <head>
    <meta charset="UTF-8" />
    <title>Om oss – Mölndals vårdcentral</title>
    <link href="style.css" rel="stylesheet">
    <script src="javaScript.js"></script>
  </head>
  <body>
       <header>
      <div id="bannerContainer">
        <div id="banner">
          <a href="exempelfil_erp.php">Mölndals vårdcentral</a>
        </div>
        <div id="loggautDiv">
          <a href="loggaut.php">Logga ut</a>
        </div>
      </div>
      <nav>
        <ul id="ulist">
          <li><a href="exempelfil_erp.php" class="headerlist">Framsidan</a></li>
          <li><a href="bokningar.php" class="headerlist">Bokade tider</a></li>
          <li><a class="headerlist selected">Om oss</a></li>
        </ul>
      </nav>
    </header>
