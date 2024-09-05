<?php

require '../../vendor/autoload.php';
require '../../src/user.php';

$result = (new User())->getUserByName($_GET['name']);
[
    "user" => $user,
    "interface" => $interface,
    "queue_name" => $queue_name,
    "last_link_up_time" => $last_link_up_time,
    "link_downs" => $link_downs,
    "rx_byte" => $rx_byte,
    "tx_byte" => $tx_byte,
    "local_address" => $local_address,
    "remote_address" => $remote_address,
    "logs" => $logs
] = $result;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@2.1.0/dist/css/materialize.min.css">
    <link rel="stylesheet" href="./style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script defer
        src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@2.1.0/dist/js/materialize.min.js"></script>
    <script
        src="https://unpkg.com/clipboard-polyfill/dist/es5/window-var/clipboard-polyfill.window-var.promise.es5.js"></script>
    <script defer src="./index.js"></script>
    <title>Mikroutils :: <?php echo $_GET['name'] ?></title>
</head>

<body>
    <div class="container">
        <main>
            <?php if ($user) {
                echo "<div class=\"data-container\">
            <h1 class=\"user\">$user</h1>
            <div class=\"grid-container\">
            <div class=\"data\"><span class=\"title\">local address: </span><span class=\"local-address\">";
                echo str_replace("/32", "", $local_address) . "</span></div>
            <div class=\"data\"><span class=\"title\">remote address:</span> <span class=\"remote-address\">$remote_address</span></div>
                    <div class=\"data\"><span class=\"title\">interface:</span> <span class=\"interface\">$interface</span></div>
                    <div class=\"data\"><span class=\"title\">queue:</span> $queue_name</div>
                    <div class=\"data\"><span class=\"title\">last link up time:</span> $last_link_up_time</div>
                    <div class=\"data\"><span class=\"title\">link downs:</span> $link_downs</div>
                    <div class=\"data\"><span class=\"title\">rx byte:</span> $rx_byte</div>
                    <div class=\"data\"><span class=\"title\">tx byte:</span> $tx_byte</div>
            </div>
            </div>
            <div class=\"log-container\">
            <h2>Logs</h2>
            <div class=\"table-container\">
            <table>
                <thead>
                <th>Time</th>
                <th>Topics</th>
                <th>Message</th>
                </thead>
                <tbody>";
                foreach ($logs as $log) {
                    $red_log = stripos($log['topics'], "error") !== false ? "style=\"color:red;\"" : null;
                    echo "<tr class=\"log\" $red_log> 
            <td class=\"log-time\">" . $log['time'] . "</td>
            <td>" . $log['topics'] . "</td>
            <td>";
                    echo str_replace("<", "&lt;", (str_replace(">", "&gt;", $log['message']))) . "</td>
            </tr>";
                }
                echo "</tbody>
        </table>
        </div>
        </div>";
            } else {
                echo "<p style=\"font-size:2rem
                ;\">Usuário inválido.</p>";
            } ?>
        </main>
    </div>
</body>

</html>