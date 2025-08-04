# GuardaChat

**Keep track of your past conversations.**

GuardaChat is a lightweight PHP MVC application that allows users to import chat history from JSON files into an SQLite database. It provides a user-friendly interface to browse and query conversations through a dynamic frontend.

## Features

- Import JSON files into an SQLite database.
- View list of chat sessions and inspect individual messages.
- JavaScript-powered frontend with `fetch()` API integration.
- MVC structure with routing and CRUD support.

>Please note that the application currently supports only text messages, and may not handle well those from web searches.

## Tech Stack

- **Backend:** PHP (MVC architecture)
- **Database:** SQLite
- **Web Server:** Apache
- **Dependencies:** Composer, [Fastvolt Markdown Parser](https://github.com/fastvolt/markdown)
- **Frontend:** Vanilla JavaScript (fetch API)

## Installation

### Requirements

- PHP 8.0+  
- Apache server (or a compatible setup like XAMPP, MAMP, etc.)  
- Composer

### Setup Steps

1. **Clone the repository**

   ```bash
   git clone https://github.com/eduardoverap/guardachat.git

2. **Install dependencies**

   ```bash
   composer install

3. **Deploy**

   - Option 1: Copy the project folder into the `/htdocs/` directory of your XAMPP/MAMP setup.
   - Option 2: Mount the folder in a PHP + Apache container.

4. **Change your timezone**

   In your project folder, open `/config/config.php` with a text editor, replace `America/Lima` with your local timezone (find it on [PHP documentation](http://php.net/manual/en/timezones.php)), and save the file.

   ```php
   // Set timezone
   date_default_timezone_set('America/Lima');

5. **Launch the application**

   Open your browser and navigate to http://localhost/guardachat

6. **Import your chats**

   - Uncompress your ChatGPT TAR.GZ downloaded files.
   - Copy and rename your `conversation.json` files into the `/storage/json/` directory.
   - Go to the `Import` page (it will show up on the first run of the application or if you haven't created your database yet).
   - Click on the `Import my chats` button to start. It will take several minutes to complete the process.

## Project Status

This is a beta version. The core functionality is implemented, but additional improvements and corrections are planned.

As this is a project made as PHP practice, plans for porting this to an offline/desktop app are not considered in the short term. Feel free to fork, tweak and build upon this code to match your needs.

## Author

Developed by Eduardo Vera Palomino.
LinkedIn: @eduardoverap | GitHub: @eduardoverap

## License

This project is licensed under the MIT License.
