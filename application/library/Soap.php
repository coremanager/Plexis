<?php/* | --------------------------------------------------------------| | Plexis|| --------------------------------------------------------------|| Author: 		Steven Wilson| Copyright:	Copyright (c) 2011, Steven Wilson| License: 		GNU GPL v3|*/namespace Application\Library;class Soap{    protected $handle;    protected $Debug;    protected $debug = array();    protected $console_return;/*| ---------------------------------------------------------------| Method: connect()| ---------------------------------------------------------------|| This method is used to initiate the Telnet Connection|| @Param: (String) $server - The servers IP address| @Param: (Int) $port - The Telnet port| @Param: (String) $user - The login username| @Param: (String) $pass - The login password| @Return (Bool): True on success, FALSE otherwise|*/     public function connect($server, $port, $user, $pass)    {        // Determine our URI based on our core        $e = config('emulator');        switch($e)        {            case "mangos":                $uri = "urn:MaNGOS";                break;                            case "trinity":                $uri = "urn:TC";                break;                            default:                $uri = "";                break;        }                // Build our connection params        $params = array(            "location" => "http://".$server.":".$port."/",            "uri" => $uri,            "style" => SOAP_RPC,            "login" => $user,            "password" => $pass        );                // Turn off error reporting for this        $this->Debug = load_class('Debug');        $this->Debug->error_reporting( FALSE );        // Open the handle        try        {            $this->handle = new \SoapClient(NULL, $params);            $return = TRUE;        }        catch(\Exception $e)         {            $this->debug[] = 'Failed to initiate SOAP Connection: '.$e->getMessage();            $this->write_log();            $return = FALSE;        }                // Re-enable error reporting        $this->Debug->error_reporting( TRUE );                // Return our success or failure        return $return;    }	/*| ---------------------------------------------------------------| Method: disconnect()| ---------------------------------------------------------------|| This method is used to close the Telnet Connection|| @Param: (String) $exit - Use the "exit" command?| @Return (None)|*/     public function disconnect($exit = TRUE)     {        if($this->handle)         {            $this->handle = NULL;        }    }/*| ---------------------------------------------------------------| Method: send()| ---------------------------------------------------------------|| This method is used to send a command to the Telnet Connection|| @Param: (String) $command - The command string| @Return (Bool): TRUE on success, or FALSE|*/     public function send($command)     {        if($this->handle)         {            try            {                $this->console_return = $this->handle->executeCommand(new \SoapParam($command, "command"));                $return = TRUE;            }            catch(\Exception $e)            {                $return = $this->console_return = $e->getMessage();                $this->debug[] = 'Server Error Response: '.$return;                $this->write_log();                $return = FALSE;                            }            return $return;        }        return FALSE;    }/*| ---------------------------------------------------------------| Method: get_response()| ---------------------------------------------------------------|| This method is used to get the last response of the Connection|| @Return (String): Repsonse String|*/ 	    public function get_response()    {        // Return        return $this->console_return;    }/*| ---------------------------------------------------------------| Method: sleep()| ---------------------------------------------------------------|| Delays the next command to prevent errors|*/ 	    public function sleep()     {        usleep(300);        return;    }/*| ---------------------------------------------------------------| Method: write_log()| ---------------------------------------------------------------|| Logs any and all errors|*/         private function write_log()    {        $date = date('Y-m-d H:i:s');        $outmsg = array();        $outmsg[] = "******************************************************************";        $outmsg[] = "Ra Debugging Log for date: ". $date . PHP_EOL;                foreach($this->debug as $line)        {            $outmsg[] = $line;        }                $outmsg[] = "******************************************************************" . PHP_EOL;        $file = fopen( SYSTEM_PATH . DS . 'logs' . DS . 'ra_debug.log', 'a' );        foreach($outmsg as $msg)        {            fwrite($file, " ". $msg . PHP_EOL);        }        fclose($file);    }}// EOF