<?php

namespace ketrel\manager;

class cookie
{

    private $cmName=null;
    private $cmDomain=null;
    private $cmPath="/";
    private $cmHours=null;
    private $cmData=null;


    public function __construct($name=null,$domain=null)
    {
        $this->cmName = $name;
        $this->cmDomain = $domain;
    }

    public function select($name,$domain)
    {
        $this->cmName = $name;
        $this->cmDomain = $domain;
        return true;
    }

    public function debug()
    {
        echo "cmName: ".$this->cmName."<br />";
        echo "cmDomain: ".$this->cmDomain."<br />";
        echo "cmHours: ".$this->cmHours."<br />";
        echo "cmData: ".$this->cmData."<br />";
    }

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

    public function setCookie($data,$hours=24,$hash=false)
    {
        if($hash)
        {
            $data = $this->hash($data);
        }
        $this->cmData = $data;

        $this->cmHours = time()+(60*60*$hours);
        //60 Seconds (Minute) * 60 Minutes (Hour) * 24 Hours (Day) * -x- Day(s)

        if(is_null($this->cmName) || is_null($this->cmDomain))
        {
            echo "error, not setting";
            return false;
        }else{
            setcookie($this->cmName,$this->cmData,$this->cmHours,$this->cmPath,$this->cmDomain);
            return true;
        }
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

    public function verifyData($data,$hashed=false,$reSetHours=0)
    {
        if(($data == $this->cmData) && $hashed != true)
        {
            if(is_numeric($reSetHours) && $reSetHours > 0)
            {
                $this->refresh($reSetHours);
            }
            return true;
        }
        elseif(($this->hash($data) == $this->cmData) && $hashed == true)
        {
            if(is_numeric($reSetHours) && $reSetHours > 0)
            {
                $this->refresh($reSetHours);
            }
            return true;
        }
        else
        {
            return false;
        }
    }

    public function refresh($reSetHours=24)
    {
        if($this->read())
        {
            setCookie($this->cmName,$this->cmData,time()+(60*60*$reSetHours),$this->cmPath,$this->cmDomain);
        }
    }

    public function hash($data)
    {
        return hash("sha256",$data);
    }
}

?>
