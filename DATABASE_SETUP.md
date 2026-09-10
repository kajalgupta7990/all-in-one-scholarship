# Government Scholarship Portal - Phase 2 Database Setup & Integration Guide

This guide provides step-by-step instructions to configure, run, and test the unified MySQL database across all three components:
1. **PHP Student Portal** (`student-portal/`)
2. **ASP.NET MVC Admin Panel** (`admin-panel/`)
3. **Android Application** (`android-app/`)

---

## 1. Prerequisites
- **XAMPP** installed (with Apache and MySQL / MariaDB).
- **Web Browser** (Chrome, Edge, or Firefox).
- **Visual Studio** (for ASP.NET MVC Admin Panel with .NET Framework 4.7.2).
- **Android Studio** (for running the Android mobile app).

---

## 2. Step-by-Step Database Setup

### Step 1: Start XAMPP
- Open the **XAMPP Control Panel**.
- Click **Start** next to **Apache**.
- Click **Start** next to **MySQL**.
- Verify that both show green status indicators with ports `80`/`443` for Apache and `3306` for MySQL.

### Step 2: Open phpMyAdmin
- Open your browser and navigate to:
  ```
  http://localhost/phpmyadmin/
  ```

### Step 3: Import SQL Database Script
1. In phpMyAdmin, click on the **Import** tab at the top menu (or click **New** and name the database `government_scholarship_portal`).
2. Click **Choose File** / **Browse**.
3. Select the SQL file from your project:
   ```
   d:\xampp\htdocs\2412061\project\database\government_scholarship_portal.sql
   ```
4. Keep the format as **SQL** and click **Import** (or **Go**) at the bottom.
5. You should see a success message: `Import has been successfully finished`.
6. Verify that the following 8 tables are created in `government_scholarship_portal`:
   - `students`
   - `admins`
   - `scholarships`
   - `applications`
   - `documents`
   - `announcements`
   - `contacts`
   - `feedback`

---

## 3. Database Connection Configuration

### A. PHP Student Portal (`student-portal/includes/db.php`)
The database connection settings are configured in `student-portal/includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'government_scholarship_portal');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```
If your MySQL has a password, set it in `DB_PASS`.

### B. ASP.NET MVC Admin Panel (`admin-panel/Web.config`)
The connection string is added to `Web.config`:
```xml
<connectionStrings>
  <add name="GovernmentScholarshipDb"
       connectionString="server=localhost;database=government_scholarship_portal;uid=root;pwd=;"
       providerName="MySql.Data.MySqlClient" />
</connectionStrings>
```
If your MySQL requires a password, update `pwd=your_password;`.

### C. Android Application (`ApiClient.java`)
Open:
```
android-app/app/src/main/java/com/gov/scholarship/api/ApiClient.java
```
Locate `BASE_URL`:
```java
public static final String BASE_URL = "http://10.0.2.2/2412061/project/student-portal/api/";
```

> [!IMPORTANT]
> **Android Localhost Networking Explained**:
> - **Android Emulator**: An Android emulator runs in an isolated virtual network. `localhost` (127.0.0.1) inside the emulator refers to the emulator itself, NOT your PC. To access your computer's XAMPP Apache server from the emulator, Android provides the special alias IP:
>   ```
>   10.0.2.2
>   ```
>   Keep `http://10.0.2.2/2412061/project/student-portal/api/` when using the standard Android emulator.
> - **Physical Android Phone**: If testing on a real phone connected to the same Wi-Fi network as your PC:
>   1. Find your computer's local IPv4 address by opening PowerShell or CMD and running `ipconfig` (e.g. `192.168.1.50`).
>   2. Change `BASE_URL` in `ApiClient.java` to:
>      ```java
>      public static final String BASE_URL = "http://192.168.1.50/2412061/project/student-portal/api/";
>      ```
>   3. Ensure Windows Firewall allows inbound connections on port 80 for Apache HTTP Server.

---

## 4. Running the Applications

### 1. Launch PHP Student Portal
Open your web browser and go to:
```
http://localhost/2412061/project/student-portal/index.php
```
- Available Student Accounts:
  - Email: `aarav.sharma@email.com` | Password: `password123`
  - Email: `priya.patel@email.com` | Password: `password123`
  - Or click **Register** to create a brand new student account!

### 2. Launch ASP.NET MVC Admin Panel
1. Open Visual Studio.
2. Open the solution file:
   ```
   d:\xampp\htdocs\2412061\project\admin-panel\GovernmentScholarshipPortal.sln
   ```
3. If prompted to restore NuGet packages, click **Restore** or build the solution (Build > Rebuild Solution).
4. Press **F5** or click **IIS Express (Google Chrome)** to start the admin panel.
5. In your browser, navigate to the Admin Login page:
   ```
   http://localhost:51234/Admin/Login
   ```
- Default Admin Credentials:
  - Username / Email: `admin` or `admin.scholarships@gov.in`
  - Password: `admin123`

