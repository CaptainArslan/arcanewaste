# Arcane Waste Management System

A comprehensive waste management platform built with Laravel 12, designed to streamline dumpster rental and waste collection services for companies, customers, and drivers.

## 🚀 Features

### Core Functionality
- **Multi-tenant Architecture**: Companies can manage their own waste management operations
- **Dumpster Management**: Track dumpster inventory, sizes, and availability
- **Order Management**: Complete order lifecycle from placement to completion
- **Driver Management**: Driver scheduling, attendance, and location tracking
- **Customer Portal**: Self-service customer management and order placement
- **Payment Integration**: Finix payment processing with merchant onboarding
- **Document Management**: File upload and management system
- **Location Tracking**: Real-time location updates for drivers and customers

### Business Features
- **Waste Type Classification**: Support for different waste types including hazardous materials
- **Pricing Management**: Dynamic pricing with taxes, discounts, and promotions
- **Warehouse Management**: Multi-warehouse support with capacity tracking
- **Holiday Management**: Advanced holiday scheduling with polymorphic support for companies and drivers
- **Time Scheduling**: Flexible timing management for operations
- **Device Token Management**: Push notification support for mobile apps

## 🛠 Technology Stack

- **Backend**: Laravel 12.30.1 (PHP 8.2.12)
- **Database**: MySQL/SQLite
- **Authentication**: JWT (tymon/jwt-auth)
- **API Documentation**: Swagger/OpenAPI (l5-swagger)
- **Testing**: Pest PHP
- **Frontend Assets**: Vite + Tailwind CSS 4.1.13
- **Development Tools**: Laravel Telescope, Laravel Pint, Laravel Sail
- **Payment Processing**: Finix API integration
- **File Storage**: AWS S3 support
- **Logging**: Advanced log viewer
- **Queue System**: Redis/Predis support
- **Real-time**: Pusher integration

## 📋 Requirements

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM
- Redis (optional, for caching)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd arcane-2nd-version
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

## ⚙️ Configuration

### Environment Variables

Key environment variables to configure:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arcane_waste_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Finix Payment Integration
FINIX_MODE=sandbox
FINIX_SANDBOX_BASE_URL=https://sandbox.finix.io
FINIX_SANDBOX_USER_NAME=your_finix_username
FINIX_SANDBOX_PASSWORD=your_finix_password
FINIX_SANDBOX_API_VERSION=2022-02-01

# AWS S3 (for file storage)
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your_bucket_name

# JWT Configuration
JWT_SECRET=your_jwt_secret
```

## 🏗 Project Structure

```
app/
├── Enums/                    # Application enums
├── Events/                   # Event classes
├── Helpers/                  # Helper functions
├── Http/
│   ├── Controllers/Api/V1/   # API controllers
│   ├── Middleware/           # Custom middleware
│   ├── Requests/             # Form request validation
│   └── Resources/            # API resources
├── Listeners/                # Event listeners
├── Models/                   # Eloquent models
├── Providers/                # Service providers
├── Repositories/             # Repository pattern implementation
├── Rules/                    # Custom validation rules
└── Services/                 # Business logic services

database/
├── factories/                # Model factories
├── migrations/               # Database migrations
└── seeders/                  # Database seeders
```

## 🔧 Key Models

### Core Entities
- **Company**: Multi-tenant companies managing waste operations with Finix payment integration
- **Customer**: End customers using waste management services with polymorphic relationships
- **Driver**: Delivery and collection drivers with employment tracking
- **Order**: Waste collection orders with full lifecycle tracking (pending → completed)
- **Dumpster**: Physical dumpsters with size and status tracking
- **Warehouse**: Storage facilities for dumpsters with capacity management
- **DumpsterSize**: Configurable dumpster sizes with pricing and specifications

### Supporting Models
- **Address**: Polymorphic address management for all entities
- **Document**: File and document management with S3 integration
- **PaymentMethod**: Payment processing configuration
- **WasteType**: Waste classification system
- **Timing**: Flexible scheduling system for companies and warehouses
- **Holiday**: Advanced holiday management with polymorphic relationships
- **Promotion**: Marketing promotions with discount management
- **Tax**: Tax configuration with percentage and fixed rate support
- **DeviceToken**: Push notification device management
- **LatestLocation**: Real-time location tracking
- **Contact**: Emergency contact management
- **GeneralSetting**: Configurable application settings
- **PaymentOption**: Payment terms and conditions
- **OrderPricing**: Order-specific pricing calculations
- **OrderDiscount**: Applied discounts on orders
- **OrderPayment**: Payment tracking for orders
- **OrderAddress**: Delivery address for orders
- **OrderTiming**: Order-specific timing information
- **DriverAttendance**: Driver attendance tracking
- **DriverBreak**: Driver break time management
- **DriverOverTime**: Overtime tracking
- **DriverTimeSchedule**: Driver scheduling system
- **MerchantOnboardingLog**: Finix onboarding process tracking
- **PasswordResetTokens**: OTP and password reset management
- **SecureFile**: Secure file storage management

## 🚀 Development

### Running the Application

```bash
# Start development server
composer run dev

