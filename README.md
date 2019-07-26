laravel 备份扩展
 ---

### 安装
    
 1. composer:
    
    ```
    composer require wanghouting/lt-dev-tools
    ```

 2. 初始化
     - 如果之前没有安装laravel-admin,需要先执行
    ```
    php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"
    
    php artisan admin:install
    ``` 

 	```
 	php artisan ltupdate:install 
 	```

 3. 添加定时任务

 	修改app/Console/kernel.php,在schedule里面增加 
 	```
 	$schedule->command('ltbackup:backup --all')->everyMinute();
    $schedule->command('ltbackup:clear')->dailyAt('1:00');
 	```

 	最后在cron中加入 ，注意修改“/path-to-your-project/artisan” 为你的项目下artisan路径
 	```
 	* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1 
 	```
 