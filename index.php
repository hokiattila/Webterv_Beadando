<!DOCTYPE html>
<?php     include_once("app/db.php");
          include_once("app/datacontroller.php");
          $controller = new DataController;
          $token = $controller->generateToken();
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
    <?php if(isset($_SESSION['username']) && isset($_SESSION['role'])):?>
        <p>Bejelentkezve mint: <i style="color: white; background-color: black"><b><?=$_SESSION['username']?></b></i>,  jogkör: <i style="color: white; background-color: black"><b><?=$_SESSION['role']?></b></i></p>
        <form method="post" action="app/datacontroller.php">
        <input type="submit" name="logout-btn" value="Kijelentkezés">
            <input type="hidden" name="token" value="<?= $token ?>"
        </form>
    <?php endif; ?>
    <h2><a href="car.php">Kocsik</a></h2>
    <?php if(empty($_SESSION['username']) || empty($_SESSION['role'])): ?>
          <h2> Bejelentkezés </h2>
           <form name="login_form" action="app/datacontroller.php" method="POST">
                   <input type="hidden" name="token" value="<?= $token ?>"
               <p>Felhasználónév</p>
                   <input type="text" name="username" placeholder="Felhasználónév"/>
               <p>Jelszó</p>
                   <input type="password" name="password" placeholder="Jelszó"/>
               <br>
               <input type="submit" name="login-btn" value="Bejelentkezés" style="margin-top: 15px"/>
           </form>
            <br><br>
            <h2> Regisztráció </h2>
            <form name="register_form" action="app/datacontroller.php" method="POST">
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