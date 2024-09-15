<?php

require dirname(__FILE__, 3) . '/vendor/autoload.php';
require dirname(__FILE__, 3) . '/src/user.php';

$gw = htmlspecialchars($_GET['gw'] ?? '');
$name = htmlspecialchars($_GET['name'] ?? '');

$pppoe_user = new User();
$result = $pppoe_user->getUserByName($name);

[
    "user" => $user,
    "caller_id" => $caller_id,
    "manufacturer" => $manufacturer,
    "gateway" => $gateway,
    "uptime" => $uptime,
    "interface" => $interface,
    "max_limit" => $max_limit,
    "last_link_up_time" => $last_link_up_time,
    "rx_byte" => $rx_byte,
    "tx_byte" => $tx_byte,
    "local_address" => $local_address,
    "remote_address" => $remote_address,
    "logs" => $logs
] = $result;

$has_logs = $logs ? "" : "hide";
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
    <script defer
        src="https://unpkg.com/clipboard-polyfill/dist/es5/window-var/clipboard-polyfill.window-var.promise.es5.js"></script>
    <script defer src="./index.js"></script>
    <title>Mikroutils :: <?php echo $user ?? "Usuário inválido" ?></title>
</head>

<body>
    <?php require '../navbar.php'; ?>
    <div class="container">
        <main>
            <?php if (!$pppoe_user->has_router_instance()) {
                echo "<p style=\"font-size:2rem;\">GW inválido ou inacessível.</p>";
            } else if (!$user) {
                echo "<p style=\"font-size:2rem;\">Usuário inválido.</p>";
            } else {
                echo "
            <div class=\"data-container\">
            <div class=\"flex-container\">
            <h1 class=\"user\">$user</h1>
            <button autofocus class=\"waves-effect waves-light btn-large btn-back\"><i class=\"material-icons left\">arrow_back</i>voltar</button>
            </div>
            <div class=\"grid-container\">
            <div class=\"data\"><span class=\"title\">gateway identity: </span><span data-gw-ip=\"$gw\" class=\"gateway\">$gateway</span></div>
            <div class=\"data\"><span class=\"title\">caller ID: </span><span class=\"caller-id\">$caller_id</span></div>
            <div class=\"data\"><span class=\"title\">remote address:</span> <span class=\"remote-address\">$remote_address</span></div>
            <div class=\"data\"><span class=\"title\">local address: </span><span class=\"local-address\">";
                echo str_replace("/32", "", $local_address) . "</span></div>
            <div class=\"data\"><span class=\"title\">interface:</span> <span class=\"interface\">$interface</span></div>
            <div class=\"data\"><span class=\"title\">manufacturer: </span><span class=\"manufacturer\">$manufacturer</span></div>
            <div class=\"data\"><span class=\"title\">uptime: </span><span class=\"uptime\">$uptime</span></div>
            <div class=\"data\"><span class=\"title\">queue:</span> $max_limit</div>
            <div class=\"data\"><span class=\"title\">rx byte:</span> $rx_byte</div>
            <div class=\"data\"><span class=\"title\">tx byte:</span> $tx_byte</div>
            <div class=\"data\"><span class=\"title\">last link up time:</span> $last_link_up_time</div>
            </div>
            <div class=\"log-container $has_logs\">
            <h2>Logs</h2>
            <div class=\"table-container\">
            <table>
                <thead>
                <tr>
                <th>Time</th>
                <th>Topics</th>
                <th>Message</th>
                </tr>
                </thead>
                <tbody>";
                foreach ($logs as $log) {
                    $red_log = stripos($log['topics'], "error") !== false ? "style=\"color:red;\"" : "";
                    echo "<tr class=\"log\" $red_log> 
            <td class=\"log-time\">" . htmlspecialchars($log['time']) . "</td>
            <td>" . htmlspecialchars($log['topics']) . "</td>
            <td>" . htmlspecialchars($log['message']) . "</td>
            </tr>";
                }
                echo "</tbody>
        </table>
        </div>
        </div>";
            } ?>
        </main>
    </div>
</body>

</html>
