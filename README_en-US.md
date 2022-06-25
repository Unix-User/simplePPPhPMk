# simplePPPhPMk


PPP users control panel on mikrotik devices/routers. Register mikrotik devices/routeros for local or cloud PPP user management, made with PHP.

Automatic registration of users and their profiles on the <a href='https://github.com/Unix-User/MP_unixlocal'>site</a> for synchronization with the PPP server of mikrotik/routero devices, retrieve the records in the database with the click of a button.

![gif1](https://user-images.githubusercontent.com/38821945/157555687-712ad725-e2a4-48c3-86f4-3ad04ef49f19.gif)

Get real-time data and resource information from registered devices or connected users, being able to disconnect, enable and delete user registrations (new users and connection profiles are automatically added by the sync function and obtained from <a href='https:// unixlocal.ml'>site</a> or create, modify, update and delete new devices;

![gif21](https://user-images.githubusercontent.com/38821945/157559421-937dcf23-7758-4430-905e-ef327f34ec2a.gif)


Requirements:

- Apache
- PHP(tested on PHP 7.4.3)
- Composer

Recomended:

Pre installed VPN <a href='https://github.com/hwdsl2/setup-ipsec-vpn'>lt2p/ipsec:</a>


Instalation:

- clone this repository, and run composer update inside the project folder
  ```
  git clone https://github.com/Unix-User/simplePPPhPMk.git
  cd simplePPPhPMk/
  composer update
  ```
- edit the file /etc//sudoers and add permissions for user run the vpn script
    ```
    ~$ sudo nano /etc/sudoers
    ```
    You should add custom permissions for your server security, for practicality and testing purposes, add the following permissions to the file (remember to change them later)
    ```
    ## adding the below permissions allow the HTTP server to execute any sudo command
    www-data        ALL=(ALL) NOPASSWD:/usr/bin
    ```
    
You can view this project running on <a href='srv.unixlocal.tk'>srv.unixlocal.tk</a>
