<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>nessproxy</title>
</head>
<body>
    <?php
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Credentials: true');
    
    class ness {
        private $host;
        private $port;
        private $protocol;
        private $username;
        private $password;
        private $connect;
        private $allowed_commands = [
            'AddPrivateKey        //  Add a private key to wallet',
            'AddressBalance        // Check the balance of specific addresses',
            'addressGen           //  Generate skycoin or bitcoin addresses types',
            'addressOutputs         Display outputs of specific addresses',
            'AddressTransactions   // Show detail for transaction associated with one or more specified addresses',
            'Addresscount        //  Get the count of addresses with unspent outputs (coins)',
            'Blocks               //  Lists the content of a single block or a range of blocks',
            'broadcastTransaction   Broadcast a raw transaction to the network',             
            'CreateRawTransaction  // Create a raw transaction that can be broadcast to the network later',
            'CreateRawTransactionV2 // Create a raw transaction that can be broadcast to the network later',
            'DecodeRawTransaction  // Decode raw transaction',                 
            'EncodeJsonTransaction // Encode JSON transaction',        
            'Help                 //  Help about any command',
            'LastBlocks          //   Displays the content of the most recently N generated blocks',
            'ListAddresses        //  Lists all addresses in a given wallet',
            'ListWallets           // Lists all wallets stored in the wallet directory',
            'PendingTransactions   // Get all unconfirmed transactions',
            'Richlist              // Get skycoin richlist',
            'Send                // Send skycoin from a wallet or an address to a recipient address',
            'ShowConfig           //  Show cli configuration',
            'ShowSeed             //  Show wallet seed and seed passphrase',
            'SignTransaction       // Sign an unsigned transaction with specific wallet',
            'Status               //  Check the status of current Privateness node',
            'Transaction           // Show detail info of specific transaction',
            'verifyAddress         // Verify a privateness address',
            'VerifyTransaction    //  Verify if the specific transaction is spendable',
            'Version              //  List the current version of Privateness components',
            'WalletAddAddresses    // Generate additional addresses for a deterministic, bip44 or xpub wallet',
            'WalletBalance         // Check the balance of a wallet',
            'WalletCreate         //  Create a new wallet',
            'WalletCreateTemp      // Create a new temporary wallet',
            'WalletHistory        //  Display the transaction history of specific wallet. Requires skycoin node rpc.',
            'WalletKeyExport       // Export a specific key from an HD wallet',
            'WalletOutputs         // Display outputs of specific wallet',
           
        ];
    
        /**
         * Ness constructor.
         */
        public function __construct()
        {
            $this->host = 'host';
            $this->port = 'port';
            $this->protocol = 'protocol';
            $this->username = 'username';
            $this->password = 'password';
            $this->connect = "$this->protocol://$this->username:$this->password@$this->host:$this->port'";
        }
    
        /**
         * @param $method
         * @param array $params
         * @return bool|mixed|string
         * @throws Exception
         */
        public function request($method, $params = [])
        {
            if (!in_array($method, $this->allowed_commands)) {
                $command_error = true;
            }
    
            if (isset($command_error) && $command_error === true) {
                throw new Exception('Method not allowed', 405);
            }
    
            foreach ($params as $param) {
                $p[] = is_numeric($param) ? (int)$param : $param;
            }
            
            if (!isset($p)) {
                $p = $params;
            }
    
            $request = json_encode(['method' => $method, 'params' => $p], JSON_UNESCAPED_UNICODE);
    
            $opts = [
                'http' => [
                    'method' => 'POST',
                    'header' => join(
                        "\r\n",
                        [
                            'Content-Type: application/json; charset=utf-8',
                            'Accept-Charset: utf-8;q=0.7,*;q=0.7',
                        ]
                    ),
                    'content' => $request,
                    'ignore_errors' => true,
                    'timeout' => 10,
                ],
                'ssl' => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
            ];
            $response = @file_get_contents($this->connect, false, stream_context_create($opts));
            $response = json_decode($response, true);
    
            if($response == null) {
                throw new Exception('Bad Gateway', 502);
            }
    
            return $response;
        }
    }
    
    
    try {
        if(file_get_contents('php://input')) {
            $post = json_decode(file_get_contents('php://input'), true);
    
            $params = [];
            if($post['params']) {
                $params = $post['params'];
            }
    
            $ness = new ness();
    
            echo json_encode($ness->request($post['method'], $params), JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        echo json_encode([
            'error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]
        ], JSON_UNESCAPED_UNICODE);
    }    
</body>
</html>
