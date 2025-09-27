# Swagger Schema Documentation

This directory contains all Swagger/OpenAPI schema definitions for the Arcane Waste Management System API.

## 📁 Directory Structure

```
app/Swagger/
├── Company/
│   ├── Schemas/
│   │   ├── Resources/          # API response schemas
│   │   │   ├── HolidayResourceSchema.php
│   │   │   ├── DumpsterResourceSchema.php
│   │   │   ├── TaxResourceSchema.php
│   │   │   ├── PaymentOptionResourceSchema.php
│   │   │   └── GeneralSettingResourceSchema.php
│   │   ├── Requests/           # API request schemas
│   │   │   ├── HolidayCreateRequestSchema.php
│   │   │   └── HolidayUpdateRequestSchema.php
│   │   ├── Components/         # Reusable component schemas
│   │   │   ├── CompanyBasicSchema.php
│   │   │   ├── DumpsterSizeSchema.php
│   │   │   └── WarehouseSchema.php
│   │   └── index.php           # Central schema loader
│   └── Dumpster/               # Legacy dumpster schemas
│       ├── DumpsterTaxResourceSchema.php
│       ├── DumpsterDumpsterSizeResourceSchema.php
│       └── DumpsterWarehouseResourceSchema.php
└── README.md                   # This file
```

## 🎯 Naming Conventions

### Schema Names
- **Resource Schemas**: `{Module}{Entity}Resource` (e.g., `CompanyHolidayResource`)
- **Request Schemas**: `{Module}{Entity}{Action}Request` (e.g., `CompanyHolidayCreateRequest`)
- **Component Schemas**: `{Entity}` (e.g., `CompanyBasic`, `DumpsterSize`)

### File Names
- **Resource Files**: `{Entity}ResourceSchema.php`
- **Request Files**: `{Entity}{Action}RequestSchema.php`
- **Component Files**: `{Entity}Schema.php`

### Class Names
- Match the file name without the `.php` extension
- Use PascalCase for all class names

## 📋 Schema Categories

### 1. Resource Schemas (`Resources/`)
Define the structure of API responses. These schemas represent the data returned by endpoints.

**Examples:**
- `CompanyHolidayResource` - Holiday data in API responses
- `CompanyDumpsterResource` - Complete dumpster information
- `CompanyTaxResource` - Tax information for billing

### 2. Request Schemas (`Requests/`)
Define the structure of API request payloads. These schemas validate incoming data.

**Examples:**
- `CompanyHolidayCreateRequest` - Creating new holidays
- `CompanyHolidayUpdateRequest` - Updating existing holidays

### 3. Component Schemas (`Components/`)
Reusable schema components that can be referenced by other schemas using `$ref`.

**Examples:**
- `CompanyBasic` - Basic company information
- `DumpsterSize` - Dumpster size specifications
- `Warehouse` - Warehouse information

## 🔧 Usage

### Including Schemas
To include all schemas in your Swagger configuration:

```php
// In your main Swagger config file
require_once app_path('Swagger/Company/Schemas/index.php');
```

### Referencing Schemas
Reference other schemas using `$ref`:

```php
@OA\Property(
    property="company",
    ref="#/components/schemas/CompanyBasic"
)
```

### Schema Properties
Each property should include:
- `type` - Data type (string, integer, boolean, etc.)
- `example` - Example value
- `description` - Clear description of the property
- `format` - For special formats (date, date-time, email, etc.)
- `enum` - For restricted values
- `minimum/maximum` - For numeric constraints
- `pattern` - For string pattern validation

## 📝 Best Practices

### 1. Consistent Naming
- Use consistent prefixes for related schemas
- Follow the established naming patterns
- Avoid abbreviations unless they're widely understood

### 2. Comprehensive Documentation
- Include detailed descriptions for all properties
- Provide meaningful examples
- Document any business rules or constraints

### 3. Reusability
- Create component schemas for commonly used structures
- Reference components instead of duplicating definitions
- Keep schemas focused and single-purpose

### 4. Validation
- Include appropriate validation rules
- Use enums for restricted values
- Add format specifications for special data types

## 🚀 Migration from Legacy Structure

The old structure had several issues:
- Inconsistent naming (typos like "Dumpser" instead of "Dumpster")
- Mixed namespaces
- No clear organization
- Duplicate schema definitions

The new structure addresses these issues by:
- ✅ Consistent naming conventions
- ✅ Proper namespace organization
- ✅ Clear directory structure
- ✅ Centralized schema loading
- ✅ Reusable components

## 🔄 Future Enhancements

Consider adding:
- Customer module schemas
- Driver module schemas
- Order management schemas
- Payment processing schemas
- Notification schemas

## 📚 Related Documentation

- [Laravel Swagger Documentation](https://github.com/DarkaOnLine/L5-Swagger)
- [OpenAPI Specification](https://swagger.io/specification/)
- [API Documentation Best Practices](https://swagger.io/resources/articles/best-practices-in-api-documentation/)
