# File Uploader

A simple file uploader application that allows authenticated users to upload, list, and delete files. 
The application uses PHP and a Python authentication service running on Apache2.

## Prerequisites

- Apache2, configured, up and running
- PHP 8.1 or higher
- Required PHP extensions: `php-json`, `php-curl`

Hint:
```
sudo apt update
sudo apt install apache2
sudo apt install php libapache2-mod-php
```

## Installation

For simplicity, I'll use my current Ubuntu instance user name, you should replace by yours.

  ### Clone 
   ```
   git clone https://github.com/yourusername/uploader.git
   cd uploader
   ```
   ### or download this repository
   ```
   wget https://github.com/sensboston/uploader/archive/refs/heads/master.zip
   unzip master.zip -d uploader
   mv uploader/uploader-master/* uploader/
   rm -r uploader/uploader-master
   rm master.zip
   ```

  ### Configure PHP

  Ensure the following PHP settings are in your **/etc/php/8.1/apache2/php.ini**:
  ```
  log_errors = On
  error_log = /var/log/php_errors.log
  ```
  
  Also check for max upload file/post size limits in **/etc/php/8.1/apache2/php.ini** (adjust to your needs, like 10G):
  ```
  upload_max_filesize = 10M
  post_max_size = 10M
  ```

  ### Create the upload directory and set the necessary permissions:

  ```
  sudo mkdir -p /var/www/html/upload
  sudo chown -R www-data:www-data /var/www/html/upload
  sudo chmod -R 755 /var/www/html/upload
  ```

  ### Do not forget to add proper permissions to www-data (used by apache & php)
  ```
  sudo chown -R www-data:www-data /var/www/html/upload
  sudo chmod -R 775 /var/www/html/upload
  ```

  ### Create application directory at webroot (or configure app/site):
  (note: with my Apache configuration, I just need to create a subdirectory)
  ```
  sudo mkdir -p /var/www/html/uploader
  ```

  ### Edit file config.php and adjust variables
  (website name, time zone etc.)
  ```
  sudo nano /home/ubuntu/uploader/config.php
  ```

  ### Copy all app files (html, php & js) to the app folder:
  ```
  sudo cp /home/ubuntu/uploader/*.* /var/www/html/uploader/
  ```

  ### Restart Apache to apply changes:

  ```
  sudo systemctl restart apache2
  ```

## Usage
Open your web browser and navigate to https://yourserveraddress/uploader

Enter your username and password to authenticate.

Choose a file to upload and click the "Upload" button.

The uploaded files will be listed on the page, and you can delete them using the "Delete" button.

![screenshot](https://github.com/sensboston/uploader/assets/1036158/5428672d-7dcc-4d7a-a96f-dfe578618c75)

## Issues / TODO
  - Add checking upload size before starting upload.
