<?php
session_start();

$servername = "localhost";
$username = "root";
$passwordb = "";
$dbname = "dobra_banka";

$error = "";
$email = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $passwordb);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password == $user['password']) {
        $session_id = uniqid();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['session_id'] = $session_id;

        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="login.js" type="module"></script>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsSHA/2.0.2/sha.js"></script>
    <title>Dobra banka - Login</title>

    

</head>
<body>
    <div class="login-div main-div">
          <button class="createbtn" onClick="location.href='signup.php'"><span>Create an account</span></button>
        <h1>Login</h1>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="container">
              <label for="email"><b>Email</b></label>
              <input type="text" placeholder="Enter Email" name="email" value="<?php echo $email; ?>" id="username" required>
          
              <label for="password"><b>Password</b></label>
              <input type="password" id="password" placeholder="Enter Password" name="password" value="<?php echo $password; ?>" required>
              <img toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password" src="icons/eye1.png" alt="">
          
              <button id="submit" class="loginbtn">Login</button>
            </div>
            <?php if (!empty($error)) { ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php } ?>
</form>


    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>

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