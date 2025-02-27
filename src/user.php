<?php

require 'utils.php';
require 'dotenv.php';
require 'manufacturer.php';

use \RouterOS\Client;
use \RouterOS\Config;
use \RouterOS\Query;

class User
{
    private $router_instance;
    public $error_message;

    public function __construct()
    {
        try {
            $config = new Config([
                'host' => $_GET['gw'],
                'user' => $_SERVER["LOGIN"],
                'pass' => $_SERVER["PASSWORD"],
                'port' => 8728,
                'attempts' => 1,
                'socket_timeout' => 15,
                'timeout' => 2
            ]);
            $this->router_instance = new Client($config);
        } catch (Throwable $e) {
            $this->error_message = $e->getMessage();
        }
    }

    public function has_router_instance()
    {
        return is_object($this->router_instance);
    }

    public function getUserByName($name)
    {
        if (!$this->router_instance) {
            return null;
        }

        $if = $this->getInterfaceDataByName($name)[0] ?? [];
        $gateway = $this->getRouterIdentity()[0] ?? [];
        $manufacturer = (new Manufacturer())->getManufacturer($if['caller-id']);
        $queue = $this->getQueueDataByName($name)[0] ?? [];
        $traffic = $this->getTrafficDataByName($name)[0] ?? [];
        $logs = $this->getLogDataByName($name, $if['caller-id']) ?? [];

        $max_limit = $queue['max-limit'] ?? '0/0';
        [$max_download, $max_upload] = explode("/", $max_limit);

        return [
            'user' => $if['user'] ?? 'N/A',
            'caller_id' => $if['caller-id'] ?? 'N/A',
            'manufacturer' => $manufacturer ?? 'N/A',
            'interface' => $if['interface'] ?? 'N/A',
            'uptime' => $if['uptime'] ?? 'N/A',
            'gateway' => $gateway['name'] ?? 'N/A',
            'local_address' => $if['local-address'] ?? 'N/A',
            'remote_address' => $if['remote-address'] ?? 'N/A',
            'max_limit' => formatBytes($max_download) . "/" . formatBytes($max_upload),
            'last_link_up_time' => $traffic['last-link-up-time'] ?? 'N/A',
            'rx_byte' => formatBytes($traffic['rx-byte'] ?? 0),
            'tx_byte' => formatBytes($traffic['tx-byte'] ?? 0),
            'logs' => $logs
        ];
    }

    private function getInterfaceDataByName($name)
    {
        $query = (new Query('/interface/pppoe-server/monitor'))
            ->equal("numbers", "<pppoe-$name>")
            ->equal('once');
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

    private function getRouterIdentity()
    {
        $query = new Query('/system/identity/print');
        $result = $this->router_instance->query($query)->read();
        return $result;
    }
}
