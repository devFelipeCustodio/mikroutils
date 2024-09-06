<?php
require '../src/search.php';
require '../src/user.php';

$query = $_GET['q'] ?? null;
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
    <title>Mikroutils :: <?php echo $query ?? "Home" ?></title>
</head>

<body>
    <?php require './navbar.php' ?>
    <div class="container">
        <form action="/">
            <div class="input-field">
                <input name="q" required id="search_query" type="search" class="validate" value="<?php echo $query ?>">
                <label for="search_query">Digite um usuário PPPOE</label>
            </div>
        </form>
        <div class="results">
            <?php if ($query) {
                $zabbix_search = new Search();
                $results = $zabbix_search->findUserByName($query);
                if ($zabbix_search->zabbix_error) {
                    echo "<p>Falha durante a conexão com o Zabbix.</p>";
                } else {
                    if ($zabbix_search->client_errors) {
                        echo "<button data-target=\"modal1\" class=\"btn-floating btn-large waves-effect waves-light red modal-trigger\"><i class=\"material-icons\">warning</i></button>
                            <div id=\"modal1\" class=\"modal\">
                                <div class=\"modal-content\">
                                <h2>Relatório de erros</h2>";
                        foreach ($zabbix_search->client_errors as $error) {
                            echo "<p>" . $error['gw_name'] . ": " . $error['error_message'] . "</p>";
                        }
                        ;
                        echo "</div>
                            </div>";
                    }
                    if (!$results) { {
                            echo "<p>Nenhum resultado encontrado.</p>";
                        }
                    } else {
                        echo "<table class=\"centered responsive-table\">
                        <thead>
                            <th>Gateway</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Caller ID</th>
                            <th>Uptime</th>
                        </thead>
                        <tbody>";
                        foreach ($results as $result) {
                            $gw_name = $result['gw_name'];
                            $gw_ip = $result['gw_ip'];
                            $name = $result['name'];
                            $address = $result['address'];
                            $caller_id = $result['caller_id'];
                            $uptime = $result['uptime'];
                            echo "<tr data-gw-ip=$gw_ip>
                                <td class=\"gw-name no-wrap\">$gw_name</td>
                                <td class=\"name\">$name</td>
                                <td class=\"address\">$address</td>
                                <td class=\"caller-id\">$caller_id</td>
                                <td class=\"uptime\">$uptime</td>
                                <td><a class=\"teal-text\" href=\"/user/?name=$name&gw=$gw_ip\"><i class=\"material-icons\">description</i></a></td>
                            </tr>";
                        }
                        echo "</table>";
                    }
                }
            }
            ?>
        </div>
    </div>
</body>

</html>