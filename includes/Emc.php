<?php


class Emc {
    private $host;
    private $port;
    private $protocol;
    private $username;
    private $password;
    private $connect;
    private $allowed_commands = [
        'name_show',
        'name_list',
    ];

    public function __construct()
    {
        $this->host = 'host';
        $this->port = 'port';
        $this->protocol = 'protocol';
        $this->username = 'username';
        $this->password = 'password';
        $this->connect = "$this->protocol://$this->username:$this->password@$this->host:$this->port'";
    }

    public function request($method, $params = [])
    {
        if (!in_array($method, $this->allowed_commands)){
            $command_error = true;
        }

        if(isset($command_error) && $command_error === true) {
            throw new Exception('Method not allowed', 405);
        }

        foreach ($params as $param) {
            $p[] = is_numeric($param) ? (int)$param : $param;
        }
        if(!isset($p)) {
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