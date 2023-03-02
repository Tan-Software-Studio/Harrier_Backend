# harrier_backend
## Run commands First time
- **composer install**
- **php artisan storage:link**
- **php artisan migrate --seed**
- **php artisan passport:install**

<br><p>Personal access client created successfully.</p>
<br>PASSPORT_PERSONAL_ACCESS_CLIENT_ID="client-id-value"
<br>PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="unhashed-client-secret-value"
<br>
<!-- - **php artisan passport:client --personal** -->

## Existing Database tables cmd:
Please update <b>config/constants.php</b> file


## Existing Database tables cmd:
- **php artisan migrate**
- **composer update**


### .env    - udpate list
APP_NAME=Harrier <br>
APP_ENV=local<br>
APP_URL=http://localhost:8000/public<br>
APP_API_URL=http://harrier-user.s3-website.eu-west-2.amazonaws.com
FRONT_APP_URL=http://harrier-user.s3-website.eu-west-2.amazonaws.com
EMP_APP_URL=http://harrier-user.s3-website.eu-west-2.amazonaws.com
ADM_APP_URL=http://harrier-admin.s3-website.eu-west-2.amazonaws.com

<br>
CONTACT_MAIL=cs@harriercandidates.com
CONTACT_MAIL_NAME="Harrrier Help Center"
<br>
DB_CONNECTION=mysql<br>
DB_HOST=127.0.0.1<br>
DB_PORT=3306<br>
DB_DATABASE=harrier_backend<br>
DB_USERNAME=root<br>
DB_PASSWORD=************<br>

<br>
<br>
<br>APP_TIMEZONE = 'Asia/Kolkata'
<br>DB_TIMEZONE = '+05:30'
<br>
<br>
PASSPORT_PERSONAL_ACCESS_CLIENT_ID="**************"
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET="**************"


Admin login credential:
admin_harrier@yopmail.com
Admin@123