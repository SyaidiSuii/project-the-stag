
````markdown
# Laravel Project Setup

## Requirements
Make sure you have the following installed on your machine:

- [PHP >= 8.1](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [MySQL or MariaDB](https://www.mysql.com/)
- [Node.js & NPM](https://nodejs.org/) (for frontend assets)
- [Git](https://git-scm.com/)

---
````

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/SyaidiSuii/project-the-stag.git
   cd project-the-stag


2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**

   ```bash
   npm install && npm run dev
   ```

4. **Copy the environment file**

   ```bash
   cp .env.example .env
   ```

5. **Generate the application key**

   ```bash
   php artisan key:generate
   ```

6. **Configure the `.env` file**
   Open `.env` and update your database & app configurations. Example:

   ```
   APP_NAME="The Stag SmartDine"
   APP_ENV=local
   APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
   APP_DEBUG=true
   APP_URL=https://the_stag.test
  

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run database migrations**

   ```bash
   php artisan migrate
   ```

8. **(Optional) Run seeders**

   ```bash
   php artisan db:seed
   ```

9. **Start the development server**

   ```bash
   php artisan serve
   ```

   Project will be available at:
   👉 [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Useful Commands

* Run tests

  ```bash
  php artisan test
  ```

* Clear cache

  ```bash
  php artisan cache:clear
  ```

* Compile assets in watch mode

  ```bash
  npm run dev
  ```

* Compile production assets

  ```bash
  npm run build
  ```

---

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

---

## License

This project is licensed under the [MIT License](LICENSE).

```
---
```
