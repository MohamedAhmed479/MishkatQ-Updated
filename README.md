# Mishkat - Quran Memorization Platform

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A comprehensive, feature-rich Quran memorization platform built with Laravel 12, designed to help users memorize the Holy Quran through intelligent planning, spaced repetition, and gamification features.

## 🌟 Features

### 📚 Quran Management
- **Complete Quran Database**: All 114 chapters (Surahs) with verses, words, and metadata
- **Multiple Text Formats**: Uthmani and Imlaei text support
- **Juz, Hizb, and Page Organization**: Traditional Quran divisions
- **Tafsir Integration**: Multiple interpretation sources
- **Audio Recitations**: Various reciter options
- **Search & Filtering**: Advanced search capabilities across text and metadata

### 🎯 Memorization System
- **Intelligent Planning**: AI-powered memorization plan generation
- **Adaptive Scheduling**: Plans adjust based on user performance and preferences
- **Daily Memorization Items**: Structured daily learning objectives
- **Progress Tracking**: Detailed progress monitoring per chapter and verse
- **Customizable Preferences**: User-defined learning pace and session frequency

### 🔄 Spaced Repetition
- **Scientific Algorithm**: Evidence-based spaced repetition intervals
- **Smart Scheduling**: Automatic review scheduling based on performance
- **Performance Tracking**: Rating system (1-5) for review quality
- **Adaptive Intervals**: Dynamic adjustment based on user performance
- **Overdue Management**: Handling of missed review sessions

### 🏆 Gamification & Incentives
- **Points System**: Earn points for memorization and reviews
- **Achievement Badges**: Unlockable badges for various milestones
- **Leaderboards**: Competitive rankings (daily, weekly, monthly, yearly)
- **Progress Statistics**: Comprehensive analytics and insights
- **Motivation System**: Encouraging progress visualization

### 👥 User Management
- **Multi-User Support**: Individual user accounts with profiles
- **Email Verification**: Secure account verification system
- **Device Management**: Track and manage multiple devices
- **User Preferences**: Customizable learning settings
- **Progress Analytics**: Personal learning insights

### 🔐 Security & Administration
- **Role-Based Access Control**: Granular permission system
- **Admin Dashboard**: Comprehensive management interface
- **Audit Logging**: Complete activity tracking and monitoring
- **Device Authentication**: Secure multi-device access
- **API Security**: Sanctum-based authentication

### 📊 Analytics & Reporting
- **Progress Analytics**: Visual progress tracking
- **Performance Metrics**: Detailed learning statistics
- **Admin Insights**: System-wide analytics and monitoring
- **User Behavior Analysis**: Learning pattern insights
- **Export Capabilities**: Data export for analysis

## 🚀 Technology Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP Version**: 8.2+
- **Database**: MySQL/PostgreSQL (configurable)
- **Authentication**: Laravel Sanctum
- **Permissions**: Spatie Laravel Permission
- **Queue System**: Laravel Queue with Redis support
- **Caching**: Redis/Memcached
- **Testing**: PHPUnit with Laravel Testing

### Frontend
- **CSS Framework**: Tailwind CSS 4.0
- **Build Tool**: Vite 6.x
- **JavaScript**: ES6+ with Axios
- **Responsive Design**: Mobile-first approach

### Development Tools
- **Code Quality**: Laravel Pint
- **Debugging**: Laravel Telescope
- **Package Management**: Composer
- **Version Control**: Git

## 📋 Requirements

### System Requirements
- PHP 8.2 or higher
- Composer 2.0 or higher
- Node.js 18+ and NPM
- MySQL 8.0+ or PostgreSQL 12+
- Redis (optional, for caching and queues)

### PHP Extensions
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## 🛠️ Installation

### 1. Clone the Repository
```bash
git clone https://github.com/MohamedAhmed479/MishkatQ-Updated.git
cd mishkatq
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Configure database in .env file
# Run migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

### 5. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Development Server
```bash
# Start Laravel server
php artisan serve

# Start queue worker (in separate terminal)
php artisan queue:work

