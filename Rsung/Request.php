<?php

namespace Rsung;

class Request
{
    
    private $debug   = false;
    private $product = [
        'account' => 'http://account.new-dhb.com',
    ];
    
    
    //remote:远程上报;files:本机存储
    
    private $logMode = 'files';
    
    private $method = '';

//    private $fileBaseUrl = '/tmp/debug/';
    
    private $fileBaseUrl = './debug/';
    
    //curl对象
    private $curl = null;
    
    //选项
    private $options = [
        //超时
        CURLOPT_TIMEOUT        => 5,
        //成功连接服务器前等待多久
        CURLOPT_CONNECTTIMEOUT => 5,
        //默认请求方法
        CURLOPT_CUSTOMREQUEST  => 'POST',
        
        CURLOPT_RETURNTRANSFER => true,
        //代理
        //CURLOPT_PROXY=>''
        //        CURLOPT_HEADER         => true,
    
    
    ];
    
    private $data  = '';
    private $info  = [];
    private $error = '';
    
    public function send($product, $method = '', $args = [])
    {
        $host = $this->product[$product];
        $this->url($this->conversionUrl($host, $method));
        $this->method = $method;
        $this->data   = $args;
        
        return $this->exec(true);
    }
    
    private function log($args = [])
    {
        switch ($this->logMode) {
            case 'remote':
                $this->logRemote($args);
                break;
            case 'files':
                $this->logFile($args);
                break;
            default:
                $this->logFile($args);
        }
    }
    
    private function logRemote()
    {
    
    }
    
    private function conversionUrl($host, $method)
    {
        $info = explode('.', $method);

        return $host . '/' . $info[1] . '/' . $info[2];
    }
    
    private function logFile($args)
    {
        
        $file = $this->dir() . $this->fileName();
        $str = "\n================[time " . date('Y-m-d H:i:s', time()) . "]================\n";
        file_put_contents($file, $str.var_export($args, true), FILE_APPEND | LOCK_EX);
        
    }
    
    private function fileName()
    {
        return date('Y-m-d') . '.log';
    }
    
    private function dir()
    {
        $folder = $this->fileBaseUrl . $this->method . '/' . date('Y-m', time()) . '/';
        if (!is_dir($folder) && !mkdir($folder, 0777, true) && !is_dir($folder)) {
            return false;
        }
        
        return $folder;
    }
    
    public function __construct($options = [])
    {
        
        $this->curl    = curl_init();
        $this->options = $options + $this->options;
        
    }
    
    
    public function error()
    {
        return $this->error;
    }
    
    public function info()
    {
        return $this->info;
    }
    
    /**
     * 设置参数
     * @param $options
     * @param string $value
     * @return $this
     */
    public function set($name, $value = '')
    {
        
        if (is_array($name)) {
            
            $this->options = $name + $this->options;
        } else if (isset($this->$name)) {
            
            $this->$name = $value;
        } else {
            $this->options[$name] = $value;
        }
        
        return $this;
    }
    
    
    public function __destruct()
    {
        curl_close($this->curl);
    }
    
    public function debug($value = true)
    {
        
        $this->debug = $value;
        
        return $this;
    }
    
    public function logMode($value = 'remote')
    {
        
        $this->debug = $value;
        
        return $this;
    }
    
    public function method($value)
    {
        
        $this->options[CURLOPT_CUSTOMREQUEST] = strtoupper($value);
        
        return $this;
    }
    
    
    private function url($value)
    {
        
        $this->options[CURLOPT_URL] = $value;
        
        return $this;
    }
    
    
    private function ssl($host = 0, $peer = false, $version = 0)
    {
        
        $this->options[CURLOPT_SSL_VERIFYHOST] = $host; //0不检测，1检查服务器SSL证书中是否存在一个公用名（即将删除）， 2检查公用名且是否与提供主机名相匹配
        $this->options[CURLOPT_SSL_VERIFYPEER] = $peer;//禁止验证对等证书
        $this->options[CURLOPT_SSLVERSION]     = $version;
        
        return $this;
    }
    
    private function exec($run = false)
    {
        $data = is_array($this->data) ? http_build_query($this->data) : $this->data;
        if ($this->data !== null) {
            
            if ($this->options[CURLOPT_CUSTOMREQUEST] == 'GET') {
                
                $link = strpos($this->options[CURLOPT_URL], '?') === false ? '?' : '&';
                
                if (is_array($this->data) && is_numeric(key($this->data))) {
                    $link = strpos($this->options[CURLOPT_URL], '/') === false ? '/' : '';
                    $data = implode('/', $this->data);
                }
                
                $this->options[CURLOPT_URL] .= $link . $data;
            } else {
                $this->options[CURLOPT_POSTFIELDS] = $data;
            }
        }
        
        $scheme = parse_url($this->options[CURLOPT_URL])['scheme'];
        if ($scheme == 'https' && !isset($this->options[CURLOPT_SSL_VERIFYHOST])) $this->ssl();
        
        ksort($this->options);
        
        if ($run === false) return $this->options;
        
        curl_setopt_array($this->curl, $this->options);
        
        $result = curl_exec($this->curl);
        
        if ($this->debug) {
            
            if ($this->error = curl_errno($this->curl)) $this->error =
                curl_error($this->curl) . '(' . $this->error . ')';
            if (empty($this->error)) $this->error = '';
            
            $this->info = curl_getinfo($this->curl);
            $logData    = [
                'is_error' => $this->error ?: '请求成功',
                'info'     => $this->info,
                'options'  => $this->options,
            ];
            $this->log($logData);
        }
        
        
        curl_reset($this->curl);
        
        return $result;
        
    }
    
    
}