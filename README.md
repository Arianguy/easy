# Modern CRM Application

A comprehensive Customer Relationship Management (CRM) system built with Laravel 12, Livewire, and Flux UI components. Designed specifically for retail sales environments with multi-branch functionality and role-based access control.

## 🚀 Features

### Core CRM Functionality

-   **Customer Management**: Complete customer profiles with contact information, interests, budget tracking, and interaction history
-   **Lead Management**: Full lead lifecycle from creation to conversion with priority levels and follow-up scheduling
-   **Opportunity Tracking**: Revenue pipeline management with stage tracking and probability assessments
-   **Campaign Management**: Marketing campaign tracking with ROI analysis and performance metrics
-   **Activity Logging**: Comprehensive activity tracking for all customer interactions

### Multi-Branch Architecture

-   **Branch Isolation**: Secure data separation between different business locations
-   **Centralized Management**: Area managers can oversee all branches simultaneously
-   **Branch-Specific Analytics**: Detailed performance metrics for each location

### Role-Based Access Control

-   **Area Manager**: Full system access across all branches
-   **Sales Manager**: Complete branch management with team oversight
-   **Sales Executive**: Lead and customer management for assigned accounts

### Advanced Analytics

-   **Real-time Dashboard**: Comprehensive overview of sales performance and metrics
-   **Conversion Tracking**: Detailed funnel analysis from leads to closed deals
-   **Team Performance**: Individual and team performance metrics with leaderboards
-   **Revenue Forecasting**: Pipeline analysis with weighted probability calculations

### Modern UI/UX

-   **Responsive Design**: Mobile-first approach for on-the-go access
-   **Flux UI Components**: Beautiful, consistent interface elements
-   **Interactive Charts**: Visual data representation for better insights
-   **Dark Mode Support**: Comfortable viewing in any lighting condition

## 🛠️ Technology Stack

-   **Backend**: Laravel 12 with PHP 8.2+
-   **Frontend**: Livewire 3.0 with Flux UI components
-   **Database**: MySQL with comprehensive indexing
-   **Authentication**: Laravel Breeze with role-based permissions
-   **Permissions**: Spatie Laravel Permission package
-   **Styling**: Tailwind CSS 4.0
-   **Charts**: Chart.js for data visualization
-   **Icons**: Heroicons for consistent iconography

## 📋 Requirements

-   PHP 8.2 or higher
-   Composer 2.0+
-   Node.js 18+ and NPM
-   MySQL 8.0+ or MariaDB 10.4+
-   Web server (Apache/Nginx)

## 🔧 Installation

### Quick Setup

1. **Clone and Setup**

    ```bash
    git clone <repository-url>
    cd easy
    ```

2. **Install Dependencies**

    ```bash
    composer install
    npm install
    ```

3. **Environment Configuration**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database Setup**

    ```bash
    # Create database
    mysql -u root -p -e "CREATE DATABASE crm_app"

    # Update .env with your database credentials
    # DB_DATABASE=crm_app
    # DB_USERNAME=your_username
    # DB_PASSWORD=your_password
    ```

5. **Run Migrations and Seeders**

    ```bash
    php artisan migrate --seed
    ```

6. **Build Assets**

    ```bash
    npm run build
    ```

7. **Start Development Server**
    ```bash
    php artisan serve
    ```

### Production Deployment

For production deployment, ensure you:

-   Set `APP_ENV=production` in your `.env` file
-   Configure your web server to point to the `public` directory
-   Set up SSL certificates for HTTPS
-   Configure caching and queue workers
-   Set up regular database backups

## 🔑 Default Login Credentials

| Role              | Email                | Password |
| ----------------- | -------------------- | -------- |
| Area Manager      | admin@company.com    | password |
| Sales Manager 1   | manager1@company.com | password |
| Sales Manager 2   | manager2@company.com | password |
| Sales Executive 1 | sales1@company.com   | password |
| Sales Executive 2 | sales2@company.com   | password |

## 📊 Database Schema

### Core Tables

-   **branches**: Store locations and branch information
-   **users**: User accounts with role assignments
-   **customers**: Customer profiles and contact information
-   **leads**: Lead tracking with status and priority
-   **opportunities**: Revenue opportunities with stage management
-   **campaigns**: Marketing campaign tracking
-   **activities**: Polymorphic activity logging

### Key Relationships

-   One-to-one: Lead → Opportunity
-   One-to-many: Branch → Users, Customers, Leads
-   Many-to-many: Users ↔ Roles (via Spatie Permissions)
-   Polymorphic: Activities → Customers/Leads/Opportunities

## 🔐 Security Features

### Data Protection

-   **Branch Scoping**: Global scopes ensure data isolation
-   **Role-Based Access**: Granular permissions for different user types
-   **Input Validation**: Comprehensive form validation and sanitization
-   **CSRF Protection**: Built-in Laravel CSRF protection
-   **SQL Injection Prevention**: Eloquent ORM with parameter binding

### Authentication

-   **Secure Password Hashing**: Bcrypt with configurable rounds
-   **Session Management**: Secure session handling
-   **Remember Me**: Optional persistent login
-   **Password Reset**: Secure password recovery system

## 📈 Performance Optimizations

### Database

-   **Strategic Indexing**: Optimized indexes for common queries
-   **Eager Loading**: Prevent N+1 query problems
-   **Query Optimization**: Efficient database queries
-   **Connection Pooling**: Optimized database connections

### Caching

-   **Model Caching**: Cache frequently accessed data
-   **Route Caching**: Improved routing performance
-   **View Caching**: Compiled view templates
-   **Configuration Caching**: Cached application configuration

## 🧪 Testing

Run the test suite:

```bash
php artisan test
```

### Test Coverage

-   Feature tests for all major functionality
-   Unit tests for business logic
-   Integration tests for API endpoints
-   Browser tests for critical user flows

## 🔄 API Documentation

### Authentication

All API endpoints require authentication via Laravel Sanctum tokens.

### Key Endpoints

-   `GET /api/customers` - List customers
-   `POST /api/leads` - Create new lead
-   `PUT /api/opportunities/{id}` - Update opportunity
-   `GET /api/analytics/dashboard` - Dashboard metrics

## 📱 Mobile Responsiveness

The application is fully responsive and optimized for:

-   Desktop computers (1920px+)
-   Tablets (768px - 1024px)
-   Mobile phones (320px - 767px)

## 🌐 Browser Support

-   Chrome 90+
-   Firefox 88+
-   Safari 14+
-   Edge 90+

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines

-   Follow PSR-12 coding standards
-   Write comprehensive tests for new features
-   Update documentation for any API changes
-   Use conventional commit messages

## 📝 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 🆘 Support

For support and questions:

-   Create an issue in the repository
-   Check the documentation wiki
-   Review existing issues for solutions

## 🚀 Roadmap

### Upcoming Features

-   [ ] Email integration for automated campaigns
-   [ ] Advanced reporting and analytics
-   [ ] Mobile app for iOS and Android
-   [ ] Integration with popular CRM platforms
-   [ ] Advanced workflow automation
-   [ ] Multi-language support
-   [ ] Advanced search and filtering
-   [ ] Document management system

### Performance Improvements

-   [ ] Redis caching implementation
-   [ ] Queue system for background jobs
-   [ ] Database query optimization
-   [ ] CDN integration for assets

## 🏆 Acknowledgments

-   Laravel community for the excellent framework
-   Livewire team for reactive components
-   Flux UI for beautiful interface components
-   Spatie for the permissions package
-   All contributors and testers

---

**Built with ❤️ for modern sales teams**
