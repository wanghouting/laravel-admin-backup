<?php


namespace LTBackup\Extension\Support;


use FtpClient\FtpClient;
use LTBackup\Extension\Facades\SettingFacade;

/**
 * Class FtpManagerSupport
 * @author wanghouting
 * @package LTBackup\Extension\Support
 */
class FtpManagerSupport
{
    public static $instance = null;

    /**
     * @var FtpClient
     */
    protected $ftpClient;

    protected $host;

    protected $port = 21;

    protected $username;

    protected $password;


    protected $path;

    private  function __construct(){


    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance(){
        if(!self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect(){
        $setting = $this->getFtpSetttingInfo();
        $this->host = $setting['ltbackup_ftp_address'];
        $this->port = $setting['ltbackup_ftp_port'];
        $this->username = $setting['ltbackup_ftp_username'];
        $this->password = $setting['ltbackup_ftp_password'];
        $this->path = $setting['ltbackup_ftp_upload_path'];
        $this->ftpClient = new FtpClient();
        $this->ftpClient->connect($this->host,true,$this->port);
        $this->ftpClient->login($this->username,$this->password);
    }

    /**
     * ftp是否开启
     * @return bool
     */
    public function isFtpOpen(){
       return  SettingFacade::get('ltbackup_to_ftp') == 'on';
    }

    /**
     * 上传文件
     * @param $file
     * @return string
     * @throws \FtpClient\FtpException
     */

    public function uploadFile($file){
        if(!$this->ftpClient) $this->connect();
        $path = $this->path  .'/'.date('Ymd');
        !$this->ftpClient->isDir($path) && $this->ftpClient->mkdir($path,true);
        $this->ftpClient->chdir($path);
        $this->ftpClient->putFromPath($file);
        $filenameArr = explode('/',$file);
        $filename = $filenameArr[count($filenameArr) -1];
        return $path .'/'.$filename;
    }


    public function downloadFile($file){
        if(!$this->ftpClient) $this->connect();
        return $this->ftpClient->getContent($file);
    }

    /**
     * 测试连接
     * @param $host
     * @param $port
     * @param $username
     * @param $password
     * @return resource
     * @throws \FtpClient\FtpException
     */
    public function testConenct($host,$port,$username,$password)
    {
        $ftpclient  = new FtpClient();
        $ftpclient->connect($host,true,$port);
        $ftpclient->login($username,$password);
        $res =  $ftpclient->getConnection();
        $ftpclient->close();
        return $res;
    }
    
    /**
     * 关闭连接
     */
    public  function close(){
        self::$instance && $this->ftpClient  && $this->ftpClient->close();
        self::$instance = null;
    }

    /**
     * 获取ftp配置信息
     * @return array
     */
    private function getFtpSetttingInfo(){
        $ftpSettings = SettingFacade::getWithType(2);
        $ftpInfo = [];
        foreach ($ftpSettings as $setting){
            if(empty(trim($setting->plainValue))){
                throw new \Exception("warning: ftp服务端为完成配置,无法使用!");
            }
            $ftpInfo[$setting->name] =  $setting->plainValue;
        }
        return $ftpInfo;
    }


}