# WordPress Simple Redirect

A simple and powerful WordPress plugin that allows you to create custom slugs with URL redirection functionality. Perfect for managing short URLs, affiliate links, and custom redirections on your WordPress or WordPress Multisite installation.

## 🚀 Features

- **Custom Slug Creation**: Create custom slugs that redirect to any target URL
- **Nested Path Support**: Supports forward slashes (/) in slugs for hierarchical paths
- **WordPress & Multisite Compatible**: Works seamlessly with both single WordPress sites and WordPress Multisite networks
- **Redirect Tracking**: Monitor redirect statistics with count and last accessed timestamps
- **Status Management**: Enable/disable redirects without deleting them
- **Test Functionality**: Test redirects without affecting statistics
- **AJAX-Powered Interface**: Modern, responsive admin interface with real-time updates
- **Clean Uninstall**: Removes all plugin data cleanly when uninstalled

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## 🛠 Installation

### Method 1: Manual Installation

1. Download the plugin files
2. Upload the `wordpress-redirection` folder to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Simple Redirect** in your WordPress admin menu

### Method 2: WordPress Admin

1. Go to **Plugins → Add New** in your WordPress admin
2. Upload the plugin zip file
3. Click **Install Now** and then **Activate**

## 📖 Usage

### Creating a New Redirect

1. Go to **Simple Redirect** in your WordPress admin menu
2. Fill in the form at the top of the page:
   - **Slug**: Enter your custom slug (e.g., `my-link` or `category/product`)
   - **Target URL**: Enter the destination URL (e.g., `https://example.com`)
3. Click **Create Redirect**

### Managing Existing Redirects

The plugin provides a comprehensive table showing all your redirects with the following information:

- **Slug**: The custom slug you created
- **Target URL**: The destination URL
- **Count**: Number of times the redirect has been accessed
- **Last Called**: When the redirect was last used
- **Status**: Whether the redirect is active or inactive
- **Actions**: Available operations for each redirect

### Available Actions

- **Edit**: Modify the slug or target URL
- **Delete**: Permanently remove the redirect
- **Enable/Disable**: Toggle redirect status without deleting
- **Test**: Open the redirect in a new tab without counting the access

## 🔧 Technical Details

### Database Structure

The plugin creates a `wp_simple_redirects` table with the following structure:

```sql
CREATE TABLE wp_simple_redirects (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    slug varchar(255) NOT NULL,
    target_url text NOT NULL,
    redirect_count int(11) DEFAULT 0,
    last_called datetime NULL,
    status enum('active','inactive') DEFAULT 'active',
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY slug (slug)
);
```

### How It Works

1. **URL Rewriting**: The plugin adds a rewrite rule to catch custom slugs
2. **Request Processing**: Incoming requests are processed through the `parse_request` action
3. **Database Lookup**: The slug is matched against stored redirects
4. **Redirection**: If found, the user is redirected with a 301 status code
5. **Statistics**: Access count and timestamp are updated automatically

### Multisite Support

- Creates separate tables for each site in a multisite network
- Handles activation/deactivation across all sites
- Clean uninstall removes data from all sites

## 🎨 Admin Interface

The plugin provides a clean, intuitive admin interface featuring:

- **Responsive Design**: Works on desktop and mobile devices
- **AJAX Operations**: All actions happen without page reloads
- **WordPress UI Standards**: Follows WordPress design guidelines
- **Real-time Feedback**: Instant success/error messages

## 🔒 Security Features

- **Capability Checks**: Only users with `manage_options` capability can manage redirects
- **CSRF Protection**: All AJAX requests are protected with WordPress nonces
- **Input Sanitization**: All user inputs are properly sanitized and validated
- **SQL Injection Prevention**: Uses WordPress prepared statements

## 📁 File Structure

```
wordpress-redirection/
├── wordpress-simple-redirect.php    # Main plugin file
├── assets/
│   ├── admin.css                   # Admin interface styles
│   └── admin.js                    # Admin interface JavaScript
├── templates/
│   └── admin-page.php              # Admin page template
├── docs/
│   └── requirements.md             # Original requirements
└── README.md                       # This file
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 Changelog

### Version 1.0.0
- Initial release
- Custom slug creation with redirection
- WordPress and Multisite support
- Admin interface with AJAX functionality
- Redirect tracking and statistics
- Enable/disable functionality
- Test redirect feature

## 👥 Authors

- **Atipat Lorwongam** - *Initial work*
- **Claude Sonnet 4** - *AI Assistant*

## 📄 License

This project is licensed under the GPL v2 or later - see the [WordPress Plugin License](https://wordpress.org/about/license/) for details.

## 🐛 Support

If you encounter any issues or have questions:

1. Check the [GitHub Issues](https://github.com/luozongbao/wordpress-redirection/issues)
2. Create a new issue if your problem isn't already reported
3. Provide detailed information about your WordPress version, plugin version, and the issue

## 🌟 Features Coming Soon

- Import/Export functionality
- Bulk operations
- Advanced statistics and analytics
- Custom redirect codes (302, 307, etc.)
- Redirect expiration dates
- Category/tag organization

---

**Made with ❤️ for the WordPress community**