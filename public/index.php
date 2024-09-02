<?php 
require '../src/client_search.php'; 
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
    <script src="https://unpkg.com/clipboard-polyfill/dist/es5/window-var/clipboard-polyfill.window-var.promise.es5.js"></script>
    <script defer src="./index.js"></script>
    <title>Mikroutils</title>
</head>
<body>
    <main class="container">
        <form action="index.php">
            <div class="input-field">
                <input name="q" id="search_query" type="search" class="validate" value="<?php echo $_GET['q']?>">
                <label for="search_query">Digite um usuário PPPOE</label>
            </div>
        </form>
        <div class="results">
            <?php if ($_GET['q'])
            {
                $results = (new ClientSearch())->findUserByPPPOE($_GET['q']);
                // $results = [["gw_name" => "gw_test", "gw_ip" => "0.0.0.0",
                // "name" => "teste@afinet.com.br", "address" => "0.0.0.0", 
                // "caller_id" => "00:00:00:00:00:00", "uptime" => "0h00m00s"]];
                echo "<table class=\"centered responsive-table\">
                    <thead>
                        <th>Gateway</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Caller ID</th>
                        <th>Uptime</th>
                    </thead>
                    <tbody>";
                foreach ($results as $result){
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
                            <td><a href=\"/client.php?teste=aaa\"><i class=\"material-icons\">description</i></a></td>
                        </tr>";  
                    }      
                echo "</table>";
            }
                ?>
        </div>
    </main>
</body>
</html>