<?php
session_start();

$servername = "localhost";
$username = "root";
$passwordb = "";
$dbname = "dobra_banka";

$error = "";
$name = "";
$surname = "";
$email = "";
$password = "";
$trr = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trr = $_POST['trr'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passwordb);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $conn->prepare("INSERT INTO users (name, surname, email, password) VALUES (:name, :surname, :email, :password)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        $user_id = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO account (user_id, trr, balance) VALUES (:user_id, :trr, 1000)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':trr', $trr);
        $stmt->execute();

        $session_id = uniqid();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['session_id'] = $session_id;

        header("Location: dashboard.php");
        exit();
    }
}
?>



<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Dobra banka - Sign up</title>

    

</head>
<body>
    <div class="signup-div main-div">
        <h1>Sign up</h1>
          <button class="createdbtn createbtn" onClick="location.href='index.php'"><span>Login</span></button>

        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="container">
              <label for="email"><b>Email</b></label>
              <input type="text" placeholder="Enter Email" name="email" value="<?php echo $email; ?>" required>

              <label for="name"><b>Name</b></label>
              <input type="text" placeholder="Enter Username" name="name" value="<?php echo $name; ?>" required>              
          
              <label for="surname"><b>Surname</b></label>
              <input type="text" id="surname" placeholder="Enter Surname" name="surname" value="<?php echo $surname; ?>" required>              
          
          <label for="trr"><b>TRR</b></label>
          <input type="text" id="trr" placeholder="Enter TRR" name="trr" value="<?php echo $trr; ?>" required>
          
              <label for="password"><b>Password</b></label>
              <input type="password" id="password" placeholder="Enter Password" name="password" value="<?php echo $password; ?>" required>
              <img toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password" src="icons/eye1.png" alt="">
          
              <button type="submit" class="loginbtn">Sign Up</button>
            </div>           
    <?php if (!empty($error)) { ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php } ?> 
          </form>
          

    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
    <script>
    $(".toggle-password").click(function() {
     
      $(this).toggleClass("fa-eye fa-eye-slash");
      
      if($(this).attr("src") == "icons/eye1.png")
        $(this).attr("src", "icons/eye2.png");
      else $(this).attr("src", "icons/eye1.png");

      var input = $($(this).attr("toggle"));
      if (input.attr("type") == "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });    
    </script>
</body>
</html>




