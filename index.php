<!DOCTYPE html>
<?php     include_once("app/db.php");
          $db = new DatabaseInteractions;
          $db->dbInit();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Autókereskedés</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <script src="js/index.js"></script>
</head>
<body>
    <h1>Szia! Vegyél kocsit!</h1>
    <h2><a href="car.php">Kocsik</a></h2>
    <?php if(empty($_SESSION['user'])): ?>
          <h2> Bejelentkezés </h2>
           <form name="register_form" action="app/eventhandler.php" method="POST">
               <p>Felhasználónév</p>
                   <input type="text" name="username" placeholder="Felhasználónév" required/>
               <p>Jelszó</p>
                   <input type="password" name="password" placeholder="Jelszó" required/>
               <br>
               <input type="submit" name="login-btn" value="Bejelentkezés" style="margin-top: 15px"/>
           </form>
            <br><br>
            <h2> Regisztráció </h2>
            <form name="register_form" action="app/eventhandler.php" method="POST">
                <p>Felhasználónév</p>
                    <input type="text" name="username" placeholder="Felhasználónév" required/>
                <p>Jelszó</p>
                    <input type="password" name="password" placeholder="Jelszó" required/>
                <p>Jelszó megerősítése</p>
                    <input type="password" name="password_conf" placeholder="Jelszó Megerősítés" required/>
                <br>
                <input type="submit" name="register-btn" value="Regisztráció" style="margin-top: 15px"/>
            </form>
            <br><br>
    <?php endif;?>
</body>
</html>