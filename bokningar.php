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

<html>
  <head>
    <meta charset="UTF-8" />
    <title>Journal</title>
    <link href="style.css" rel="stylesheet">
    <script src="javaScript.js"></script>
  </head>
  <body>
    <header>
        <a href="index.html"><div id="banner">Mölndals vårdcentral</div></a>
        <nav>
            <ul id="ulist">
                <li><a href="exempelfil_erp.php" class="headerlist">Framsidan</a></li>
                <li><a class="headerlist selected">Bokade tider</a></li>
                <li><a href="om.html" class="headerlist">Om oss</a></li>
            </ul>
        </nav>
    </header>


<?php

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  $cookiepath = "/tmp/cookies.txt";
  $tmeout = 3600; // (3600=1hr)
  // här sätter ni er domän
  $baseurl = 'http://193.93.250.83:8080/';

  try {
    $ch = curl_init($baseurl . 'api/method/login');
  } catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage(), "\n";
  }

  curl_setopt($ch, CURLOPT_POST, true);
  //  ----------  Här sätter ni era login-data ------------------ //
  curl_setopt($ch, CURLOPT_POSTFIELDS, '{"usr":"a24felbo@student.his.se", "pwd":"lösenord123"}'); 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
  curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $response = curl_exec($ch);
  $response = json_decode($response, true);

  $error_no = curl_errno($ch);
  $error = curl_error($ch);
  curl_close($ch);

  if (!empty($error_no)) {
    echo "<div style='background-color:red'>";
    echo '$error_no<br>';
    var_dump($error_no);
    echo "<hr>";
    echo '$error<br>';
    var_dump($error);
    echo "<hr>";
    echo "</div>";
  }

  #$ch = curl_init($baseurl . 'api/resource/Patient?fields=["*"]&limit_page_length=999');

  $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment?filters=[["patient","=","'.urlencode($name).'"]]&fields=["name","appointment_date","appointment_time","practitioner_name","appointment_type"]');

  // man kan även specificera vilka fält man vill se
  // urlencode krävs när du har specialtecken eller mellanslag
  // $ch = curl_init($baseurl . 'api/resource/User?fields='. urlencode('["name", "first_name", "last_login"]'));
  // det funkerar lika bra att ta bort mellanslaget i denna fråga
  // $ch = curl_init($baseurl . 'api/resource/User?fields=["name","first_name","last_login"]');

  //jag kör en get request, ibland vill man kanske köra en annan typ av request, och ibland så beöver man ha med postfields
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
  curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
  curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
  curl_setopt($ch, CURLOPT_TIMEOUT, value: $tmeout);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


  $response = curl_exec($ch);
  //echo $response;
  $response = json_decode($response, true);

  $error_no = curl_errno($ch);
  $error = curl_error($ch);
  curl_close($ch);

  if (!empty($error_no)) {
    echo "<div style='background-color:red'>";
    echo '$error_no<br>';
    var_dump($error_no);
    echo "<hr>";
    echo '$error<br>';
    var_dump($error);
    echo "<hr>";
    echo "</div>";
  }
  
  echo "<div id='bokadetider'>";
  echo "<strong>Bokningar:</strong><br>";
  if (!empty($response['data'])){
    echo "<table>";
    echo "<th>Datum</th><th>Tid</th><th>Läkare</th><th>Typ av besök</th>";
    foreach($response['data'] AS $key => $value){
      echo "<tr>";
      echo "<td>".$value["appointment_date"]."</td>";
      echo "<td>".$value["appointment_time"]."</td>";
      echo "<td>".$value["practitioner_name"]."</td>";
      echo "<td>".$value["appointment_type"]."</td>";
      echo "<td><a href='hanteratid.php?appointment=".$value["name"]."&typ=bokaom'>Boka om</a></td>";
      echo "<td><a href='hanteratid.php?appointment=".$value["name"]."&typ=avboka'>Avboka</a></td>";
      echo "</tr>";
    }
    echo "</table>";
  }else{
    echo "Du har inga registrerade bokningar just nu.";
  }
  echo "</div>";
  

}
?>
  </body>
</html>