# Or run individually
php artisan serve
php artisan queue:listen
npm run dev
```

### Testing

```bash
# Run tests
composer run test

# Or with Pest
./vendor/bin/pest
```

### Code Quality

```bash
# Code formatting
./vendor/bin/pint

# Static analysis (if configured)
./vendor/bin/phpstan
```

## 📚 API Documentation

API documentation is available via Swagger UI. After starting the application:

1. Generate API docs: `php artisan l5-swagger:generate`
2. Access documentation at: `http://localhost:8000/api/documentation`

### Key API Endpoints

#### Authentication
- `POST /api/v1/company/auth/send-otp` - Send OTP for registration
- `POST /api/v1/company/auth/register` - Company registration with OTP verification
- `POST /api/v1/company/auth/login` - Company login
- `POST /api/v1/company/auth/forgot-password` - Send OTP for password reset
- `POST /api/v1/company/auth/reset-password` - Reset password with OTP
- `POST /api/v1/company/auth/update-password` - Update password (authenticated)
- `POST /api/v1/company/auth/logout` - Company logout
- `GET /api/v1/company/details` - Get company details

#### Media Management
- `POST /api/v1/media/upload` - Upload files to S3 with optional cleanup

#### Company Management
- `GET /api/v1/company/payment-methods` - List available payment methods
- `GET /api/v1/company/payment-methods/{code}` - Get specific payment method
- `GET /api/v1/company/payment-methods/{code}/onboarding-requirements` - Get onboarding requirements

#### General Settings
- `GET /api/v1/company/general-settings` - List company settings
- `GET /api/v1/company/general-settings/{id}` - Get specific setting
- `PUT /api/v1/company/general-settings/{id}/{key}` - Update setting

#### Payment Options
- `GET /api/v1/company/payment-options` - List payment options
- `GET /api/v1/company/payment-options/{id}` - Get specific payment option
- `PUT /api/v1/company/payment-options/{id}/{type}` - Update payment option

#### Warehouse Management
- `GET /api/v1/company/warehouses` - List warehouses
- `GET /api/v1/company/warehouses/{id}` - Get specific warehouse
- `POST /api/v1/company/warehouses` - Create warehouse
- `PUT /api/v1/company/warehouses/{id}` - Update warehouse
- `DELETE /api/v1/company/warehouses/{id}` - Delete warehouse

#### Timing Management
- `GET /api/v1/company/timings` - List company timings
- `GET /api/v1/company/timings/{id}` - Get specific timing
- `PUT /api/v1/company/timings/sync` - Sync timings

#### Holiday Management
- `GET /api/v1/company/holidays` - List company holidays
- `GET /api/v1/company/holidays/{id}` - Get specific holiday
- `POST /api/v1/company/holidays` - Create holiday
- `PUT /api/v1/company/holidays/{id}` - Update holiday
- `DELETE /api/v1/company/holidays/{id}` - Delete holiday
- `GET /api/v1/days-of-week-options` - Get day options for weekly holidays

#### Tax Management
- `GET /api/v1/company/taxes` - List taxes
- `GET /api/v1/company/taxes/{id}` - Get specific tax
- `POST /api/v1/company/taxes` - Create tax
- `PUT /api/v1/company/taxes/{id}` - Update tax
- `DELETE /api/v1/company/taxes/{id}` - Delete tax

