# 第三方对接SDK for PHP
## 概述
提供独立的接口调用工具类给到业务方，业务方只需传入接口名称和参数即可完成接口调用,支持debug模式。
## 运行环境
* PHP 5.6+
* cURL extension

## 安装方法
1. composer require rsung/request

## 快速使用
### 常用类

| 类名    | 解释 |
| ------- | ---- |
| Request | 请求类，用户用过Request的实例调用接口 |

### Request初始化
SDK的请求操作通过Request类完成的，下面代码创建一个Request对象:

```PHP

<?PHP
$request = new Request();

```

### 请求操作
下面代码将请求 user服务的**获取账户详细信息**接口，默认是POST请求

```PHP
<?PHP

$request = new Request();
$product = "<您请求的服务名字>";
$method  = "<您请求的方法>";
$args    = [        //非必填项
    "请求的参数",
];
$res = $request->send($product, $method, $args);
```

### 请求方式设置
下面代码将设置**get请求方式**

```PHP

<?PHP

$request = new Request();
$product = "<您请求的服务名字>";
$method  = "<您请求的方法>";
$args    = [        //非必填项
    "请求的参数",
];
$request->method('get');//忽略大小写
$res = $request->send($product, $method, $args);   

```

<div class="page-break"></div>

### debug模式设置
debug模式分为两种
* 本机存储  **files**
* 远程上报  **remote**(未实现)

下面代码将设置debug模式

```PHP
<?PHP

$request = new Request();
$product = "<您请求的服务名字>";
$method  = "<您请求的方法>";
$args    = [        //非必填项
    "请求的参数",
];
$request->logMode('files');//设置debug模式 files 是本机存储  remote 是远程上报 //默认是files
$request->debug();//设置debug模式，默认参数默认true，false则是不设置
$res = $request->send($product, $method, $args);

```
debug模式日志记录详解
[CURLcode](https://curl.haxx.se/libcurl/c/libcurl-errors.html)
```java
================[time 2018-10-26 16:41:52]================
array (
  'is_error' => '请求成功',  //是返回当前会话最后一次错误的字符串+当前请求的错误码, 0代表没有错误，是一个Ok正常的请求。非0代码请求出现了错误。这里 汉字请求成功则代表没有错误
  'info' =>  //当前请求的相关信息
    array (
      'url'                     => 'http://account.newdhb.com/account/getAccountPassportDetail', //资源网络地址
      'content_type'            => 'application/json;charset=utf-8',  //内容编码
      'http_code'               => 200,                               //http状态码
      'header_size'             => 199,                               //header 的大小
      'request_size'            => 180,                               //请求的大小
      'filetime'                => -1,                                //文件的创建时间
      'ssl_verify_result'       => 0,                                 //ssl验证结果
      'redirect_count'          => 0,                                 //跳转次数
      'total_time'              => 1.631235,                          //耗时
      'namelookup_time'         => 1.5170650000000001,                //DNS查询时间
      'connect_time'            => 1.559134,                          //连接时间
      'pretransfer_time'        => 1.559277,                          //准备传输耗时
      'size_upload'             => 21,                                //上传数据大小
      'size_download'           => 492,                               //下载数据大小
      'speed_download'          => 301,                               //下载速度
      'speed_upload'            => 12,                                //上传速度
      'download_content_length' => -1,                                //下载内容长度
      'upload_content_length'   => 21,                                //上传内容长度
      'starttransfer_time'      => 0,                                 //开始传输耗时
      'redirect_time'           => 0,                                 //重定向耗时
      'redirect_url'            => '',
      'primary_ip'              => '101.37.254.95',
      'certinfo'                =>                                    //认证信息
         array (
         ),
      'primary_port'            => 80,
      'local_ip'                => '172.18.0.4',
      'local_port'              => 40258,
    ),
  'options' =>   //curl配置
    array (
       13 => 5,
       78 => 5,
       10002 =>  'http://account.newdhb.com/account/getAccountPassportDetail',
       10015 => '',
       10036 => 'POST',
       19913 => 0,
    ),
)
```

<div class="page-break"></div>

### 设置curl配置
下面代码将设置curl配置
```PHP
<?PHP

$request = new Request();
$product = "<您请求的服务名字>";
$method  = "<您请求的方法>";
$args    = [        //非必填项
    "请求的参数",
];
$name = "<配置项>" //常量配置项比如 CURLOPT_PROXY
$value = "<值>"
$request->set($name,$value);
$res = $request->send($product, $method, $args);
```
