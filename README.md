# A4lution

### 開發環境

- PHP 7.4
- Laravel 8
- MySQL 5.7

###  相關設定與安裝
- schedule 設定
    - ```crontab -e```
    - 新增此語法: ```* * * * * php /app/artisan schedule:run >> /dev/null 2>&1```
- supervisor 安裝
    - sudo apt-get install supervisor
    - 在 /etc/supervisor/conf.d 目錄下建立 horizon.conf，內容如下
        ```
        [program:horizon] process_name=%(program_name)s
        command=php /app/artisan horizon
        autostart=true
        autorestart=true
        redirect_stderr=true
        stdout_logfile=/logs/horizon.log
        ```
    - 啟動 supervisor: ```service supervisor start```