#### Dumpster Size Management
- `GET /api/v1/company/dumpster-sizes` - List dumpster sizes
- `GET /api/v1/company/dumpster-sizes/{id}` - Get specific dumpster size
- `POST /api/v1/company/dumpster-sizes` - Create dumpster size
- `PUT /api/v1/company/dumpster-sizes/{id}` - Update dumpster size
- `DELETE /api/v1/company/dumpster-sizes/{id}` - Delete dumpster size

#### Dumpster Management
- `GET /api/v1/company/dumpsters` - List dumpsters
- `GET /api/v1/company/dumpsters/{id}` - Get specific dumpster
- `POST /api/v1/company/dumpsters` - Create dumpster
- `PUT /api/v1/company/dumpsters/{id}` - Update dumpster
- `DELETE /api/v1/company/dumpsters/{id}` - Delete dumpster

#### Customer Management
- `GET /api/v1/company/customers` - List customers
- `GET /api/v1/company/customers/{id}` - Get specific customer
- `POST /api/v1/company/customers` - Create customer
- `PUT /api/v1/company/customers/{id}` - Update customer
- `DELETE /api/v1/company/customers/{id}` - Delete customer

#### Driver Management
- `GET /api/v1/company/drivers` - List drivers
- `GET /api/v1/company/drivers/{id}` - Get specific driver
- `POST /api/v1/company/drivers` - Create driver
- `PUT /api/v1/company/drivers/{id}` - Update driver
- `DELETE /api/v1/company/drivers/{id}` - Delete driver
- `PUT /api/v1/company/drivers/{id}/terminate` - Terminate driver

#### Promotion Management
- `GET /api/v1/company/promotions` - List promotions
- `GET /api/v1/company/promotions/{id}` - Get specific promotion
- `POST /api/v1/company/promotions` - Create promotion
- `PUT /api/v1/company/promotions/{id}` - Update promotion
- `DELETE /api/v1/company/promotions/{id}` - Delete promotion

## 🔐 Authentication

The application uses JWT (JSON Web Tokens) for authentication across all user types:
- Companies
- Customers  
- Drivers

Each user type has separate authentication flows and permissions.

## 💳 Payment Integration

Integrated with Finix payment processing:
- Merchant onboarding
- Payment method management
- Transaction processing
- Webhook handling

## 📱 Mobile Support

The system is designed to support mobile applications with:
- Device token management for push notifications
- Location tracking capabilities
- JWT-based authentication
- RESTful API design

## 🗄 Database Schema

The application uses a comprehensive database schema supporting:

### Core Tables
- **companies**: Multi-tenant companies with Finix integration
- **customers**: Customer information with polymorphic relationships
- **drivers**: Driver profiles with employment tracking
- **orders**: Order lifecycle management
- **dumpsters**: Physical dumpster inventory
- **warehouses**: Storage facility management
- **dumpster_sizes**: Configurable dumpster specifications

### Polymorphic Tables
- **addresses**: Address management for all entities
- **documents**: File and document storage
- **general_settings**: Configurable application settings
- **timings**: Scheduling for companies and warehouses
- **holidays**: Holiday management for companies and drivers
- **device_tokens**: Push notification device management
- **latest_locations**: Real-time location tracking
- **contacts**: Emergency contact management

### Relationship Tables
- **company_customer**: Many-to-many relationship with pivot data
- **company_driver**: Many-to-many relationship with employment details
- **dumpster_size_promotion**: Promotion-dumpster size relationships
- **dumpster_size_tax**: Tax-dumpster size relationships

### Order Management Tables
- **order_timings**: Order-specific timing information
- **order_pricings**: Pricing calculations
- **order_discounts**: Applied discounts
- **order_payments**: Payment tracking
- **order_addresses**: Delivery addresses

### Driver Management Tables
- **driver_attendances**: Attendance tracking
- **driver_breaks**: Break time management
- **driver_over_times**: Overtime tracking
- **driver_time_schedules**: Scheduling system

### Supporting Tables
- **payment_methods**: Available payment methods
- **payment_options**: Payment terms and conditions
- **promotions**: Marketing promotions
- **taxes**: Tax configuration
- **waste_types**: Waste classification
- **merchant_onboarding_logs**: Finix onboarding tracking
- **password_reset_tokens**: OTP and password reset
- **secure_files**: Secure file storage
- **notifications**: System notifications

