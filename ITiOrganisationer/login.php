<html>
  <head>
    <meta charset="UTF-8" />
    <title>Journal</title>
    <link href="style.css" rel="stylesheet">
    <script src="javaScript.js"></script>
  </head>
  <body>
    <header>
      <div id="banner">Mölndals Vårdcentral</div>
      <nav>
        <div class="dropdown">
          <button onclick="dropdownToggle()" class='dropbtn'><img src="Icons/dropdownMeny.png" /></button>
          <div id="dropdownOptions" class="dropdown-content">
            <a href="exempelfil_erp.php">Startsida</a>
            <a href="exempelfil_erp.php">Bokade tider</a>
            <a href="exempelfil_erp.php">Journal</a>
          </div>
        </div>
      </nav>
    </header>
        <div class="container">
            <form action="verifiering.php" method="POST">
                <h1><div>LOGIN</div></h1>
                <div>
                    <input type="text" name="user" placeholder="Personnummer" />
                </div>
                <input type="submit" id="submit" />
            </form> 
        </div>
    </body>
</html>