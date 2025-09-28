# Aurora Hotel Management System

A modern PHP-based hotel management application with room booking, menu ordering, and real-time communication features.

## Features

### 🏨 **Room Management**
- Online room booking system
- Real-time room occupancy tracking
- Walk-in booking support for reception staff
- Automatic room availability checking

### 🍽️ **Menu & Ordering**
- Live menu management for kitchen staff
- Room service ordering system
- Order status tracking (pending → preparing → ready → delivered)
- Real-time order notifications

### 💬 **Communication**
- Real-time chat system between customers and staff
- Role-based messaging (receptionist, kitchen, customer)
- Automatic message notifications

### 👥 **User Roles**
- **Customer**: Book rooms, place orders, chat with staff
- **Receptionist**: Manage bookings, view room occupancy, handle walk-ins
- **Kitchen**: Manage menu items, track orders, update order status
- **Admin**: Full system access

## Tech Stack

- **Backend**: PHP 8.0+ with PDO
- **Database**: SQLite
- **Frontend**: HTML5, Tailwind CSS, Vanilla JavaScript
- **Server**: PHP Built-in Development Server

## Installation & Setup

### Prerequisites
- PHP 8.0 or higher
- Web server (Apache/Nginx) or PHP built-in server

### Quick Start

1. **Clone/Download the project**
   ```bash
   git clone <repository-url>
   cd Hotel
   ```

2. **Start the development server**
   ```bash
   php -S 127.0.0.1:8000 -t . index.php
   ```

3. **Initialize the database**
   - Visit `http://127.0.0.1:8000/setup` in your browser
   - This creates the SQLite database and seeds initial data

4. **Access the application**
   - Home page: `http://127.0.0.1:8000`
   - Login page: `http://127.0.0.1:8000/login`

## Default User Accounts

The setup script creates these test accounts (password: `password`):

| Role | Phone | Password | Access |
|------|-------|----------|---------|
| Receptionist | +254700000001 | password | Reception Dashboard |
| Kitchen Staff | +254700000002 | password | Kitchen Dashboard |
| Admin | +254700000003 | password | All Dashboards |

## Application Structure

```
Hotel/
├── app/
│   ├── Controllers/          # MVC Controllers
│   │   ├── AuthController.php
│   │   ├── BookingController.php
│   │   ├── ChatController.php
│   │   ├── KitchenController.php
│   │   ├── OrderController.php
│   │   └── ReceptionController.php
│   ├── Views/               # Template files
│   │   ├── auth/           # Login/Register pages
│   │   ├── booking/        # Room booking pages
│   │   ├── dashboard/      # Customer dashboard
│   │   ├── kitchen/         # Kitchen management
│   │   ├── reception/       # Reception dashboard
│   │   └── ...
│   └── Support/
│       └── helpers.php     # Utility functions
├── storage/
│   └── database.sqlite     # SQLite database
├── scripts/
│   └── setup.php          # Database initialization
├── index.php              # Application entry point
├── bootstrap.php          # Core functions & autoloader
└── config.php             # Application configuration
```

## How It Works

### 1. **Room Booking Flow**

**Customer Journey:**
1. Register/Login → Dashboard
2. Click "Book Room" → Select dates
3. System finds available room automatically
4. Proceed to payment (simulated)
5. Booking confirmed

**Reception Journey:**
1. Login as receptionist → Reception Dashboard
2. View room occupancy grid
3. Create walk-in bookings
4. Manage existing bookings

### 2. **Order Management Flow**

**Customer Journey:**
1. View live menu on dashboard
2. Place room service order
3. Track order status
4. Receive notifications

**Kitchen Journey:**
1. Login as kitchen staff → Kitchen Dashboard
2. View incoming orders
3. Update order status (preparing → ready → delivered)
4. Manage menu items (add/edit/stock control)

### 3. **Communication System**

- **Customer**: Can message reception or kitchen
- **Staff**: Receive messages based on their role
- **Real-time**: Messages poll every 3 seconds
- **Context**: Messages linked to current booking/room

## Key Features Explained

### Room Availability Algorithm
```php
// Checks for date conflicts
SELECT 1 FROM bookings 
WHERE room_id = ? 
AND status IN ('pending','confirmed') 
AND NOT (date(end_date) < date(?) OR date(start_date) > date(?))
```

### Order Status Workflow
1. **pending** → Customer places order
2. **preparing** → Kitchen starts preparation
3. **ready** → Food is ready for delivery
4. **delivered** → Order completed
5. **cancelled** → Order cancelled

### Real-time Updates
- Chat messages poll every 3 seconds
- Order status updates immediately
- Room occupancy refreshes on page load

## Configuration

Edit `config.php` to customize:

```php
const CONFIG = [
    'app' => [
        'name' => 'Aurora Hotel',
        'currency' => 'KES',
    ],
    'payments' => [
        'room_price_per_night' => 500000, // 5000.00 KES in cents
    ],
];
```

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Password Hashing**: Uses PHP's `password_hash()`
- **Role-based Access**: Controllers check user permissions
- **Input Validation**: All user inputs are sanitized
- **SQL Injection Prevention**: Uses prepared statements

## API Endpoints

### Authentication
- `GET /login` - Login form
- `POST /login` - Process login
- `POST /logout` - Logout user
- `GET /register` - Registration form
- `POST /register` - Process registration

### Customer Features
- `GET /dashboard` - Customer dashboard
- `GET /booking/new` - New booking form
- `POST /booking` - Create booking
- `GET /orders` - View orders
- `POST /orders/create` - Place order
- `GET /chat` - Chat interface

### Staff Features
- `GET /reception` - Reception dashboard
- `GET /kitchen` - Kitchen dashboard
- `POST /reception/book` - Create walk-in booking
- `POST /kitchen/menu/create` - Add menu item
- `POST /orders/update-status` - Update order status

## Database Schema

### Core Tables
- **users**: User accounts and roles
- **rooms**: Hotel room inventory
- **bookings**: Room reservations
- **payments**: Payment records
- **menu_items**: Restaurant menu
- **orders**: Room service orders
- **order_items**: Order line items
- **messages**: Chat messages

## Development

### Adding New Features
1. Create controller in `app/Controllers/`
2. Add routes in `index.php`
3. Create views in `app/Views/`
4. Update database schema in `scripts/setup.php`

### Testing
- Use the seeded test accounts
- Test all user roles and permissions
- Verify CSRF protection on forms
- Check real-time features (chat, orders)

## Troubleshooting

### Common Issues

**"Forbidden" Error:**
- Ensure you're logged in with correct role
- Check user permissions in database

**Database Errors:**
- Run setup script: `http://127.0.0.1:8000/setup`
- Check SQLite file permissions

**Chat Not Working:**
- Verify JavaScript is enabled
- Check browser console for errors

**Orders Not Updating:**
- Refresh page to see latest status
- Check kitchen staff is updating orders

## Production Deployment

1. **Web Server Setup**
   - Configure Apache/Nginx with PHP
   - Set document root to project directory
   - Enable URL rewriting for clean URLs

2. **Database**
   - Consider migrating to MySQL/PostgreSQL
   - Set up proper database credentials
   - Enable foreign key constraints

3. **Security**
   - Change default passwords
   - Enable HTTPS
   - Set secure session configuration
   - Implement rate limiting

4. **Performance**
   - Enable PHP OPcache
   - Use Redis for session storage
   - Implement caching for static data

## License

This project is open source and available under the MIT License.

## Support

For issues or questions:
1. Check this README
2. Review the code comments
3. Test with provided demo accounts
4. Check PHP error logs

---

**Happy Hotel Managing! 🏨✨**
