<?php
    session_start();
?>

<html>
    <head>
        <meta charset="UTF-8" />
        <title>Verifierar...</title>
        <link href="style.css" rel="stylesheet">
    </head>
    <body>
      
        <?php
            if (!empty($_POST['user'])){
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
                $ch = curl_init($baseurl . 'api/resource/Patient?filters=[["uid","=","'.$_POST['user'].'"]]&fields=["uid","name"]&limit=none');
            
                
            
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

                echo "<div id='verifieringsdiv'>";
                echo "<h1>Verifierar med BankID</h1><span class='loader'></span>";
                if (empty($response['data'])){
                    echo "<h3>Verifiering misslyckades</h3>";
                    header( "refresh:5;url=login.php" );
                }else{
                    echo "<h3>Verifiering lyckades</h3>";
                    header( "refresh:5;url=exempelfil_erp.php" );
                    foreach ($response['data'] as $key=>$value){
                        $_SESSION['uid'] = $value['uid'];
                        $_SESSION['name'] = $value['name'];
                    }
                }
                echo "</div>";
            }else{
                echo "Inte inloggad än, gå tillbaka <a href='login.php'>inloggningssidan</a>";
            }
            
        ?>
    </body>
</html>
