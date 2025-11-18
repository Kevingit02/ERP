<html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
            form { background: white; padding: 20px; border-radius: 8px; max-width: 700px; margin: auto; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
            h2 { color: #2b6cb0; }
            fieldset { margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; }
            legend { font-weight: bold; }
            label { display: block; margin-top: 10px; }
            textarea { width: 100%; height: 80px; }
            input[type="submit"] { background-color: #2b6cb0; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
            input[type="submit"]:hover { background-color: #1a4f8b; }
        </style>
    </head>
    <body>
        <form method="POST" action="uploadFormTest.php">
            <fieldset>
                <legend>Övergripande symptom för kontakt</legend>

                <label>
                    Personnummer (10 siffror):
                    <input type='text' name='personnummer' placeholder='XXXXXX-XXXX' />
                </label>

                <label>Har du haft feber i över sju dygn?</label>
                <input type="radio" name="FeberÖver7Dygn" value="1"> Ja
                <input type="radio" name="FeberÖver7Dygn" value="0"> Nej

                <label>Har du hosta?</label>
                <input type="radio" name="Hosta" value="1"> Ja
                <input type="radio" name="Hosta" value="0"> Nej

                <label>Kommer det blod när du hostar?</label>
                <input type="radio" name="BlodVidHosta" value="1"> Ja
                <input type="radio" name="BlodVidHosta" value="0"> Nej

                <label>Känns det tungt när du andas?</label>
                <input type="radio" name="TungAndning" value="1"> Ja
                <input type="radio" name="TungAndning" value="0"> Nej

                <label>Har du muskelvärk och/eller huvudvärk?</label>
                <input type="radio" name="MuskelvärkEllerHuvudvärk" value="1"> Ja
                <input type="radio" name="MuskelvärkEllerHuvudvärk" value="0"> Nej

                <label>Har du varit sjuk i mer än 7 dagar?</label>
                <input type="radio" name="SjukMerÄn7Dagar" value="1"> Ja
                <input type="radio" name="SjukMerÄn7Dagar" value="0"> Nej

                <label>Tar du några mediciner, om ja ange vilka:</label>
                <input type="text" name="Mediciner-FysiskHälsa">

                <label>Beskriv dina besvär (max 150 ord):</label>
                <textarea name="Beskrivning-FysiskHälsa" maxlength="1000"></textarea>
            </fieldset>

            <fieldset>
                <legend>Övergripande symptom för kontakt med kurator</legend>

                <label>Känner du dig nedstämd?</label>
                <input type="radio" name="Nedstämd" value="1"> Ja
                <input type="radio" name="Nedstämd" value="0"> Nej

                <label>Känner du av ångest och oro?</label>
                <input type="radio" name="ÅngestOchOro" value="1"> Ja
                <input type="radio" name="ÅngestOchOro" value="0"> Nej

                <label>Tar du några mediciner, om ja ange vilka:</label>
                <input type="text" name="Mediciner-PsykiskHälsa">

                <label>Beskriv dina besvär (max 150 ord):</label>
                <textarea name="Beskrivning-PsykiskHälsa" maxlength="1000"></textarea>
            </fieldset>

            <input type="submit" name="submit" value="Skicka in">
        </form>

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
            if (isset($_POST['personnummer'])){
                $ch = curl_init($baseurl . 'api/resource/G5Bokningsforfragningar');
                echo "posted";
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, 
                '{
                    "personnummer":"'.$_POST['personnummer'].'",
                    "feber7dagar":"'.$_POST['FeberÖver7Dygn'].'",
                    "hosta":"'.$_POST['Hosta'].'",
                    "blodhosta":"'.$_POST['BlodVidHosta'].'",
                    "andastungt":"'.$_POST['TungAndning'].'",
                    "muskelhuvudvärk":"'.$_POST['MuskelvärkEllerHuvudvärk'].'",
                    "sjuk7dagar":"'.$_POST['SjukMerÄn7Dagar'].'",
                    "mediciner1":"'.$_POST['Mediciner-FysiskHälsa'].'",
                    "besvär1":"'.$_POST['Beskrivning-FysiskHälsa'].'",
                    "nedstämd":"'.$_POST['Nedstämd'].'",
                    "ångestoro":"'.$_POST['ÅngestOchOro'].'",
                    "mediciner2":"'.$_POST['Mediciner-PsykiskHälsa'].'",
                    "besvär2":"'.$_POST['Beskrivning-PsykiskHälsa'].'"
                }');
            
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
                echo "<div style='background-color:lightgray; border:1px solid black;'>";
                echo '$response<br><pre>';
                echo print_r($response)."</pre><br>";
                echo "</div>";
            }
        ?>
    </body>
</html>