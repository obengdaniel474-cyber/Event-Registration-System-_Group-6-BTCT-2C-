# EventReg Ghana - Event Registration Platform

A modern event registration and management platform built with PHP, featuring user authentication, event browsing, and registration management.

## 📋 Table of Contents
- [EventReg Ghana - Event Registration Platform](#eventreg-ghana---event-registration-platform)
  - [📋 Table of Contents](#-table-of-contents)
  - [👥 Group Members](#-group-members)
  - [📋 System Overview](#-system-overview)
  - [🏗️ Architecture](#️-architecture)
    - [File Structure](#file-structure)
  - [🔧 Core Components](#-core-components)
    - [1. Authentication System (`login.php`, `register.php`)](#1-authentication-system-loginphp-registerphp)
    - [2. User Dashboard (`dashboard.php`)](#2-user-dashboard-dashboardphp)
    - [3. Event Management (`events.json`)](#3-event-management-eventsjson)
    - [4. Registration Flow (`registerRedirect.php`)](#4-registration-flow-registerredirectphp)
  - [💾 Data Structures](#-data-structures)
    - [Standard User Format (`users.json`)](#standard-user-format-usersjson)
    - [Event Format (`events.json`)](#event-format-eventsjson)
    - [Session Structure](#session-structure)
  - [🔐 Security Features](#-security-features)
  - [🎨 Frontend Stack](#-frontend-stack)
  - [🚀 Setup Instructions](#-setup-instructions)
  - [📱 Features](#-features)
    - [For Users](#for-users)
    - [For Admins](#for-admins)
  - [🔄 Session Flow](#-session-flow)
  - [⚠️ Challenges Faced \& Solutions](#️-challenges-faced--solutions)
  - [🎯 Future Enhancements](#-future-enhancements)
  - [👥 User Roles](#-user-roles)
  - [📝 Version History](#-version-history)
  - [📄 License](#-license)

---

## 👥 Group Members

| Name | Index Number| 
|------|------|
**Daniel Obeng** | 052441360054 |
| **Asare Overmass** | 052441360305 |
| **Kamal-deen Ahmed** | 052441360237 |
| **Kyei Osei Agyemang** | 052541360384 | 
| **Asola Edmond Akelisiba** | 052341360112 | 
| **David Peprah** | 052341360112 | 
| **Philip Essuman** | 052341360146 |

## 📋 System Overview

EventReg Ghana is a web-based platform that allows users to discover, register for, and manage events in Ghana. The platform includes user authentication, profile management, and an intuitive dashboard for tracking registered events.

## 🏗️ Architecture

### File Structure

```
project/
├── index.html          # Landing page with event listings
├── dashboard.php       # User dashboard for managing registrations
├── login.php          # User authentication
├── register.php       # New user registration
├── logout.php         # Session termination
├── registerRedirect.php # Event registration handler
├── styles.css         # Tailwind CSS styles
├── events.json        # Event data storage
├── users.json         # User accounts storage
└── users.txt          # Legacy user data (inconsistent format)
```

## 🔧 Core Components

### 1. Authentication System (`login.php`, `register.php`)
- Session-based authentication
- Password hashing using `password_hash()`/`password_verify()`
- Admin demo credentials: `admin@eventreg.gh` / `admin2026`
- Automatic redirect for authenticated users
- Email validation and duplicate prevention

### 2. User Dashboard (`dashboard.php`)
- Profile picture upload functionality
- View registered events
- Cancel event registrations
- Responsive Tailwind CSS interface
- Session-based registration tracking

### 3. Event Management (`events.json`)
- Structured event data storage
- Events include: id, title, description, date, location, image, capacity
- Sample events: Tech Summit, Cultural Festival, Startup Bootcamp

### 4. Registration Flow (`registerRedirect.php`)
- Handles event registration redirects
- Stores pending registrations for non-authenticated users
- Updates session-based registration tracking

## 💾 Data Structures

### Standard User Format (`users.json`)
```json
{
    "id": 1772588258,
    "name": "Asare Overmass",
    "email": "user@example.com",
    "password": "$2y$10$...",
    "role": "member",
    "profile_pic": "https://ui-avatars.com/api/?name=...",
    "registered_events": []
}
```

### Event Format (`events.json`)
```json
{
    "id": 1,
    "title": "Kumasi Tech Summit 2026",
    "description": "Event description...",
    "date": "2026-04-10",
    "location": "Venue name",
    "image": "https://example.com/image.jpg",
    "capacity": 500
}
```

### Session Structure
```php
$_SESSION['user'] = [/* user data */];
$_SESSION['registered_events'] = [/* event IDs */];
$_SESSION['pending_event'] = /* event ID for redirect */
```

## 🔐 Security Features

- **Password Security**: Passwords hashed using bcrypt
- **Session Management**: Proper session start/destroy
- **Input Validation**: Email validation, file type checking
- **XSS Prevention**: `htmlspecialchars()` for output escaping
- **File Upload Security**: Allowed extensions limited to images

## 🎨 Frontend Stack

- **Tailwind CSS**: Utility-first CSS framework
- **Font Awesome**: Icons for UI elements
- **Google Fonts**: Inter font family
- **Responsive Design**: Mobile-friendly layouts
- **Modern UI**: Glass morphism effects, gradients, animations

## 🚀 Setup Instructions

1. **Requirements**: PHP 7.4+, web server (Apache/Nginx)
2. **Permissions**: Ensure write permissions for:
   - `users.json`
   - `profiles/` directory (created automatically)
3. **Configuration**: No additional setup required

## 📱 Features

### For Users
- Browse upcoming events
- Register for events
- Upload profile pictures
- View and manage registrations
- Cancel event registrations

### For Admins
- Demo admin access included
- Can manage events (via file editing)

## 🔄 Session Flow

1. **Unauthenticated User**: 
   - Can browse events
   - Clicking "Register" redirects to login
   - Event ID stored in `pending_event` session variable

2. **Authenticated User**:
   - Direct event registration
   - Dashboard access
   - Profile management

3. **Registration Process**:
   - Event ID added to `registered_events` session array
   - Displayed in user dashboard
   - Can be cancelled

## ⚠️ Challenges Faced & Solutions

### 1. **Data Inconsistency**
**Challenge**: The `users.txt` file contained legacy data with two different formats, making it difficult to maintain consistent user records.
**Solution**: Standardized all user data into `users.json` with a uniform format including id, name, email, password, role, profile_pic, and registered_events fields.

### 2. **Session-Based Registration Tracking**
**Challenge**: Initially, registered events were only stored in the session, causing data loss when users logged out.
**Solution**: Modified the data structure to store `registered_events` array within each user's record in `users.json`, ensuring persistence across sessions.

### 3. **File Upload Security**
**Challenge**: Profile picture uploads posed security risks including malicious file execution.
**Solution**: Implemented strict file type validation, unique filename generation, and dedicated upload directory with proper permissions.

### 4. **Redirect Logic for Unauthenticated Users**
**Challenge**: Users who tried to register for events while logged out lost their intended registration after logging in.
**Solution**: Implemented `pending_event` session variable to store the event ID and redirect users back to registration after successful login.

### 5. **Password Hashing Compatibility**
**Challenge**: Different PHP environments had varying password hashing algorithms, causing authentication failures.
**Solution**: Standardized on `PASSWORD_DEFAULT` and ensured consistent verification using `password_verify()` across all login attempts.

### 6. **JSON File Corruption**
**Challenge**: Concurrent write attempts to `users.json` sometimes led to file corruption.
**Solution**: Implemented file locking mechanisms and atomic write operations using temporary files.

### 7. **Responsive Design Complexities**
**Challenge**: Creating a seamless mobile experience with complex dashboard layouts.
**Solution**: Leveraged Tailwind CSS responsive utilities and thorough testing across multiple device sizes.

### 8. **Event Capacity Management**
**Challenge**: No enforcement of event capacity limits in the initial version.
**Solution**: Added capacity tracking in event objects and implemented checks before allowing new registrations.

### 9. **Profile Picture Display**
**Challenge**: Users without profile pictures showed broken image links.
**Solution**: Implemented fallback to UI Avatars API for automatic avatar generation based on user names.

### 10. **Cross-Browser Compatibility**
**Challenge**: Consistent styling across different browsers, especially for backdrop blur effects.
**Solution**: Used CSS features with broad support and implemented graceful degradation for older browsers.

## 🎯 Future Enhancements

1. **Database Integration**: Replace JSON files with MySQL for better scalability and data integrity
2. **Email Notifications**: Automated email confirmations for registrations and reminders
3. **Event Capacity Management**: Real-time capacity tracking with waitlist functionality
4. **Admin Panel**: Web-based interface for event CRUD operations
5. **Payment Integration**: Support for paid events with mobile money and card payments
6. **Social Login**: OAuth authentication with Google and Facebook
7. **Event Categories**: Filtering and searching events by type, location, and date
8. **User Reviews**: Allow attendees to rate and review events they've attended
9. **QR Code Check-in**: Generate QR codes for event tickets with scanning capability
10. **Analytics Dashboard**: Track registration trends and user engagement metrics

## 👥 User Roles

- **Member**: Regular users who can browse events, register, and manage their profile
- **Admin**: Special access with demo credentials for system management and oversight

## 📝 Version History

- **v1.0.0** (2026-02-27): Initial release with basic authentication and event browsing
- **v1.1.0** (2026-03-06): Added profile picture upload and dashboard enhancements
- **v1.2.0** (Planned): Database migration and email notifications