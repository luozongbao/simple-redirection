# Simple Redirection Plugin - Release Notes

## Version 1.0.0 - Initial Release
*Release Date: [Current Date]*

### 🎉 New Features

#### Core Functionality
- **Custom Slug Creation**: Create custom URL slugs that redirect to any target URL
- **Nested Path Support**: Full support for "/" in slugs (e.g., `products/special-offer`)
- **301 Redirects**: SEO-friendly permanent redirects
- **Real-time Redirect Tracking**: Track redirect count and last access time for each slug

#### Admin Interface
- **Intuitive Dashboard**: Clean, user-friendly admin interface under "Simple Redirect" menu
- **Quick Creation Form**: Simple form at the top of the page for creating new redirects
- **Comprehensive List View**: Table showing all redirects with key information
- **One-Click Copy**: Copy full redirect URLs to clipboard with a single click

#### Management Features
- **Edit Redirects**: Modify slug and target URL through modal dialog
- **Enable/Disable**: Temporarily disable redirects without deleting them
- **Test Functionality**: Test redirects in new tab without incrementing counter
- **Bulk Actions**: Delete unwanted redirects with confirmation

#### Technical Features
- **WordPress Integration**: Uses WordPress rewrite rules for optimal performance
- **Multisite Support**: Full compatibility with WordPress Multisite networks
- **Security**: CSRF protection, capability checks, and input sanitization
- **Clean Database**: Automatic table cleanup on plugin uninstall

### 📊 Data Tracking

Each redirect includes:
- **Slug**: The custom URL path
- **Target URL**: Destination URL for redirects
- **Count**: Number of times the redirect has been accessed
- **Last Called**: Date and time of most recent access
- **Status**: Active/Inactive status for easy management

### 🔧 Technical Specifications

- **WordPress Version**: 4.0 or higher
- **PHP Version**: 5.6 or higher
- **MySQL Version**: 5.0 or higher
- **Multisite**: Full support
- **Network Activation**: Supported

### 🛠️ Installation

1. Upload plugin files to `/wp-content/plugins/simple-redirection/`
2. Activate the plugin through the WordPress admin
3. Navigate to "Simple Redirect" in the admin menu
4. Start creating your custom redirects!

### 📁 File Structure

```
simple-redirection/
├── simple-redirection.php           # Main plugin file
├── templates/
│   └── admin-page.php               # Admin interface template
└── assets/
    ├── admin.css                    # Admin styling
    └── admin.js                     # Admin JavaScript
```

### 🔒 Security Features

- **CSRF Protection**: All AJAX requests protected with WordPress nonces
- **Capability Checks**: Requires `manage_options` capability
- **Input Sanitization**: All user inputs properly sanitized
- **SQL Injection Prevention**: Uses WordPress prepared statements

### 🌐 Multisite Compatibility

- Each site in a multisite network has its own redirect table
- Network activation supported
- Site-specific redirect management
- Clean uninstall across all sites

### 🚀 Performance

- **Lightweight**: Minimal impact on site performance
- **Efficient Redirects**: Uses WordPress rewrite rules
- **Database Optimized**: Indexed database table for fast lookups
- **Early Hook**: Processes redirects before template loading

### 📝 Usage Examples

**Simple Redirect:**
- Slug: `contact`
- Target: `https://example.com/contact-us`
- Result: `yoursite.com/contact` → `https://example.com/contact-us`

**Nested Path Redirect:**
- Slug: `products/sale`
- Target: `https://shop.example.com/sale-items`
- Result: `yoursite.com/products/sale` → `https://shop.example.com/sale-items`

### 🔄 Future Roadmap

- Import/Export functionality
- Bulk redirect management
- Advanced analytics
- Custom redirect codes (302, 307, etc.)
- Redirect expiration dates

### 👥 Credits

**Authors:** Atipat Lorwongam and Claude Sonnet 4  
**Repository:** https://github.com/luozongbao/simple-redirection/  
**License:** GPL v2 or later

### 📞 Support

For support, bug reports, or feature requests:
- Visit our [GitHub repository](https://github.com/luozongbao/simple-redirection/)
- Create an issue with detailed information
- Check existing issues for solutions

### 🎯 Getting Started

1. **Install & Activate** the plugin
2. **Go to Simple Redirect** in your WordPress admin
3. **Create your first redirect** using the form at the top
4. **Test the redirect** using the "Test" button
5. **Monitor usage** through the Count and Last Called columns

---

*Thank you for using Simple Redirection! We hope this plugin makes managing your redirects simple and efficient.*