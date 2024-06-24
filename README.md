# File Uploader

A simple file uploader application that allows authenticated users to upload, list, and delete files. 
The application uses PHP and a Python authentication service running on Apache2.

## Prerequisites

- Apache2, configured, up and running
- PHP 8.1 or higher
- Python 3
- Required PHP extensions: `php-json`, `php-curl`
- PAM authentication for Python

## Installation

For simplicity, I'll use my current Ubuntu instance user name, you should replace by yours.

  ### Clone or download this repository

   ```
   git clone https://github.com/yourusername/uploader.git
   cd uploader
   ```

  ### Install Python prerequisites

  ```
  pip install flask pam
  ```

  ### Create Python authentication service
  (note: **port 7000** is used; if you need to change port number, make needful changes in the **app.py** and php scripts - search for '7000')  
  
  ```
  sudo nano /etc/systemd/system/flaskapp.service
  ```

  Add the following content to this file (but replace **User**, **WorkingDirectory** and **ExecStart**):

  ```
  [Unit]
  Description=Flask Application
  After=network.target

  [Service]
  User=ubuntu
  WorkingDirectory=/home/ubuntu/uploader
  ExecStart=/usr/bin/python3 /home/ubuntu/uploader/app.py
  Restart=always

  [Install]
  WantedBy=multi-user.target
  ```

  ### Enable and start the service:
  ```
  sudo systemctl enable flaskapp
  sudo systemctl start flaskapp
  sudo systemctl status flaskapp.service
  ```

  ### Configure PHP

  Ensure the following PHP settings are in your **php.ini**:
  ```
  log_errors = On
  error_log = /var/log/php_errors.log
  ```
  
  Also check for max upload file/post size limits in **php.ini** (adjust to your needs, like 10G):
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

  ### Create a limited user for uploading files
  (please note, I don't recommend you to use your actual ssh-enabled user account):

  ```
  sudo useradd -M -d /var/www/html/upload -s /usr/sbin/nologin uploader
  sudo passwd uploader
  sudo chown -R uploader:www-data /var/www/html/upload
  ```

  ### Create application directory at webroot (or configure app/site):
  (note: with my Apache configuration, I just need to create a subdirectory)
  ```
  sudo mkdir -p /var/www/html/uploader
  ```

  ### Copy all files to the folder created above:
  ```
  sudo cp -r * /var/www/html/uploader
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
