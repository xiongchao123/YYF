Yaf_web (Yaf for web)
============================

基于PHP yaf扩展和参考YYF框架,构建的安全高性能RESTful后端框架。

YYF致力于提高生产环境下的运行性能和开发环境下的开发效率。
[核心库](library/)高效封装常用库和操作,兼容php5.3及以上,并对php7做最大优化。


## 链接

* [**YYF-BOOK** 开发文档 (更新中)](https://yyf.newfuture.cc/)
* [**最新版演示**dev分支(开发)](https://yyf.yunyin.org/)
* [**稳定版演示**master分支(生产)](https://yyf-master.yunyin.org/)
* [YYFJS前端JavaScript接口库](https://github.com/YunYinORG/YYFJS)
* [YYF-Debugger浏览器输出查看](http://debugger.newfuture.cc/)

## 安装使用

可以一键配置本地开发环境和部署服务器生成环境。
详细安装方法查看文档 [http://yyf.newfuture.cc/setup/](http://yyf.newfuture.cc/setup/)

快速安装:

1. 下载解压，或者clone仓库 `git clone https://github.com/xiongchao123/yaf_web.git` ;
2. 运行 `./init.cmd` (windows下双击即可) ;
3. 使用YYF虚拟机开发环境或者配置服务器环境 ;


## 目录结构
>
```
├── app                   		应用目录【添加代码目录】         
│   ├── Console			  		控制台操作实现						
│   ├── controllers      		控制器目录【添加代码的主战场】
│   ├── email             		邮件模板目录
│   ├── lang              		语言包
│   ├── models           		数据模型目录
│   ├── modules           		新建模块目录
│   ├── plugins           		插件
│   └── views             		视图
├── composer.json         
├── composer.lock
├── conf                  		配置目录
│   ├── app.ini                 全局配置
│   ├── AuthFilter.php          用户认证过滤
│   ├── CsrfFilter.php          CSRF过滤
│   ├── router.ini              路由配置
│   ├── secret.common.ini       dev模式下配置
│   └── secret.product.ini      product模式下配置
│   └── queue.php               队列配置
├── init.cmd                     开发环境初始化通用脚本
├── library                      核心库目录 
│   ├── Aes.php
│   ├── Auth.php
│   ├── Bootstrap
│   ├── Cache.php
│   ├── Cipher.php
│   ├── Config.php
│   ├── Cookie.php
│   ├── Db.php
│   ├── Debug
│   ├── Debug.php
│   ├── Exception
│   ├── Functions
│   ├── Input.php
│   ├── Interfaces
│   ├── Jobs
│   ├── Kv.php
│   ├── Logger.php
│   ├── Mail.php
│   ├── Model.php
│   ├── Orm.php
│   ├── Parse
│   ├── Psr
│   ├── Random.php
│   ├── README.md
│   ├── Resque
│   ├── Rest.php
│   ├── Rsa.php
│   ├── Safe.php
│   ├── Service
│   ├── Session.php
│   ├── Storage
│   ├── Test
│   ├── Util
│   ├── Validate.php
│   └── Wechat.php
├── LICENSE
├── public                       公共目录【前端资源目录，生产环境根目录】
│   ├── css
│   ├── favicon.ico
│   ├── index.php
│   ├── js
│   └── robots.txt
├── README.MD
├── runtime                      默认缓存日志临时文件夹【保证程序具有可读写权限】  
│   ├── kv
│   ├── log
│   └── phpunit
├── server.cmd
├── tests                        单元测试目录  
│   ├── library
│   ├── phpunit.xml
│   └── test.php
├── vendor                       第三方包
│   ├── autoload.php
│   ├── composer
│   ├── psr
│   └── symfony
└── yaf                          控制台操作脚本

```
>

### 生成控制器
```bash
    # php yaf help make:controller 
    php yaf make:controller controller name 

```

### 操作队列

#### 驱动
* redis (目前只支持redis驱动,后续可增加)
#### 消息入队列
```php

<?php 

//test resque
    $argus = array(
        'time' => time(),
        'array' => array(
            'test' => 'test',
        ),
    );
    
    $jobId =\Resque\Queue::push('Jobs\testJob',$argus,true);
    echo "Queued job ".$jobId."\n\n";
    
    $jobId =\Resque\Queue::push('Jobs\testJob',$argus,true,'queue_name');
    echo "Queued job ".$jobId."\n\n";
    
    /*
     * 手动设置redis连接配置
     * 需在push之前修改配置
     */
     \Resque\Queue::setConfig($host, $port, $password = null, $database = 0);
     \Resque\Queue::push(...);
    
```
#### 消费队列
```bash
    php yaf help  #可查看帮助命令
    php yaf list  #查看命令列表
    php yaf queue:work  #默认消费所有队列,间隔轮询默认5秒
    php yaf help queue:work #查看参数提示 
    
    queue:work --queue=queue_name --interval=time --blocking=0/1 --process=num --loglevel=0/1 --pidfile=filepath
    
    queue       =>  队列名 默认为* 检测所有队列
    interval    =>  间隔轮询时间
    blocking    =>  是否堵塞  0不堵塞,1堵塞
    process     =>  进程数    windows下恒为1
    loglevel    =>  日志详细  默认打开
    pidfile     =>  进程pid文本存储 默认不存储

```

## 许可协议(LICENSE)

[Apache2.0 开源协议](LICENSE) 授权
