# GenZ Wearables - E-commerce Admin Panel

Complete admin panel for the GenZ Wearables e-commerce platform built with Laravel 11 and Tailwind CSS.

## 🚀 Features

### ✅ Complete Admin Dashboard
- Statistics overview (users, products, orders, revenue)
- Recent orders display
- Quick access to all modules

### 📦 Product Management
- Full CRUD operations for products
- Category assignment
- Multiple product images upload
- Product variants (Size × Color combinations)
- SKU management
- Stock tracking
- Brand and gender filtering

### 📂 Category Management
- Create, edit, delete categories
- Parent-child category relationships
- Category status management

### 🛒 Order Management
- View all orders with filters
- Order status tracking (placed, packed, shipped, delivered, cancelled, returned)
- Payment status management
- Shipment tracking with courier details
- Complete order details view
- Customer information display

### 👥 Customer Management
- View all customers
- Customer details with order history
- Address management
- Customer status control (active/inactive)
- Purchase statistics

### 🎟️ Coupon Management
- Create discount coupons
- Flat or percentage-based discounts
- Minimum order amount settings
- Expiry date management
- Coupon status control

### 🎨 Attributes Management
- **Sizes**: Add/remove product sizes (S, M, L, XL, etc.)
- **Colors**: Add/remove colors with hex codes

### ⭐ Reviews Management
- View all product reviews
- Rating display
- Delete inappropriate reviews

## 📋 Database Schema

Complete database with 20+ tables including:
- Users & Addresses
- Categories (with parent-child support)
- Products, Images, Variants
- Sizes & Colors
- Cart & Wishlist
- Orders, Order Items, Payments
- Shipments & Returns
- Coupons
- Reviews
- Settings & Pages

## 🔧 Installation & Setup

### 1. Run Database Migrations
```bash
php artisan migrate
```

### 2. Create Admin User
```bash
php artisan db:seed --class=AdminUserSeeder
```

**Admin Credentials:**
- Email: `admin@genzwearables.com`
- Password: `password`

### 3. Create Storage Link (for product images)
```bash
php artisan storage:link
```

### 4. Start Development Server
```bash
php artisan serve
```

### 5. Access Admin Panel
Navigate to: `http://localhost:8000/login`

Login with admin credentials and you'll be redirected to `/admin`

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ProductController.php
│   │   │   ├── OrderController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── CouponController.php
│   │   │   ├── AttributeController.php
│   │   │   └── ReviewController.php
│   │   └── Auth/
│   │       └── LoginController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Product.php
│   ├── ProductImage.php
│   ├── ProductVariant.php
│   ├── Size.php
│   ├── Color.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Payment.php
│   ├── Shipment.php
│   ├── UserAddress.php
│   ├── Coupon.php
│   └── Review.php

resources/
└── views/
    ├── admin/
    │   ├── layout.blade.php (main layout)
    │   ├── dashboard.blade.php
    │   ├── categories/ (index, create, edit)
    │   ├── products/ (index, create, edit)
    │   ├── orders/ (index, show)
    │   ├── customers/ (index, show)
    │   ├── coupons/ (index, create, edit)
    │   ├── attributes/ (sizes, colors)
    │   └── reviews/ (index)
    └── auth/
        └── login.blade.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_categories_table.php
│   ├── 2024_01_01_000002_create_sizes_table.php
│   ├── 2024_01_01_000003_create_colors_table.php
│   ├── 2024_01_02_000001_create_user_addresses_table.php
│   ├── 2024_01_03_000001_create_products_table.php
│   ├── 2024_01_03_000002_create_product_images_table.php
│   ├── 2024_01_03_000003_create_product_variants_table.php
│   ├── 2024_01_04_000001_create_carts_table.php
│   ├── 2024_01_04_000002_create_cart_items_table.php
│   ├── 2024_01_04_000003_create_wishlists_table.php
│   ├── 2024_01_05_000001_create_orders_table.php
│   ├── 2024_01_05_000002_create_order_items_table.php
│   ├── 2024_01_05_000003_create_payments_table.php
│   ├── 2024_01_05_000004_create_shipments_table.php
│   ├── 2024_01_06_000001_create_returns_table.php
│   ├── 2024_01_06_000002_create_coupons_table.php
│   ├── 2024_06_000003_create_reviews_table.php
│   ├── 2024_01_06_000004_create_pages_table.php
│   └── 2024_01_06_000005_create_settings_table.php
└── seeders/
    └── AdminUserSeeder.php