### Key Features
- Multi-tenancy (company-based data isolation)
- Polymorphic relationships for addresses, documents, and settings
- Audit trails and soft deletes
- Flexible timing and scheduling
- Complex order management with pricing and payments
- Real-time location tracking
- Device token management for push notifications

## 🎉 Holiday Management System

The application features a sophisticated holiday management system that supports both company-wide holidays and individual driver leave requests through a polymorphic relationship structure.

### Holiday Types & Usage

#### ✅ When to use what

**One-off holiday** → Store in `date` field (e.g., `2025-03-20`)
- Use for specific dates that don't repeat
- Examples: Company retreat, special events, one-time closures

**Weekly holiday** → Store in `day_of_week` field (e.g., `5 = Friday`)
- Use for recurring weekly holidays
- Day values: `0=Sunday, 1=Monday, 2=Tuesday, 3=Wednesday, 4=Thursday, 5=Friday, 6=Saturday`
- Examples: Weekly maintenance day, recurring team meetings

**Yearly holiday** → Store in `month_day` field (e.g., `03-23` for Pakistan Day, `08-14` for Independence Day)
- Use for holidays that repeat annually on the same date
- Format: `MM-DD` (e.g., `12-25` for Christmas, `01-01` for New Year)
- Examples: National holidays, company anniversaries, religious observances

### Database Structure

```sql
holidays table:
├── id (primary key)
├── holidayable_id (polymorphic - company_id or driver_id)
├── holidayable_type (polymorphic - "App\Models\Company" or "App\Models\Driver")
├── name (holiday name)
├── date (one-off holiday date)
├── recurrence_type (none, weekly, yearly)
├── day_of_week (0-6 for weekly holidays)
├── month_day (MM-DD format for yearly holidays)
├── reason (description/justification)
├── is_approved (pending, approved, rejected)
├── is_active (boolean)
└── timestamps
```

### Polymorphic Relationships

The holiday system uses polymorphic relationships to support multiple entity types:

- **Companies**: Can create company-wide holidays (auto-approved)
- **Drivers**: Can request personal leave (requires approval)

### API Endpoints

#### Company Holidays
- `GET /api/v1/company/holidays` - List company holidays
- `GET /api/v1/company/holidays/{id}` - Get specific holiday
- `POST /api/v1/company/holidays` - Create new holiday
- `PUT /api/v1/company/holidays/{id}` - Update holiday
- `DELETE /api/v1/company/holidays/{id}` - Delete holiday
- `GET /api/v1/company/days-of-week-options` - Get day options for weekly holidays

#### Query Parameters
- `filters[name]` - Filter by holiday name
- `filters[date]` - Filter by specific date
- `filters[from_date]` - Filter from date range
- `filters[to_date]` - Filter to date range
- `filters[recurrence_type]` - Filter by recurrence type
- `filters[day_of_week]` - Filter by day of week
- `filters[month_day]` - Filter by month-day
- `filters[is_active]` - Filter by active status
- `filters[is_approved]` - Filter by approval status

### Validation Rules

#### Required Fields
- `name` - Holiday name (string, max 255)
- `date` - Holiday date (date format)
- `recurrence_type` - Must be: `none`, `weekly`, or `yearly`

#### Conditional Validation
- **Weekly holidays**: `day_of_week` required (0-6)
- **Yearly holidays**: `month_day` required (MM-DD format)
- **Date consistency**: If both `date` and `day_of_week` provided, they must match

### Approval Workflow

#### Company Holidays
- Automatically approved (`is_approved = 'approved'`)
- Immediately active (`is_active = true`)

#### Driver Leave Requests
- Initially pending (`is_approved = 'pending'`)
- Requires manual approval by company admin
- Can be approved or rejected

### Examples

#### Creating a One-off Holiday
```json
{
  "name": "Company Retreat",
  "date": "2025-03-20",
  "recurrence_type": "none",
  "reason": "Annual company team building event"
}
```