### 3. Launch Android Application
1. Open Android Studio.
2. Open the project directory:
   ```
   d:\xampp\htdocs\2412061\project\android-app
   ```
3. Sync Gradle and start an Android Virtual Device (AVD) emulator.
4. Click **Run 'app'**.
5. Test login using any registered account or register directly on the mobile app.

---

## 5. End-to-End Synchronization Testing (10 Tests)

### TEST 1: Register Student from PHP Portal
1. Open `http://localhost/2412061/project/student-portal/register.php`.
2. Fill out the registration form with:
   - Name: `Kajal Gupta`
   - Email: `kajal.gupta@email.com`
   - Phone: `9812345678`
   - Course: `Bachelor of Technology (B.Tech)`
   - Category: `General`
   - Password: `password123`
3. Click **Register Student Account**.
4. Check MySQL phpMyAdmin: The row exists in `students` with `verification_status = 'Pending'`.
5. Log in on `login.php` with `kajal.gupta@email.com` and `password123`.
6. You are redirected to `dashboard.php` showing real data.

### TEST 2: Login from Android with Same Account
1. Open the Android application.
2. Enter `kajal.gupta@email.com` and `password123`.
3. Tap **Authenticate Credentials**.
4. Login succeeds and opens `MainActivity`. Tap the Profile tab to view the student name and registered course.

### TEST 3: Admin Views New Student
1. Open the ASP.NET Admin Panel (`http://localhost:51234/Admin/Login`).
2. Log in with `admin` / `admin123`.
3. Click **Students** on the sidebar.
4. `Kajal Gupta` is listed with status `Pending`. Click **View Profile** to inspect Aadhaar and institution data.

### TEST 4: Admin Adds New Scholarship
1. In the ASP.NET Admin Panel, click **Scholarships**.
2. Click **Add New Scheme**.
3. Enter:
   - Name: `National AI & Cyber Security Fellowship 2026`
   - Category: `General`
   - Grant Amount: `45000`
   - Last Date: `2026-11-30`
   - Status: `Active`
4. Click **Publish Scheme**.
5. Verify it appears in:
   - MySQL `scholarships` table.
   - PHP Portal: Open `scholarships.php` -> The new fellowship appears!
   - Android App: Open Scholarships tab -> The new fellowship appears in the mobile list!

### TEST 5: Student Applies for Scholarship
1. In the PHP Student Portal (logged in as `Kajal Gupta`), go to `scholarships.php`.
2. Click **View Scheme Guidelines** on the newly created scholarship.
3. Click **Submit Application Now**.
4. Application reference `#APP-...` is generated.
5. Go to `dashboard.php` -> Application is listed under **Applied Scholarship Status** with status `Pending`.
6. Check MySQL `applications` table: Record exists linked by foreign keys.

### TEST 6: Admin Approves Application
1. In the ASP.NET Admin Panel, navigate to **Applications**.
2. Locate the pending application for `Kajal Gupta`.
3. Click **Approve**.
4. Status turns green (`Approved`).
5. Refresh PHP Student Portal `dashboard.php`: Status now displays **Approved**!
6. Open Android App Profile / Home tab: Reflects approved status!

### TEST 7: Document Upload
1. In the PHP Student Portal, go to `documents.php`.
2. Under **Aadhaar Card**, choose a PDF or JPG file (under 2MB).
3. Click **Upload Aadhaar**.
4. Success banner displays and file is listed under **My Uploaded Documents in Database**.
5. Verify file is stored in `student-portal/uploads/documents/` and recorded in `documents` table.

### TEST 8: Submit Contact Query
1. Open `contact.php` or mobile **Help & Support**.
2. Submit an inquiry message.
3. Reference ticket ID `#NSP-QRY-...` is returned.
4. Verify entry is recorded in the `contacts` table.

### TEST 9: Submit Portal Feedback
1. Open `feedback.php`.
2. Select rating and comments and click submit.
3. Verify entry is recorded in the `feedback` table.

### TEST 10: Admin Publishes Notice
1. In the ASP.NET Admin Panel, click **Notifications**.
2. Add a new announcement: "Examination schedule announced".
3. Check PHP `announcements.php`: The notice is visible on the timeline.
4. Check Android **Notifications** tab: The announcement is visible immediately.

---

## 6. Architecture Summary

```
                      MySQL Database (government_scholarship_portal)
                                          |
        -------------------------------------------------------------------
        |                                 |                               |
    PHP PDO Layer                 ASP.NET ADO.NET                  PHP REST APIs
        |                                 |                               |
  Student Portal Web               Nodal Admin Panel              Android Mobile App
(register, login, apply,        (review, approve, reject,       (login, list schemes, apply,
 documents, dashboard)            manage scholarships)           announcements, support)
```

The system ensures complete database synchronization: changes made from any platform are immediately reflected across all three applications.
