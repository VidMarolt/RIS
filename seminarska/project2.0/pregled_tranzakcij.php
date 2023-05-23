<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>DB - Pregled tranzakcij</title>
</head>
<body>
    <div id="back" class="loginbtn" onClick="location.href='index.html'"><p>Nazaj</p></div>
    <div class="dashboard-div main-div">
        <div id="transactions">
            <div class="transaction transaction-header">
                <p>Prejemnik</p>
                <p>Pošiljatelj</p>
                <p>Datum</p>
                <p>Vsota</p>
            </div>
            <hr>
            

        <?php
        
        session_start();
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "dobra_banka";
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php");
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $session_id = $_SESSION['session_id'];
        
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $userId = $_SESSION['user_id'];

                $stmt = $conn->prepare("SELECT t.amount, u1.name AS sender_name, u1.surname AS sender_surname, u2.name AS receiver_name, u2.surname AS receiver_surname 
                        FROM transaction t
                        INNER JOIN account a1 ON t.sender_id = a1.id
                        INNER JOIN account a2 ON t.receiver_id = a2.id
                        INNER JOIN users u1 ON a1.user_id = u1.id
                        INNER JOIN users u2 ON a2.user_id = u2.id
                        WHERE a1.user_id = :user_id OR a2.user_id = :user_id");

                $stmt->bindParam(':user_id', $userId);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $senderName = $row['sender_name'];
                    $senderSurname = $row['sender_surname'];
                    $receiverName = $row['receiver_name'];
                    $receiverSurname = $row['receiver_surname'];
                    $amount = $row['amount'];

                    echo '<div class="transaction">';
                    echo '<p>' . $receiverName . ' ' . $receiverSurname . '</p>';
                    echo '<p>' . $senderName . ' ' . $senderSurname . '</p>';
                    echo '<p>' . $amount . ' eur</p>';
                    echo '</div>';
                }
            ?>
        </div>
    </div>
    <script src="index.js" type="module"></script>
</body>
</html>