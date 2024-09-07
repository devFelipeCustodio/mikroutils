<?php
require '../src/search.php';
require '../src/user.php';

$query = $_GET['q'] ?? '';
$filter = $_GET['filter'] ?? 'pppoe';
$gateway = $_GET['gateway'] ?? '';

$valid_filters = ['pppoe', 'mac', 'ip'];
if (!in_array($filter, $valid_filters)) {
    $filter = 'pppoe';
}

$search = new Search();
$gateways = $search->gateways;
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
    <title>Mikroutils :: <?php echo htmlspecialchars($query) ?: "Home" ?></title>
</head>

<body>
    <?php require './navbar.php' ?>
    <div class="container">
        <form action="/" method="GET">
            <div style="display: flex; align-items: center;">
                <div class="input-field" style="display: inline-block; width: 200px; margin-right: 10px;">
                    <input name="q" required id="search_query" type="search" class="validate"
                        value="<?php echo htmlspecialchars($query) ?>">
                    <label for="search_query">Digite um usuário PPPoE</label>
                </div>

                <div class="input-field" style="display: inline-block; width: 200px; margin-right: 10px;">
                    <select name="gateway" id="gateway">
                        <option value="todos">Todos</option>
                        <?php
                        foreach ($gateways as $value => $gw) {
                            echo '<option value="' . htmlspecialchars($value) . '" ' . ($gateway === htmlspecialchars($value) ? 'selected' : '') . '>' . htmlspecialchars($gw['name']) . '</option>';
                        }
                        ?>
                    </select>
                    <label>Gateway</label>
                </div>

                <div class="input-field" style="display: inline-block; width: 200px; margin-right: 10px;">
                    <select name="filter" id="filter">
                        <option value="pppoe" <?php echo $filter === 'pppoe' ? 'selected' : '' ?>>PPPoE</option>
                        <option value="mac" <?php echo $filter === 'mac' ? 'selected' : '' ?>>MAC</option>
                        <option value="ip" <?php echo $filter === 'ip' ? 'selected' : '' ?>>IP</option>
                    </select>
                    <label>Filtrar por</label>
                </div>

                <button type="submit" class="btn waves-effect waves-light">Pesquisar</button>
            </div>
        </form>

        <div class="results">
            <?php if ($query) {
                $zabbix_search = new Search();
                $results = $zabbix_search->findUserByFilter($query, $filter);
                if ($zabbix_search->zabbix_error) {
                    echo "<p>Falha durante a conexão com o Zabbix.</p>";
                } else {
                    if ($zabbix_search->client_errors) {
                        echo "<button data-target=\"modal1\" class=\"btn-floating btn-large waves-effect waves-light red modal-trigger\"><i class=\"material-icons\">warning</i></button>
                            <div id=\"modal1\" class=\"modal\">
                                <div class=\"modal-content\">
                                <h2>Relatório de erros</h2>";
                        foreach ($zabbix_search->client_errors as $error) {
                            echo "<p>" . htmlspecialcharshtmlspecialchars($error['gw_name']) . ": " . htmlspecialchars($error['error_message']) . "</p>";
                        }
                        echo "</div>
                        </div>";
                    }
                    if (!$results) {
                        echo "<p>Nenhum resultado encontrado.</p>";
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
                            $gw_name = htmlspecialchars($result['gw_name']);
                            $gw_ip = htmlspecialchars($result['gw_ip']);
                            $name = htmlspecialchars($result['name']);
                            $address = htmlspecialchars($result['address']);
                            $caller_id = htmlspecialchars($result['caller_id']);
                            $uptime = htmlspecialchars($result['uptime']);
                            echo "<tr data-gw-ip=\"$gw_ip\">
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var elems = document.querySelectorAll('select');
            var instances = M.FormSelect.init(elems, {});
        });
    </script>
</body>

</html>