<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = "";
$dbname = "dobra_banka";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $trr = $_POST["trr"];
    $amount = $_POST["amount"];

    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT * FROM account WHERE trr = :trr");
        $stmt->bindParam(':trr', $trr);
        $stmt->execute();
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            throw new Exception("Account with TRR '$trr' does not exist.");
        }

        $userId = $account['user_id'];

        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id AND name = :name AND surname = :surname");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Name and surname do not match the account with TRR '$trr'.");
        }

        $receiverBalance = $account['balance'] + $amount;

        $senderId = $_SESSION['user_id'];

        $stmt = $conn->prepare("SELECT * FROM account WHERE user_id = :id");
        $stmt->bindParam(':id', $senderId);
        $stmt->execute();
        $senderAccount = $stmt->fetch(PDO::FETCH_ASSOC);

        if($senderId == $userId){
          throw new Exception("You cannot transfer yourself cash.");
        }

        $stmt = $conn->prepare("UPDATE account SET balance = :receiver_balance WHERE trr = :trr");
        $stmt->bindParam(':receiver_balance', $receiverBalance);
        $stmt->bindParam(':trr', $trr);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE account SET balance = balance - :amount WHERE user_id = :user_id");
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':user_id', $senderId);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO transaction (sender_id, receiver_id, amount) VALUES (:sender_id, :receiver_id, :amount)");
        $stmt->bindParam(':sender_id', $senderAccount['id']);
        $stmt->bindParam(':receiver_id', $account['id']);
        $stmt->bindParam(':amount', $amount);
        $stmt->execute();

        $conn->commit();

        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();

        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>DB - Nova tranzakcija</title>
</head>
<body>
    <div id="back" class="loginbtn" onClick="location.href='dashboard.php'"><p>Nazaj</p></div>

    <div class="main-div trans-div">
        <form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
          <div class="container">
            <label for="name"><b>Prejemnikov ime:</b></label>
            <input type="text" placeholder="Vnesi ime" name="name" id="ime" required>
            
            <label for="surname"><b>Prejemnikov priimek:</b></label>
            <input type="text" placeholder="Vnesi priimek" name="surname" id="priimek" required>
            
            <label for="trr"><b>Prejemnikov trr:</b></label>
            <input type="text" placeholder="Vnesi trr" name="trr" id="trr" required>
        
            <label for="amount"><b>Vsota v eur:</b></label>
            <input type="text" id="amount" placeholder="Vnesi vsoto" name="amount" required>
        
            <button id="izvedi" class="loginbtn">Izvedi tranzakcijo</button>
          </div>
          
    <?php if (isset($error)): ?>
        <div><?php echo $error; ?></div>
    <?php endif; ?>
        </form>
    <script src="nova_tranzakcija.js" type="module"></script>
</body>
</html>