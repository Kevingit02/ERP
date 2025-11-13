<?php

    session_start();
    if (empty($_SESSION['uid'])){
      echo "Inte inloggad än, gå tillbaka till <a href='login.php'>inloggningssidan</a>";
    }else{
      $uid = $_SESSION['uid'];
      $name = $_SESSION['name'];
    }
    if (!empty($_SESSION['uid'])){
?>
<html>
  <head>
    <meta charset="UTF-8" />
    <title>Hantera bokade tider</title>
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
    if (isset($_GET['appointment']) && isset($_GET['typ']) && $_GET['typ'] == 'avboka'){
      $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment?filters=[["name","=","'.$_GET['appointment'].'"]]&fields=["appointment_date","practitioner_name"]');
    }else if(isset($_GET['appointment']) && isset($_GET['typ']) && $_GET['typ'] == 'bokaom'){
      $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment?filters=[["name","=","'.$_GET['appointment'].'"]]&fields=["appointment_date","practitioner_name"]');
    }

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
    curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


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
    echo "<a href='bokningar.php'>Gå tillbaka till bokade tider</a><br />";
    if (isset($_GET['typ']) && isset($_GET['appointment'])){
      if ($_GET['typ'] == 'avboka'){
        foreach($response['data'] AS $key=>$value){
          echo "<form action='hanteratid.php' method='POST'>";
          echo "<h2>Är du säker att du vill avboka din bokade tid: ".$value['appointment_date']." med ".$value['practitioner_name']."?</h2>";
          echo "<input type='submit' name='ja' value='ja' />";
          echo "<input type='submit' name='nej' value='nej' />";
          echo "<input type='text' name='appointment_name' value='".$_GET['appointment']."' hidden />";
          echo "</form>";
        }
      }else if($_GET['typ'] == 'bokaom'){
        echo "Vilket datum vill du boka om till?";
        echo "<form action='hanteratid.php?appointment=".$_GET['appointment']."&typ=".$_GET['typ']."' method='POST'>";
        echo "<input type='date' name='datum' />";
        echo "<input type='submit' />";
        echo "</form>";
        foreach($response['data'] AS $key=>$value){
          $practitioner = $value['practitioner_name'];
          break;
        }
        if (isset($_POST['datum'])){
          /* Hämtar alla bokade tider på datumet */
          $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment?fields=["appointment_datetime","appointment_time"]&filters='.urlencode('[["practitioner_name","=","'.$practitioner.'"], ["appointment_date","=","'.$_POST['datum'].'"]]'));
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
          curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
          curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
          curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
          curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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

          $bokadetider = array();
          foreach($response['data'] AS $value){
            $bokadetider[] = $value['appointment_time'];
          }

          $dateday = date('l', strtotime(str_replace('-', '/', $_POST['datum'])));

          $ch = curl_init($baseurl . 'api/resource/Practitioner%20Schedule/G5%20Schema%20Läkare?fields=["*"]');
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

          curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
          curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
          curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
          curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
          curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
          echo "<h2>".$_POST['datum']."</h2>";
          echo "<div class='bokningstider'>";
          foreach($response['data']['time_slots'] AS $value){
            if ($value['day'] == $dateday){
              $dt1 = DateTime::createFromFormat('H:i:s', $value['from_time']);
              $dt2 = DateTime::createFromFormat('H:i:s', $value['to_time']);
              if (in_array($value['from_time'], $bokadetider)){
                echo "<div class='tidsruta bokad'>".$dt1->format('H:i')."-".$dt2->format('H:i')."</div>";
              }else{
                echo "<div class='tidsruta' onclick='selected(this)'>".$dt1->format('H:i')."-".$dt2->format('H:i')."</div>";
              }
            }
          }
          echo "</div>";
          echo "<a href=hanteratid.php id='skickabutton'>Skicka</a>";
        }
      }else if(isset($_POST['ja'])){
        $ch = curl_init($baseurl . 'api/resource/Patient%20Appointment/'.$_POST['appointment_name']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
        curl_setopt($ch, CURLOPT_TIMEOUT, $tmeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
        echo "Din tid är nu avbokad, du kommer skickas tillbaka till dina bokade tider nu!";
        header( "refresh:3;url=bokningar.php" );
      }else if(isset($_POST['nej'])){
        echo "Okej, din tid kommer ej avbokas. Klicka <a href='bokningar.php'>här</a> för att gå tillbaka till dina bokade tider.";
      }else{
        echo "Tid hittades inte, gå tillbaka till <a href='bokningar.php'>dina bokade tider</a>";
      }
    }
  }

?>
</body>
</html>