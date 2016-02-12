<?php
class Svn_Ssh{ 
    // SSH Host 
    private $ssh_host = '10.100.11.121'; 
    // SSH Port 
    private $ssh_port = 22; 
    // SSH Username 
    private $ssh_auth_user = 'ashwinm'; 
    // SSH Public Key File 
    private $ssh_auth_pass = 'cvlx*123'; 
    // SSH Connection 
    private $connection; 
    
    public function __construct(){
        $this->connect();
    }
    public function connect() { 
        if (!($this->connection = ssh2_connect($this->ssh_host, $this->ssh_port))) { 
            throw new Exception('Cannot connect to server'); 
        } 
        
        if (!ssh2_auth_password($this->connection, $this->ssh_auth_user, $this->ssh_auth_pass) ) {
            throw new Exception('Autentication rejected by server'); 
        }
    } 
    
    public function execute( $cmd = '' ) { 

        if (!($stream = ssh2_exec($this->connection, $cmd))) { 
            throw new Exception('SSH command failed'); 
        } 
        stream_set_blocking($stream, true); 
        $data = ""; 
        while ($buf = fread($stream, 4096)) { 
            $data .= $buf; 
        }
        fclose($stream);  
        return $data; 
    } 
    public function disconnect() { 
        $this->execute('echo "EXITING" && exit;'); 
        $this->connection = null; 
    } 
}