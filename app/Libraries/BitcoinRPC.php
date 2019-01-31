<?php
/**
 * Created by PhpStorm.
 * User: elyci
 * Date: 1/30/2019
 * Time: 10:05 AM
 */

namespace App\Libraries;


class BitcoinRPC
{
    /**
     * Placeholder Variable for CURL.
     */
    private $curlHandle;

    /**
     * RPC Template that requests will be made out of
     */
    const RPC_TEMPLATE = [
        "jsonrpc" => "1.0"
    ];

    /**
     * The number of confirmations required for the transaction to be considered valid.
     */
    const MINIMUM_TRANSACTION_CONFIRMATIONS = 6;

    /**
     * BitcoinRPC constructor.
     *
     * - Create CURL instance.
     * - Handle credentials.
     * - Assign default parameters.
     */
    public function __construct()
    {
        // Initialize curl and specify credentials.
        $this->curlHandle = curl_init();
        curl_setopt($this->curlHandle, CURLOPT_URL, sprintf("http://%s:%s@localhost:8332", env('RPC_USERNAME', 'bitcoin'), env('RPC_PASSWORD')));
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Send the command to the RPC server.
     *
     * @param string $method
     * @param array $params
     * @return string
     */
    private function sendRPC(string $method, array $params): string
    {
        // Generate an array from the template.
        $data = self::RPC_TEMPLATE;

        // Assign a random ID
        $data["id"] = rand(0, PHP_INT_MAX);
        // Assign the method
        $data["method"] = $method;
        // Assign the parameters.
        $data["params"] = $params;

        // Assign the dataset to curl after JSON encoding.
        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the request and return it to the calling function.
        return curl_exec($this->curlHandle);
    }

    /**
     * Generate a new address for the account.
     *
     * @param string $account
     * @return mixed
     * @throws \Exception
     */
    public function getNewAddress($account = "tipper"): string
    {
        $decoded = json_decode($this->sendRPC("getnewaddress", [$account]), true);
        if ($decoded["error"] != null) {
            return $decoded["result"];
        } else {
            throw new \Exception("Failed to create a a new address for " . $account);
        }
    }

    /**
     * Get the balance for the account
     *
     * @param string $account
     * @return float
     * @throws \Exception
     */
    public function getBalance(string $account = "tipper"): float
    {
        $decoded = json_decode($this->sendRPC("getbalance", [$account, self::MINIMUM_TRANSACTION_CONFIRMATIONS]), true);
        if ($decoded["error"] != null) {
            return $decoded["result"];
        } else {
            throw new \Exception("Invalid response while getting balance for " . $account);
        }
    }

    /**
     * @param string $from_account
     * @param array $addresses
     * @return mixed
     * @throws \Exception
     */
    public function sendMany(string $from_account = "tipper", array $addresses)
    {
        $decoded = json_decode($this->sendRPC("sendmany", [$from_account, $addresses, self::MINIMUM_TRANSACTION_CONFIRMATIONS]), true);
        if ($decoded["error"] != null) {
            return $decoded["result"];
        } else {
            throw new \Exception("Failed to send to multiple bitcoin addresses.");
        }
    }


}