#### Creating a Weekly Holiday
```json
{
  "name": "Maintenance Day",
  "date": "2025-01-03",
  "recurrence_type": "weekly",
  "day_of_week": 5,
  "reason": "Weekly equipment maintenance"
}
```

#### Creating a Yearly Holiday
```json
{
  "name": "Independence Day",
  "date": "2025-08-14",
  "recurrence_type": "yearly",
  "month_day": "08-14",
  "reason": "National holiday"
}
```

### Business Logic

- **Duplicate Prevention**: System prevents creating duplicate holidays for the same date
- **Automatic Approval**: Company holidays are auto-approved, driver requests require approval
- **Flexible Filtering**: Advanced filtering capabilities for holiday management
- **Polymorphic Design**: Single table supports multiple entity types efficiently

## 🏗 Architecture & Design Patterns

### Design Patterns Used
- **Repository Pattern**: Data access abstraction in `app/Repositories/`
- **Service Layer**: Business logic in `app/Services/`
- **Resource Pattern**: API response formatting in `app/Http/Resources/`
- **Request Validation**: Form request validation in `app/Http/Requests/`
- **Trait Pattern**: Reusable functionality in `app/Traits/`
- **Enum Pattern**: Type-safe constants in `app/Enums/`

### Key Traits
- **HasAddresses**: Polymorphic address management
- **HasDocuments**: File and document management
- **HasEmergencyContacts**: Emergency contact management
- **HasDeviceTokens**: Push notification device management

### Enums
- **DiscountTypeEnum**: Percentage and fixed discount types
- **DumpsterStatusEnum**: Available, rented, maintenance, inactive
- **EmploymentTypeEnum**: Full-time, part-time, contract
- **GenderEnum**: Male, female, other
- **HolidayApprovalStatusEnum**: Pending, approved, rejected
- **FinixOnboardingStatusEnum**: Onboarding status tracking
- **EmergencyContactTypeEnum**: Contact type classification
- **NotificationEnum**: Notification types
- **PaymentOptionTypeEnum**: Payment option types
- **RecurrenceTypeEnum**: Holiday recurrence types
- **TaxEnums**: Tax calculation types

### Services
- **CompanyAuthenticationService**: Company registration, OTP, password management
- **DeviceService**: Device token registration and management
- **FinixService**: Payment gateway integration

## 🚀 Deployment

### Production Considerations

1. **Environment Configuration**
   - Set `APP_ENV=production`
   - Configure production database
   - Set up SSL certificates

2. **Performance Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

3. **Queue Workers**
   - Set up supervisor for queue workers
   - Configure Redis for job processing

4. **File Storage**
   - Configure AWS S3 for production file storage
   - Set up CDN if needed

5. **Payment Integration**
   - Configure Finix production credentials
   - Set up webhook endpoints
   - Test payment flows

6. **Monitoring**
   - Laravel Telescope for debugging
   - Log monitoring and alerting
   - Performance monitoring

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the API documentation
- Review the Laravel documentation

## 📊 Project Statistics

### Codebase Overview
- **Total Models**: 30+ Eloquent models
- **API Controllers**: 15+ RESTful controllers
- **Database Tables**: 40+ tables with relationships
- **API Endpoints**: 50+ endpoints
- **Enums**: 10+ type-safe enums
- **Traits**: 5+ reusable traits
- **Services**: 3+ business logic services
- **Migrations**: 40+ database migrations

### Key Features Implemented
- ✅ Multi-tenant company management
- ✅ JWT authentication system
- ✅ File upload with S3 integration
- ✅ Payment gateway integration (Finix)
- ✅ Real-time location tracking
- ✅ Push notification system
- ✅ Advanced holiday management
- ✅ Order lifecycle management
- ✅ Driver and customer management
- ✅ Warehouse and dumpster management
- ✅ Tax and pricing system
- ✅ Promotion and discount system
- ✅ Document management
- ✅ Address management
- ✅ Emergency contact system
- ✅ Device token management
- ✅ API documentation with Swagger

## 🔄 Version History

- **v2.0** - Current version with multi-tenant architecture
  - Complete API implementation
  - Finix payment integration
  - Advanced holiday management
  - Real-time features
  - Comprehensive documentation
- **v1.0** - Initial release

---

Built with ❤️ using Laravel