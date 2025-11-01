# Marjan Investment Admin Panel

## 🚀 **Quick Start Guide**

### **1. Access the Admin Panel**
- **URL**: `http://localhost/Vtuber_project/partnership/project/admin_login.php`
- **Username**: `admin`
- **Password**: `marjan2024`

### **2. Files Created**
- `admin_login.php` - Beautiful login page with modern design
- `admin_panel.php` - Main admin dashboard with applications management
- `admin_profile.php` - Admin profile and settings page
- `admin_reports.php` - Reports and analytics page
- `admin_settings.php` - System configuration page
- `admin_logs.php` - Activity logs and monitoring page
- `README_ADMIN.md` - This documentation file

## ✨ **Features**

### **Dashboard Overview**
- **Statistics Cards**: Total, Pending, Approved, and Today's applications
- **Real-time Data**: Live counts from your database
- **Responsive Design**: Works on all devices

### **Applications Management**
- **View All Applications**: See all partnership submissions
- **Search & Filter**: Find applications by name, email, phone, or status
- **Status Updates**: Change application status (Pending → Reviewed → Approved/Rejected)
- **Delete Applications**: Remove unwanted submissions
- **Detailed Information**: View all form data including reasons

### **Profile Section**
- **Admin Profile**: View administrator information and statistics
- **Activity Summary**: See total applications handled, approved, and rejected
- **Performance Metrics**: Track your admin activity
- **Profile Settings**: Update admin name and email address
- **Password Management**: Change admin password securely
- **Account Preferences**: Toggle various account settings

### **Reports & Analytics**
- **Data Visualization**: Interactive charts showing application distribution
- **Trend Analysis**: Monthly application trends and patterns
- **Export Options**: Generate reports in PDF, Excel, or CSV formats
- **Custom Filters**: Filter data by date range and status

### **System Settings**
- **Notification Settings**: Configure email, SMS, and dashboard alerts
- **Security Settings**: Enable 2FA, session timeout, and IP restrictions
- **Appearance Settings**: Toggle dark mode and compact view options

### **Activity Logs**
- **Action Tracking**: Monitor all admin activities and system changes
- **Filterable Logs**: Filter by date range and action type
- **Export Functionality**: Download activity logs for audit purposes
- **Real-time Monitoring**: Track login attempts and status changes

### **Security Features**
- **Session Management**: Secure login/logout system
- **SQL Injection Protection**: Prepared statements for all database queries
- **XSS Protection**: HTML escaping for all user input
- **Access Control**: Only logged-in admins can access the panel

## 🔧 **How It Works**

### **1. Form Submission Flow**
```
User fills form → submit_form.php → Database → Admin Panel
```

### **2. Admin Workflow**
```
Login → View Applications → Update Status → Manage Database
```

### **3. Database Integration**
- Connects to `marjanformul` database
- Table: `partnership_applications`
- Fields: name, email, phone, budget, source, reason, status, submission_date

## 📱 **Responsive Design**

- **Desktop**: Full sidebar navigation
- **Tablet**: Collapsible sidebar
- **Mobile**: Mobile-optimized layout with hamburger menu

## 🎨 **Design Features**

- **Modern UI**: Clean, professional interface
- **Marjan Branding**: Gold accents and crown logo
- **Dark Theme**: Easy on the eyes
- **Smooth Animations**: Hover effects and transitions
- **Icon Integration**: Font Awesome icons throughout

## 🚨 **Important Notes**

### **Database Requirements**
Make sure your database has the `partnership_applications` table with these fields:
- `id` (auto-increment)
- `name` (varchar)
- `email` (varchar)
- `phone` (varchar)
- `budget` (varchar)
- `source` (varchar)
- `reason` (text)
- `status` (varchar, default: 'pending')
- `submission_date` (timestamp, default: current timestamp)

### **Security**
- Change default admin credentials in production
- Use HTTPS in production
- Regular security updates

## 🔄 **Status Management**

### **Application Statuses**
1. **Pending** - New submission (default)
2. **Reviewed** - Admin has reviewed
3. **Approved** - Application accepted
4. **Rejected** - Application denied

### **Status Update Process**
1. Select new status from dropdown
2. Click "Update Status" button
3. Status is immediately updated in database
4. Success message appears

## 📊 **Statistics Dashboard**

### **Real-time Metrics**
- **Total Applications**: All submissions
- **Pending Reviews**: Applications awaiting review
- **Approved**: Successfully approved applications
- **Today's Applications**: Submissions from current day

## 🛠️ **Customization**

### **Colors**
Edit CSS variables in `admin_panel.php`:
```css
:root {
    --primary: #D4AF37;        /* Gold */
    --background: #0f172a;     /* Dark blue */
    --surface: #1e293b;        /* Lighter blue */
    --success: #22c55e;        /* Green */
    --warning: #f59e0b;        /* Orange */
    --error: #ef4444;          /* Red */
}
```

### **Logo & Branding**
- Uses plane icon instead of crown
- "Admin" branding instead of "Marjan"
- Modern blue and green color scheme

## 🚀 **Deployment**

### **Local Development (XAMPP)**
1. Place files in `htdocs/Vtuber_project/partnership/project/`
2. Start Apache and MySQL services
3. Access via `http://localhost/Vtuber_project/partnership/project/admin_login.php`

### **Production Server**
1. Upload files to your web server
2. Update database credentials
3. Ensure HTTPS is enabled
4. Change default admin password

## 📞 **Support**

If you need help:
1. Check database connection settings
2. Verify file permissions
3. Check browser console for errors
4. Ensure PHP and MySQL are running

## 🔐 **Default Credentials**

- **Username**: `admin`
- **Password**: `marjan2024`

**⚠️ IMPORTANT**: Change these credentials before going live!

---

**Created with ❤️ for Marjan Investment**
