# 🛒 Grocerific - Grocery Management System

A complete grocery management system built with PHP, MySQL, and modern web technologies. This application allows users to add, update, and delete grocery items with a clean, responsive interface.

## Features

- ✅ **Add Items**: Add new grocery items with name, category, quantity, price, and description
- ✅ **Update Items**: Edit existing items with a user-friendly modal interface
- ✅ **Delete Items**: Remove items with confirmation dialogs
- ✅ **Search & Filter**: Search by name/description and filter by category
- ✅ **Responsive Design**: Works great on desktop and mobile devices
- ✅ **Modern UI**: Clean, professional interface with smooth animations

## Technologies Used

- **Backend**: PHP 7.4+ with PDO for database operations
- **Database**: MySQL 5.7+ or MariaDB
- **Frontend**: HTML5, CSS3 (Grid/Flexbox), Vanilla JavaScript
- **Architecture**: RESTful API design with JSON responses

## Setup Instructions

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher (or MariaDB)
- Web server (Apache/Nginx) or PHP built-in server

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/mikie27/Active-Learning-241---Grocerific.git
   cd Active-Learning-241---Grocerific
   ```

2. **Set up the database**
   - Create a MySQL database named `grocerific`
   - Import the database schema:
   ```bash
   mysql -u root -p grocerific < database.sql
   ```

3. **Configure database connection**
   - Edit `config.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'grocerific');
   ```

4. **Start the application**
   - Using PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
   - Or set up with Apache/Nginx pointing to the project directory

5. **Access the application**
   - Open your browser and go to `http://localhost:8000`

## File Structure

```
├── index.html          # Main application interface
├── styles.css          # CSS styling and responsive design
├── script.js           # Frontend JavaScript functionality
├── api.php             # RESTful API endpoints
├── config.php          # Database configuration
├── database.sql        # Database schema and sample data
└── README.md           # This file
```

## API Endpoints

- `GET api.php?action=items` - Get all grocery items
- `GET api.php?action=item&id={id}` - Get single item
- `POST api.php?action=add` - Add new item
- `PUT api.php?action=update` - Update existing item
- `DELETE api.php?action=delete&id={id}` - Delete item

## Usage

1. **Adding Items**: Fill out the form at the top with item details and click "Add Item"
2. **Editing Items**: Click the "Edit" button on any item card to open the edit modal
3. **Deleting Items**: Click the "Delete" button and confirm the action
4. **Searching**: Use the search box to find items by name or description
5. **Filtering**: Use the category dropdown to filter items by category

## Database Schema

The application uses a single table `grocery_items` with the following structure:

- `id` - Primary key (auto-increment)
- `name` - Item name (required)
- `category` - Item category (required)
- `quantity` - Number of items (required)
- `price` - Item price in dollars (required)
- `description` - Optional item description
- `created_at` - Timestamp when item was created
- `updated_at` - Timestamp when item was last updated

## Contributing

This project is for educational purposes (Active Learning 241). Feel free to fork and modify for your learning needs.

## License

Educational use only - Created for learning purposes.