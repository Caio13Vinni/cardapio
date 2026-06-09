# Cardáp.io

Cardáp.io is a full-stack web application designed for restaurants to create, manage, and share their digital menus with autonomy. It provides a simple interface for restaurant owners to update their offerings and a modern, responsive view for customers.

## Architecture

The project is structured into two main parts: a front-end single-page application and a back-end API.

*   **`front-end/`**: A Vue.js 3 application that serves as the user-facing interface. It includes a public landing page to attract restaurant owners and the authentication pages (login and registration) for the management dashboard.
*   **`back-end/`**: A PHP-based API that handles server-side logic, including user authentication, registration, and database interactions with a MySQL database.

## Features

*   **User Authentication**: Secure registration and login system for restaurant owners.
*   **Password Strength Indicator**: Real-time feedback on password strength during registration.
*   **Responsive Landing Page**: A marketing page detailing the product's features, benefits, and testimonials.
*   **Intuitive Menu Management**: The application is designed to allow easy management of dishes and categories.
*   **QR Code & Link Sharing**: Functionality to generate and share the digital menu via QR codes and direct links.
*   **Visual Customization**: Tools for restaurant owners to customize the menu's appearance to match their brand identity.

## Technology Stack

*   **Frontend**:
    *   Vue.js 3
    *   Vue Router
    *   HTML5 & CSS3

*   **Backend**:
    *   PHP
    *   MySQL

## Local Development Setup

To run this project locally, you will need to set up both the back-end and front-end environments.

### Backend Setup

1.  **Prerequisites**: A local server environment with PHP and MySQL (e.g., XAMPP, MAMP, WAMP).

2.  **Database Configuration**:
    *   Create a new MySQL database named `cardapio`.
    *   Create a database user. The default credentials in `back-end/conexao.php` are:
        *   **Username**: `dev_cardapio`
        *   **Password**: `root`
        *   **Host**: `127.0.0.1`
    *   Execute the following SQL query to create the `usuarios` table:

    ```sql
    CREATE TABLE `cardapio`.`usuarios` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `nome` VARCHAR(255) NOT NULL,
      `email` VARCHAR(255) NOT NULL UNIQUE,
      `usuario` VARCHAR(255) NOT NULL,
      `senha` VARCHAR(255) NOT NULL
    );
    ```

3.  **Server**: Place the contents of the `back-end` directory into your local web server's document root (e.g., `/htdocs/cardapio/back-end/`).

### Frontend Setup

1.  **Prerequisites**: Node.js and npm installed.

2.  **Installation**:
    *   Navigate to the `front-end` directory:
        ```sh
        cd front-end
        ```
    *   Install the required npm packages:
        ```sh
        npm install
        ```

3.  **API Configuration**:
    *   The frontend expects the backend API to be available. In `front-end/src/views/LoginPage.vue` and `front-end/src/views/RegisterPage.vue`, the API endpoints are hardcoded (e.g., `/cardapio/back-end/valida.php`). Ensure your local server is configured to handle these paths or update them to match your setup (e.g., `http://localhost/cardapio/back-end/valida.php`).

4.  **Running the Development Server**:
    *   Start the Vue.js development server:
        ```sh
        npm run serve
        ```
    *   The application will be available at `http://localhost:8080` (or another port if 8080 is in use).

## API Endpoints

The backend provides the following endpoints for user management:

#### Register a New User

*   **URL**: `/cadastrar.php`
*   **Method**: `POST`
*   **Body**:o run this project locally, you will need to set u
    ```json
    {
      "nome": "Restaurante Exemplo",
      "email": "contato@exemplo.com",
      "senha": "password123"
    }
    ```
*   **Response (Success)**:
    ```json
    {
      "sucesso": true,
      "mensagem": "Usuário cadastrado com sucesso!"
    }
    ```
*   **Response (Error)**:
    ```json
    {
      "sucesso": false,
      "mensagem": "Erro ao cadastrar o usuário no banco de dados."
    }
    ```

#### User Login

*   **URL**: `/valida.php`
*   **Method**: `POST`
*   **Body**:
    ```json
    {
      "cardapioLogin": true,
      "email": "contato@exemplo.com",
      "senha": "password123"
    }
    ```
*   **Response (Success)**:
    ```json
    {
      "success": true,
      "message": "Login realizado com sucesso",
      "user": {
        "id": 1,
        "nome": "Restaurante Exemplo"
      }
    }
    ```
*   **Response (Error)**:
    ```json
    {
      "success": false,
      "message": "Usuário ou Senha incorretos!"
    }
