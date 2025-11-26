<?php
session_start();

// Om ej inloggad=stoppar
if (empty($_SESSION['uid'])) {
    echo "Inte inloggad än, gå tillbaka till <a href='login.php'>inloggningssidan</a>";
    exit;
}

$personnummer = $_SESSION['uid'];
$patientName = $_SESSION['name'];

?>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Journal</title>
    <link href="style.css" rel="stylesheet">
    <script src="javaScript.js"></script>

<style>
    .journal-container {
        width: 60%;
        margin: 40px auto;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }

    .journal-header {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .journal-table {
        width: 100%;
        border-collapse: collapse;
    }

    .journal-table td {
        padding: 10px;
        border: 1px solid #000;
        vertical-align: top;
    }

    .renew-btn {
        padding: 6px 12px;
        background-color: #2c5b72;
        border-radius: 5px;
        color: white;
        cursor: pointer;
        display: inline-block;
        margin-top: 8px;
    }

    .renew-btn:hover {
        background-color: #163645;
    }
</style>
</head>

<body>

<header>
    <a href="index.html"><div id="banner">Mölndals vårdcentral</div></a>
    <nav>
        <ul id="ulist">
            <li><a href="exempelfil_erp.php" class="headerlist">Framsidan</a></li>
            <li><a href="bokningar.php" class="headerlist">Bokade tider</a></li>
            <li><a class="headerlist selected">Journal</a></li>
            <li><a href="om.html" class="headerlist">Om oss</a></li>
        </ul>
    </nav>
</header>

<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$cookiepath = "/tmp/cookies.txt";
$baseurl = 'http://193.93.250.83:8080/';

$ch = curl_init($baseurl . 'api/method/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS,
    '{"usr":"a24kevwe@student.his.se", "pwd":"lösenord123"}'
);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

$url = $baseurl .
    'api/resource/Patient?filters=[["uid","=","'.$personnummer.'"]]'.
    '&fields=["*"]';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (empty($data['data'])) {
    echo "<div class='journal-container'><h2>Ingen patient hittades!</h2></div>";
    exit;
}

$p = $data['data'][0];

if (isset($_POST['renew_med'])) {

    $medName = $_POST['renew_med'];

    $sendData = [
        "patient" => $p["patient_name"],
        "patient_uid" => $p["uid"],
        "medication_name" => $medName
    ];

    $ch = curl_init($baseurl . "api/resource/G5Renewprescription");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sendData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $res = curl_exec($ch);
    curl_close($ch);

    echo "<div class='journal-container'>
            <h3>Förnyelse skickad!</h3>
            <p>Receptförnyelse begärd för: <b>$medName</b></p>
          </div>";
}

$visa = [
    "patient_name" => "Namn",
    "sex" => "Kön",
    "dob" => "Födelsedatum",
    "uid" => "Personnummer",
    "allergies" => "Allergier",
    "medication" => "Mediciner",
    "medical_history" => "Medicinsk historik",
    "surgical_history" => "Kirurgisk historik",
    "tobacco_past_use" => "Tobak (tidigare)",
    "tobacco_current_use" => "Tobak (nuvarande)",
    "alcohol_past_use" => "Alkohol (tidigare)",
    "alcohol_current_use" => "Alkohol (nuvarande)",
    "surrounding_factors" => "Miljöfaktorer",
    "other_risk_factors" => "Övriga riskfaktorer"
];

echo "<div class='journal-container'>";
echo "<div class='journal-header'>Din Journal:</div>";
echo "<table class='journal-table'>";

foreach ($visa as $field => $label) {

    if (!empty($p[$field])) {

        echo "<tr>";
        echo "<td>$label</td>";
        echo "<td>";

        if ($field === "medication") {

            $medList = explode("\n", $p["medication"]);

            foreach ($medList as $med) {
                $med = trim($med);
                if ($med === "") continue;

                echo "$med";

                echo "
                <form method='POST' style='display:inline'>
                    <input type='hidden' name='renew_med' value='$med'>
                    <button class='renew-btn'>Förnya</button>
                </form><br>";
            }

        } else {
            echo nl2br($p[$field]);
        }

        echo "</td></tr>";
    }
}

echo "</table>";
echo "</div>";

$apiUrlRenewAll = $baseurl .
    'api/resource/G5Renewprescription?filters=' .
    urlencode('[["patient_uid","=","'.$personnummer.'"]]') .
    '&fields=["patient","patient_uid","medication_name","status","creation"]';

$ch = curl_init($apiUrlRenewAll);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiepath);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiepath);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$responseRenewAll = curl_exec($ch);
curl_close($ch);

$dataRenewAll = json_decode($responseRenewAll, true);

echo "<div class='journal-container' style='margin-top:30px'>";
echo "<div class='journal-header'>Dina receptförnyelser:</div>";

if (empty($dataRenewAll['data'])) {
    echo "<p>Du har inga registrerade receptförnyelser.</p>";
} else {
    echo "<table class='journal-table'>";
    echo "<tr>
            <td><strong>Medicin</strong></td>
            <td><strong>Status</strong></td>
            <td><strong>Datum</strong></td>
          </tr>";

    foreach ($dataRenewAll['data'] as $renew) {

        $status = $renew["status"] ?? "Unknown";
        $color = ($status == "Approved") ? "green" :
                 (($status == "Pending") ? "orange" :
                 (($status == "Rejected") ? "red" : "black"));

        $med   = $renew["medication_name"] ?? "";
        $datum = substr($renew["creation"], 0, 10);

        echo "<tr>";
        echo "<td>$med</td>";
        echo "<td style='color:$color; font-weight:bold;'>$status</td>";
        echo "<td>$datum</td>";
        echo "</tr>";
    }

    echo "</table>";
}

echo "</div>";

?>

</body>
</html>
