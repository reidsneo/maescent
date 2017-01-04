<?php 
/**
* Infor Soap Class
* 
*/
class CentreonSoap
{
	static private $client;
	static private $tokendat;

    static public function getClient() {
        if (empty(self::$client)) {
            //echo "<br>initializing...";
            self::initClient();
        }
        return self::$client;
    }

    static private function initClient() {
      //  $settingsOld = Settings::GetOld();
        self::$client = new SoapClient("http://erpdbserver.cloudapp.net/IDORequestService/IDOWebService.asmx?wsdl",array(
			'uri'=>'http://schemas.xmlsoap.org/soap/envelope/',
			'style'=>SOAP_RPC,
			'use'=>SOAP_ENCODED,
			'soap_version'=>SOAP_1_1,
			'cache_wsdl'=>WSDL_CACHE_NONE,
			'connection_timeout'=>15,
			'trace'=>true,
			'encoding'=>'UTF-8',
			'exceptions'=>true,
		));
        $tokendat=self::$client->CreateSessionToken(array('strUserId'=>'mums','strPswd'=>'Mums12345','strConfig'=>'MTNL'));
        self::$tokendat=$tokendat->CreateSessionTokenResult;
    }
    static public function token(){
    	return self::$tokendat;
    }

    static public function parsexml($dat){
    	$xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $dat);
		$xml = simplexml_load_string($xml);
		$json = json_encode($xml);
		$responseArray = json_decode($json,true);
		return $responseArray;
    }

/*	public $soaptoken = "";

	function __construct()
	{
	}

	public function soapconnect($svr="http://erpdbserver.cloudapp.net/IDORequestService/IDOWebService.asmx?wsdl") {
		$inforcfg = array(
			'uri'=>'http://schemas.xmlsoap.org/soap/envelope/',
			'style'=>SOAP_RPC,
			'use'=>SOAP_ENCODED,
			'soap_version'=>SOAP_1_1,
			'cache_wsdl'=>WSDL_CACHE_NONE,
			'connection_timeout'=>15,
			'trace'=>true,
			'encoding'=>'UTF-8',
			'exceptions'=>true,
		);
       $soap = new SoapClient($svr, $inforcfg);
       $loginsoap = array('strUserId'=>'sa','strPswd'=>'','strConfig'=>'Demo');
       $tokendat=$soap->CreateSessionToken($loginsoap);
       $this->soaptoken=$tokendat->CreateSessionTokenResult;
    }
    public function soapparsexml($resultdata){
	    $xml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $resultdata);
		$xml = simplexml_load_string($xml);
		$json = json_encode($xml);
		$responseArray = json_decode($json,true);
    }
    public function getToken(){ 
        return $this->$soaptoken; 
    }*/
}
?>