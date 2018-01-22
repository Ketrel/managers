<?php

namespace ketrel\managers;

class InvalidDetails extends \Exception {}
class NoCookie extends \Exception {}

class cookie
{
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const ERR_NAME = 'Cookie Name Must Be Set';
    const ERR_DOMAIN = 'Cookie Domain Must Be Set';
    const ERR_NOCOOKIE = 'Cookie Does Not Exist';

    public static function hash($data)
    {
        return hash("sha256",$data);
    }

    private $cmName=null;
    private $cmDomain=null;
    private $cmPath="/";
    private $cmHours=null;
    private $cmData=null;
    private $cmExists=false;

    public function __construct($name=null,$domain=null)
    {
        if(is_null($name)){ throw new InvalidDetails(self::ERR_NAME); }
        if(is_null($domain)){ throw new InvalidDetails(self::ERR_DOMAIN); }

        $this->cmName = $name;
        $this->cmDomain = $domain;
        if(isset($_COOKIE[$this->cmName])){
            $this->cmExists = true;
            $this->cmData = $_COOKIE[$this->cmName];
        }
    }

    public function checkSet(){
        return $this->cmExists;
    }

    public function readCookie(){
        if ($this->cmExists){
            return $this->cmData;
        }else{
            return false;
        }
    }

    public function debug()
    {
        echo "cmName: ".$this->cmName."<br />";
        echo "cmDomain: ".$this->cmDomain."<br />";
        echo "cmHours: ".$this->cmHours."<br />";
        echo "cmData: ".$this->cmData."<br />";
    }

    public function setCookie($data,$hours=24,$hash=false)
    {
        $this->cmData = ($hash) ? self::hash($data) : $data;
        $this->cmHours = time()+(self::HOURS*$hours);

        if(is_null($this->cmName)) { throw new InvalidDetails(self::ERR_NAME); }
        if(is_null($this->cmDomain)) { throw new InvalidDetails(self::ERR_DOMAIN); }

        setcookie($this->cmName,$this->cmData,$this->cmHours,$this->cmPath,$this->cmDomain);

        return true;
    }

    public function unsetCookie()
    {
        if(is_null($this->cmName) || is_null($this->cmDomain))
        {
            return false;
        }else{
            setcookie($this->cmName,'',time()-3600,$this->cmPath,$this->cmDomain);
            return true;
        }
    }

    public function verifyData($data,$hashed=false)
    {
        if(!$this->cmExists){
            throw new NoCookie(self::ERR_NOCOOKIE.': Cannot Verify Data');
        }
        if($hashed){
            return ($this->cmData == self::hash($data));
        }else{
            return ($this->cmData == $data);
        }
    }

    public function extend($reSetHours=24)
    {
        if(!$this->cmExists){
            throw new NoCookie(self::ERR_NOCOOKIE.': Cannot Extend Expiration');
        }
        setCookie($this->cmName,$this->cmData,time()+(self::hours*$reSetHours),$this->cmPath,$this->cmDomain);
        return true;
    }


    //Function below this point are deprecated, and either will be re-written or removed

    /*
    public function select($name,$domain)
    {
        $this->cmName = $name;
        $this->cmDomain = $domain;
        return true;
    }
    */

    /*
    public function read()
    {
        if(isset($_COOKIE[$this->cmName]))
        {
            $this->cmData = $_COOKIE[$this->cmName];
            return $this->cmData;
        }else{
            return false;
        }
    }
    */
}

?>
