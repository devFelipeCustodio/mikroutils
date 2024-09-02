<?php 

require '../../vendor/autoload.php';
require '../../src/user.php'; 

$user = (new User())->getUserByName($_GET['name']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <title>Mikroutils :: <?php $_GET['name']?></title>
</head>
<body>
    <main class="container">
        <?php var_dump($user) ?>
    </main>
</body>
</html>