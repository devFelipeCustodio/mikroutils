<?php

require 'utils.php';

use \RouterOS\Client;
use \RouterOS\Config;
use \RouterOS\Query;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

class User
{
    private $router_instance;

    public function __construct()
    {
        $config = new Config([
            'host' => $_GET['gw'],
            'user' => $_ENV["LOGIN"],
            'pass' => $_ENV["PASSWORD"],
            'port' => 8728,
            'attempts' => 1,
            'socket_timeout' => 2,
            'timeout' => 2
        ]);
        $this->router_instance = new Client($config);
    }

    public function getUserByName($name)
    {
        $if = $this->getInterfaceDataByName($name)[0] ?? [];
        $mac = $if['remote-address'];
        $queue = $this->getQueueDataByName($name)[0] ?? [];
        $traffic = $this->getTrafficDataByName($name)[0] ?? [];
        $addresses = $this->getAddressesDataByName($name)[0] ?? [];
        $logs = $this->getLogDataByName($name, $mac) ?? [];
        return [
            'user' => $if['user'],
            'interface' => $if['interface'],
            'queue_name' => $queue['name'],
            'last_link_up_time' => $traffic['last-link-up-time'],
            'link_downs' => $traffic['link-downs'],
            'rx_byte' => formatBytes($traffic['rx-byte']),
            'tx_byte' => formatBytes($traffic['tx-byte']),
            'local_address' => $addresses['address'],
            'remote_address' => $addresses['network'],
            'logs' => $logs
        ];
    }

    private function getInterfaceDataByName($name)
    {
        $query = (new Query('/interface/pppoe-server/print'))->where('name', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }

    private function getQueueDataByName($name)
    {
        $query = (new Query('/queue/simple/print'))->where('target', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }

    private function getTrafficDataByName($name)
    {
        $query = (new Query('/interface/getall'))->where('name', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }

    private function getAddressesDataByName($name)
    {
        $query = (new Query('/ip/address/print'))->where('interface', "<pppoe-$name>");
        $response = $this->router_instance->query($query)->read();
        return $response;
    }

    private function getLogDataByName($name, $mac)
    {
        $query = new Query('/log/print');
        $logs = $this->router_instance->query($query)->read();

        $result = array_filter(array_reverse($logs), function ($log) use ($name, $mac) {
            if (preg_match("<pppoe-$name>", $log['message'])) {
                return true;
            } else if (stripos($log['message'], $mac) !== false) {
                return true;
            }
        });
        return $result;
    }
}
