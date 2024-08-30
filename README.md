Cómo clonar el proyecto en un servidor Local git clone https://github.com/josyanez/testTorneoTenis.git 
cd testTorneoTenis
Instalar las dependencias del proyecto con: 
    composer install 
Configurar el archivo .env.example y dejarlo como .env y dentro colocar todas las variables de entorno de nuestro proyecto. 
Creamos la base de datos para nuestro proyecto. 
    CREATE DATABASE `torneo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

Generar una APP_KEY que es una llave para cada proyecto de Laravel se puede generar con este comando: 
    php artisan key:generate
Generar las migraciones y ejecutar los seeders para nuestras tablas de base de datos con este comando: 
    php artisan migrate --seed
