## Sourceguardian Directadmin Installer

Deleting expired users through the database in IBSng

## How to use system

1) Upload bash file to the IBSng server for get list expire user and then run it on the server

```bash
wget -O database_get.sh https://raw.githubusercontent.com/MrAriaNet/IBSngDeleteExpireUser/main/database_get.sh
chmod +x database_get.sh
bash database_get.sh
```

2) Upload php files in Linux host for run script
3) Edit info.txt and paste the user ID received from the bash script that you got in the first step
4) Edit userdelete.php file and added info login IBSng
5) Run userdelete.php and delete all user expire

## Author

[Aria](https://github.com/MrAriaNet)