```

## 🎨 UI Features

- **Responsive Design**: Works on desktop, tablet, and mobile
- **Tailwind CSS**: Modern, clean design
- **Font Awesome Icons**: Beautiful iconography
- **Color-coded Status**: Visual feedback for order/payment status
- **Interactive Tables**: Sort, filter, and pagination
- **Form Validation**: Client and server-side validation
- **Success/Error Messages**: User feedback for all actions

## 🔐 Security Features

- Admin middleware protection
- Password hashing
- CSRF protection
- Session management
- Role-based access control
- XSS protection

## 📊 Admin Panel Routes

### Dashboard
- `GET /admin` - Dashboard with statistics

### Categories
- `GET /admin/categories` - List all categories
- `GET /admin/categories/create` - Create category form
- `POST /admin/categories` - Store new category
- `GET /admin/categories/{id}/edit` - Edit category form
- `PUT /admin/categories/{id}` - Update category
- `DELETE /admin/categories/{id}` - Delete category

### Products
- `GET /admin/products` - List all products
- `GET /admin/products/create` - Create product form
- `POST /admin/products` - Store new product
- `GET /admin/products/{id}/edit` - Edit product (with images & variants)
- `PUT /admin/products/{id}` - Update product
- `DELETE /admin/products/{id}` - Delete product
- `POST /admin/products/{id}/images` - Upload product image
- `DELETE /admin/product-images/{id}` - Delete product image
- `POST /admin/products/{id}/variants` - Add product variant
- `DELETE /admin/product-variants/{id}` - Delete product variant

### Orders
- `GET /admin/orders` - List all orders (with filters)
- `GET /admin/orders/{id}` - View order details
- `PATCH /admin/orders/{id}/status` - Update order status
- `PATCH /admin/orders/{id}/payment-status` - Update payment status
- `POST /admin/orders/{id}/shipment` - Add/update shipment details

### Customers
- `GET /admin/customers` - List all customers
- `GET /admin/customers/{id}` - View customer details
- `PATCH /admin/customers/{id}/status` - Update customer status

### Coupons
- `GET /admin/coupons` - List all coupons
- `GET /admin/coupons/create` - Create coupon form
- `POST /admin/coupons` - Store new coupon
- `GET /admin/coupons/{id}/edit` - Edit coupon form
- `PUT /admin/coupons/{id}` - Update coupon
- `DELETE /admin/coupons/{id}` - Delete coupon

### Attributes
- `GET /admin/sizes` - Manage sizes
- `POST /admin/sizes` - Add new size
- `DELETE /admin/sizes/{id}` - Delete size
- `GET /admin/colors` - Manage colors
- `POST /admin/colors` - Add new color
- `DELETE /admin/colors/{id}` - Delete color

### Reviews
- `GET /admin/reviews` - List all reviews
- `DELETE /admin/reviews/{id}` - Delete review

## 🌐 Website URL
Production: https://genzwearables.com/

## 💡 Usage Tips

1. **First Time Setup**: Run the seeder to create admin user
2. **Add Attributes First**: Add sizes and colors before creating product variants
3. **Product Images**: Upload images after creating the product
4. **Product Variants**: Create variants (size/color combinations) for inventory management
5. **Order Management**: Update order status as orders progress
6. **Shipment Tracking**: Add courier and tracking details when shipping orders

## 🔄 Order Status Flow
1. **Placed** → Order received
2. **Packed** → Order is being prepared
3. **Shipped** → Order dispatched (add shipment details)
4. **Delivered** → Order delivered to customer
5. **Cancelled** → Order cancelled
6. **Returned** → Order returned by customer

## 📱 Technology Stack

- **Framework**: Laravel 11
- **Frontend**: Blade Templates + Tailwind CSS
- **Icons**: Font Awesome 6
- **Database**: MySQL (via migrations)
- **Authentication**: Laravel built-in
- **File Storage**: Laravel Storage (public disk)

## 🎯 Next Steps (Optional Enhancements)

- [ ] Add frontend customer-facing website
- [ ] Implement API for mobile app
- [ ] Add bulk import/export functionality
- [ ] Email notifications for orders
- [ ] Advanced reporting and analytics
- [ ] Multi-currency support
- [ ] Payment gateway integration
- [ ] Real-time order tracking
- [ ] Inventory alerts
- [ ] Customer wishlist functionality

## 📝 Notes

- All passwords are hashed using Laravel's default bcrypt
- Product images are stored in `storage/app/public/products`
- Make sure to run `php artisan storage:link` for image uploads to work
- The admin panel is fully responsive and works on all devices
- All forms include CSRF protection
- Input validation is implemented on both client and server side

## 🤝 Support

For issues or questions about the admin panel, please check the code comments or Laravel documentation.

---

**Happy Managing! 🎉**