# Start Vite dev server (in separate terminal)
npm run dev
```

## 🗄️ Database Structure

### Core Models
- **User**: User accounts and authentication
- **Admin**: Administrative users with role-based permissions
- **Chapter**: Quran chapters (Surahs)
- **Verse**: Individual Quran verses
- **Word**: Individual words within verses
- **MemorizationPlan**: User memorization strategies
- **PlanItem**: Daily memorization objectives
- **SpacedRepetition**: Review scheduling system
- **ReviewRecord**: Performance tracking for reviews
- **Badge**: Achievement system
- **Leaderboard**: Competitive rankings
- **AuditLog**: System activity monitoring

### Key Relationships
- Users have multiple memorization plans
- Plans contain multiple plan items
- Plan items generate spaced repetition schedules
- Reviews are recorded for performance tracking
- Users earn badges and points for achievements

## 🔌 API Endpoints

### Authentication
- `POST /api/v1/auth/register` - User registration
- `POST /api/v1/auth/login` - User login
- `POST /api/v1/auth/logout` - User logout
- `POST /api/v1/auth/verify-email` - Email verification
- `POST /api/v1/auth/forgot-password` - Password reset request
- `POST /api/v1/auth/reset-password` - Password reset

### Quran Data
- `GET /api/v1/quran/chapters` - List all chapters
- `GET /api/v1/quran/chapters/{id}` - Get chapter details
- `GET /api/v1/quran/chapters/{id}/verses` - Get chapter verses
- `GET /api/v1/quran/verses/random` - Get random verse
- `GET /api/v1/quran/verses/{id}` - Get verse details
- `GET /api/v1/quran/juzs` - List all Juzs
- `GET /api/v1/quran/statistics` - Get Quran statistics

### Memorization
- `GET /api/v1/memorization-plans` - User's memorization plans
- `POST /api/v1/memorization-plans` - Create new plan
- `GET /api/v1/memorization-plans/{id}` - Get plan details
- `POST /api/v1/memorization-plans/{id}/active` - Activate plan
- `POST /api/v1/memorization-plans/{id}/pause` - Pause plan

### Daily Learning
- `GET /api/v1/daily-memorization/getTodayItem` - Get today's learning item
- `GET /api/v1/daily-memorization/plan-item/{id}` - Get item content
- `POST /api/v1/daily-memorization/complete` - Mark item as completed
- `GET /api/v1/daily-memorization/today-revisions` - Get today's reviews
- `GET /api/v1/daily-memorization/last-uncompleted-revisions` - Get overdue reviews

### Progress & Analytics
- `GET /api/v1/memorization-progress` - User progress overview
- `GET /api/v1/memorization-progress/chapter/{id}` - Chapter-specific progress
- `GET /api/v1/active-memorization-plan/analytics` - Plan analytics
- `GET /api/v1/revision-reviews/{id}/record` - Record review performance

### Incentives & Gamification
- `GET /api/v1/badges` - Available badges
- `GET /api/v1/users/{id}/badges` - User's earned badges
- `GET /api/v1/users/{id}/points-history` - Points transaction history
- `GET /api/v1/leaderboard` - Competitive rankings
- `GET /api/v1/users/{id}/stats` - User statistics

### User Preferences
- `GET /api/v1/preferences` - Get user preferences
- `PUT /api/v1/preferences` - Update user preferences

## 🎨 Admin Dashboard

### Management Features
- **User Management**: Create, edit, and manage user accounts
- **Content Management**: Manage Quran content, chapters, verses, and words
- **Badge System**: Create and manage achievement badges
- **Leaderboard Management**: Monitor and manage competitive rankings
- **System Monitoring**: Real-time system health and performance
- **Audit Logs**: Comprehensive activity tracking and security monitoring

### Analytics & Reporting
- **Dashboard Overview**: Key performance indicators and metrics
- **User Analytics**: Learning progress and behavior analysis
- **System Statistics**: Platform usage and performance data
- **Export Capabilities**: Data export for external analysis

## 🔒 Security Features

### Authentication & Authorization
- **Multi-Guard Authentication**: Separate guards for users and admins
- **Role-Based Access Control**: Granular permission system
- **API Token Management**: Secure API access with Sanctum
- **Device Tracking**: Monitor and manage user devices

### Data Protection
- **Audit Logging**: Complete activity tracking
- **Input Validation**: Comprehensive request validation
- **SQL Injection Prevention**: Parameterized queries
- **XSS Protection**: Output sanitization
- **CSRF Protection**: Cross-site request forgery prevention

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests with coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: API endpoint testing
- **Unit Tests**: Individual component testing
- **Test Data**: Factory-generated test data

## 📦 Deployment

### Production Setup
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set up queue workers
php artisan queue:work --daemon

# Configure web server (Apache/Nginx)
# Set up SSL certificates
# Configure database backups
```

### Environment Variables
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mishkatq
DB_USERNAME=your_username
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

## 🤝 Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Standards
- Follow PSR-12 coding standards
- Write comprehensive tests for new features
- Update documentation as needed
- Follow Laravel best practices

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel Team**: For the amazing framework
- **Spatie**: For the permission package
- **Quran.com**: For Quran data inspiration
- **Open Source Community**: For various packages and tools

## 📞 Support

### Getting Help
- **Documentation**: Check the docs folder for detailed guides
- **Issues**: Report bugs and feature requests via GitHub Issues
- **Discussions**: Join community discussions on GitHub
- **Email**: Contact the development team directly

### Community
- **GitHub**: [Repository](https://github.com/MohamedAhmed479/MishkatQ-Updated)
- **Discussions**: [GitHub Discussions](https://github.com/MohamedAhmed479/MishkatQ-Updated/discussions)
- **Wiki**: [Project Wiki](https://github.com/MohamedAhmed479/MishkatQ-Updated/wiki)

---
**Mishkat** - Illuminating the path to Quran memorization through technology and innovation. 🌟
