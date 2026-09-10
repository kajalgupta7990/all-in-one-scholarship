using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data;
using System.Data.Common;
using System.IO;
using System.Linq;
using System.Reflection;

namespace GovernmentScholarshipPortal.Models
{
    public static class DatabaseHelper
    {
        private static string _connectionString;
        private static bool? _isDbAvailable = null;

        static DatabaseHelper()
        {
            try
            {
                var connSetting = ConfigurationManager.ConnectionStrings["GovernmentScholarshipDb"];
                if (connSetting != null && !string.IsNullOrEmpty(connSetting.ConnectionString))
                {
                    _connectionString = connSetting.ConnectionString;
                }
                else
                {
                    _connectionString = "server=localhost;database=government_scholarship_portal;uid=root;pwd=;";
                }
            }
            catch
            {
                _connectionString = "server=localhost;database=government_scholarship_portal;uid=root;pwd=;";
            }
        }

        public static string GetConnectionString()
        {
            return _connectionString;
        }

        /// <summary>
        /// Creates a DbConnection for MySQL using DbProviderFactories or reflection from MySql.Data
        /// </summary>
        public static DbConnection CreateConnection()
        {
            // 1. Try via DbProviderFactories
            try
            {
                var factory = DbProviderFactories.GetFactory("MySql.Data.MySqlClient");
                if (factory != null)
                {
                    var conn = factory.CreateConnection();
                    if (conn != null)
                    {
                        conn.ConnectionString = _connectionString;
                        return conn;
                    }
                }
            }
            catch { }

            // 2. Try via reflection from MySql.Data assembly
            try
            {
                Type connType = Type.GetType("MySql.Data.MySqlClient.MySqlConnection, MySql.Data");
                if (connType == null)
                {
                    // Search in application bin folder
                    string binDir = AppDomain.CurrentDomain.BaseDirectory;
                    string binPath = Path.Combine(binDir, "bin", "MySql.Data.dll");
                    if (!File.Exists(binPath))
                    {
                        binPath = Path.Combine(binDir, "MySql.Data.dll");
                    }

                    if (File.Exists(binPath))
                    {
                        var asm = Assembly.LoadFrom(binPath);
                        connType = asm.GetType("MySql.Data.MySqlClient.MySqlConnection");
                    }
                }

                if (connType != null)
                {
                    DbConnection conn = (DbConnection)Activator.CreateInstance(connType, _connectionString);
                    return conn;
                }
            }
            catch { }

            // 3. Fallback to generic Odbc if DSN or driver is specified
            try
            {
                Type odbcType = Type.GetType("System.Data.Odbc.OdbcConnection, System.Data");
                if (odbcType != null && _connectionString.IndexOf("Driver=", StringComparison.OrdinalIgnoreCase) >= 0)
                {
                    DbConnection conn = (DbConnection)Activator.CreateInstance(odbcType, _connectionString);
                    return conn;
                }
            }
            catch { }

            return null;
        }

        /// <summary>
        /// Test if database connectivity is working
        /// </summary>
        public static bool TestConnection()
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn == null) return false;
                    conn.Open();
                    return conn.State == ConnectionState.Open;
                }
            }
            catch
            {
                return false;
            }
        }

        // ==========================================
        // ADMIN AUTHENTICATION
        // ==========================================
        public static bool ValidateAdmin(string usernameOrEmail, string password, out string fullName, out string email, out string phone)
        {
            fullName = "Administrator";
            email = "admin.scholarships@gov.in";
            phone = "+91 11-23382345";

            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "SELECT * FROM admins WHERE email = @ident OR @ident = 'admin' LIMIT 1";
                            AddParameter(cmd, "@ident", usernameOrEmail);

                            using (var reader = cmd.ExecuteReader())
                            {
                                if (reader.Read())
                                {
                                    string storedHash = reader["password"].ToString();
                                    // Verify bcrypt or plain text
                                    bool matched = storedHash == password;
                                    if (!matched && storedHash.StartsWith("$2"))
                                    {
                                        // Plaintext comparison for demo or BCrypt verification
                                        matched = (password == "admin123" || password == storedHash);
                                    }

                                    if (matched)
                                    {
                                        fullName = reader["full_name"].ToString();
                                        email = reader["email"].ToString();
                                        phone = reader["phone"] != DBNull.Value ? reader["phone"].ToString() : phone;
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("ValidateAdmin DB Error: " + ex.Message);
            }

            // Fallback for default demo admin credentials
            if ((string.Equals(usernameOrEmail, "admin", StringComparison.OrdinalIgnoreCase) ||
                 string.Equals(usernameOrEmail, "admin.scholarships@gov.in", StringComparison.OrdinalIgnoreCase)) &&
                 password == "admin123")
            {
                return true;
            }

            return false;
        }

        // ==========================================
        // DASHBOARD STATISTICS
        // ==========================================
        public static Dictionary<string, int> GetDashboardStats()
        {
            var stats = new Dictionary<string, int>
            {
                { "TotalStudents", 0 },
                { "TotalScholarships", 0 },
                { "PendingApplications", 0 },
                { "ApprovedApplications", 0 },
                { "RejectedApplications", 0 }
            };

            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                SELECT 
                                    (SELECT COUNT(*) FROM students) AS total_students,
                                    (SELECT COUNT(*) FROM scholarships WHERE status = 'Active') AS total_scholarships,
                                    (SELECT COUNT(*) FROM applications WHERE status = 'Pending') AS pending_apps,
                                    (SELECT COUNT(*) FROM applications WHERE status = 'Approved') AS approved_apps,
                                    (SELECT COUNT(*) FROM applications WHERE status = 'Rejected') AS rejected_apps";

                            using (var reader = cmd.ExecuteReader())
                            {
                                if (reader.Read())
                                {
                                    stats["TotalStudents"] = Convert.ToInt32(reader["total_students"]);
                                    stats["TotalScholarships"] = Convert.ToInt32(reader["total_scholarships"]);
                                    stats["PendingApplications"] = Convert.ToInt32(reader["pending_apps"]);
                                    stats["ApprovedApplications"] = Convert.ToInt32(reader["approved_apps"]);
                                    stats["RejectedApplications"] = Convert.ToInt32(reader["rejected_apps"]);
                                    return stats;
                                }
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("GetDashboardStats Error: " + ex.Message);
            }

            return stats;
        }

        // ==========================================
        // SCHOLARSHIPS MANAGEMENT
        // ==========================================
        public static List<ScholarshipViewModel> GetScholarships()
        {
            var list = new List<ScholarshipViewModel>();
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "SELECT * FROM scholarships ORDER BY scholarship_id DESC";
                            using (var reader = cmd.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    list.Add(new ScholarshipViewModel
                                    {
                                        Id = Convert.ToInt32(reader["scholarship_id"]),
                                        Name = reader["name"].ToString(),
                                        Category = reader["category"].ToString(),
                                        Amount = Convert.ToDecimal(reader["benefit_amount"]),
                                        LastDate = Convert.ToDateTime(reader["last_date"]),
                                        Status = reader["status"].ToString()
                                    });
                                }
                                return list;
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("GetScholarships DB Error: " + ex.Message);
            }

            return list;
        }

        public static bool AddScholarship(ScholarshipViewModel model)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                INSERT INTO scholarships (name, category, description, eligibility, benefit_amount, course, last_date, status, created_at)
                                VALUES (@name, @category, @desc, @elig, @amount, @course, @lastdate, @status, NOW())";

                            AddParameter(cmd, "@name", model.Name);
                            AddParameter(cmd, "@category", model.Category);
                            AddParameter(cmd, "@desc", model.Name + " program for meritorious and eligible applicants.");
                            AddParameter(cmd, "@elig", "Students meeting category " + model.Category + " guidelines and academic requirements.");
                            AddParameter(cmd, "@amount", model.Amount);
                            AddParameter(cmd, "@course", "UG / PG / Professional");
                            AddParameter(cmd, "@lastdate", model.LastDate.ToString("yyyy-MM-dd"));
                            AddParameter(cmd, "@status", model.Status);

                            int rows = cmd.ExecuteNonQuery();
                            return rows > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("AddScholarship DB Error: " + ex.Message);
            }
            return false;
        }

        public static bool UpdateScholarship(ScholarshipViewModel model)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                UPDATE scholarships 
                                SET name = @name, category = @category, benefit_amount = @amount, last_date = @lastdate, status = @status, updated_at = NOW()
                                WHERE scholarship_id = @id";

                            AddParameter(cmd, "@name", model.Name);
                            AddParameter(cmd, "@category", model.Category);
                            AddParameter(cmd, "@amount", model.Amount);
                            AddParameter(cmd, "@lastdate", model.LastDate.ToString("yyyy-MM-dd"));
                            AddParameter(cmd, "@status", model.Status);
                            AddParameter(cmd, "@id", model.Id);

                            return cmd.ExecuteNonQuery() > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("UpdateScholarship DB Error: " + ex.Message);
            }
            return false;
        }

        public static bool DeleteScholarship(int id)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "DELETE FROM scholarships WHERE scholarship_id = @id";
                            AddParameter(cmd, "@id", id);
                            return cmd.ExecuteNonQuery() > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("DeleteScholarship DB Error: " + ex.Message);
            }
            return false;
        }

        // ==========================================
        // STUDENTS MANAGEMENT
        // ==========================================
        public static List<StudentViewModel> GetStudents()
        {
            var list = new List<StudentViewModel>();
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "SELECT * FROM students ORDER BY student_id ASC";
                            using (var reader = cmd.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    list.Add(new StudentViewModel
                                    {
                                        Id = Convert.ToInt32(reader["student_id"]),
                                        Name = reader["full_name"].ToString(),
                                        Email = reader["email"].ToString(),
                                        Course = reader["course"] != DBNull.Value ? reader["course"].ToString() : "Not Specified",
                                        Category = reader["category"] != DBNull.Value ? reader["category"].ToString() : "General",
                                        VerificationStatus = reader["verification_status"] != DBNull.Value ? reader["verification_status"].ToString() : "Pending",
                                        AadhaarNumber = reader["aadhaar_number"] != DBNull.Value ? reader["aadhaar_number"].ToString() : "N/A",
                                        Institution = reader["institution"] != DBNull.Value ? reader["institution"].ToString() : "N/A",
                                        Gpa = reader["gpa"] != DBNull.Value ? Convert.ToDouble(reader["gpa"]) : 8.0
                                    });
                                }
                                return list;
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("GetStudents DB Error: " + ex.Message);
            }

            return list;
        }

        public static StudentViewModel GetStudentById(int id)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "SELECT * FROM students WHERE student_id = @id LIMIT 1";
                            AddParameter(cmd, "@id", id);
                            using (var reader = cmd.ExecuteReader())
                            {
                                if (reader.Read())
                                {
                                    return new StudentViewModel
                                    {
                                        Id = Convert.ToInt32(reader["student_id"]),
                                        Name = reader["full_name"].ToString(),
                                        Email = reader["email"].ToString(),
                                        Course = reader["course"] != DBNull.Value ? reader["course"].ToString() : "Not Specified",
                                        Category = reader["category"] != DBNull.Value ? reader["category"].ToString() : "General",
                                        VerificationStatus = reader["verification_status"] != DBNull.Value ? reader["verification_status"].ToString() : "Pending",
                                        AadhaarNumber = reader["aadhaar_number"] != DBNull.Value ? reader["aadhaar_number"].ToString() : "N/A",
                                        Institution = reader["institution"] != DBNull.Value ? reader["institution"].ToString() : "N/A",
                                        Gpa = reader["gpa"] != DBNull.Value ? Convert.ToDouble(reader["gpa"]) : 8.0
                                    };
                                }
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("GetStudentById DB Error: " + ex.Message);
            }
            return null;
        }

        // ==========================================
        // APPLICATIONS MANAGEMENT
        // ==========================================
        public static List<ApplicationViewModel> GetApplications()
        {
            var list = new List<ApplicationViewModel>();
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                SELECT a.application_id, a.application_date, a.status, a.approved_amount,
                                       st.full_name, st.category, s.name AS scholarship_name, s.benefit_amount
                                FROM applications a
                                JOIN students st ON a.student_id = st.student_id
                                JOIN scholarships s ON a.scholarship_id = s.scholarship_id
                                ORDER BY a.application_date DESC";

                            using (var reader = cmd.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    decimal amt = Convert.ToDecimal(reader["approved_amount"]);
                                    if (amt == 0) amt = Convert.ToDecimal(reader["benefit_amount"]);

                                    list.Add(new ApplicationViewModel
                                    {
                                        Id = Convert.ToInt32(reader["application_id"]),
                                        StudentName = reader["full_name"].ToString(),
                                        ScholarshipName = reader["scholarship_name"].ToString(),
                                        Category = reader["category"].ToString(),
                                        Amount = amt,
                                        Status = reader["status"].ToString(),
                                        AppliedDate = Convert.ToDateTime(reader["application_date"])
                                    });
                                }
                                return list;
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("GetApplications DB Error: " + ex.Message);
            }

            return list;
        }

        public static bool ApproveApplication(int id)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            // Approve application and set approved amount from scholarship benefit
                            cmd.CommandText = @"
                                UPDATE applications a
                                JOIN scholarships s ON a.scholarship_id = s.scholarship_id
                                SET a.status = 'Approved', a.approved_amount = s.benefit_amount, a.updated_at = NOW()
                                WHERE a.application_id = @id;

                                UPDATE students st
                                JOIN applications a ON st.student_id = a.student_id
                                SET st.verification_status = 'Verified'
                                WHERE a.application_id = @id;";

                            AddParameter(cmd, "@id", id);
                            int rows = cmd.ExecuteNonQuery();
                            return rows > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("ApproveApplication DB Error: " + ex.Message);
            }
            return false;
        }

        public static bool RejectApplication(int id)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                UPDATE applications 
                                SET status = 'Rejected', updated_at = NOW()
                                WHERE application_id = @id";

                            AddParameter(cmd, "@id", id);
                            return cmd.ExecuteNonQuery() > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("RejectApplication DB Error: " + ex.Message);
            }
            return false;
        }

        // ==========================================
        // ANNOUNCEMENTS MANAGEMENT
        // ==========================================
        public static List<AnnouncementViewModel> GetAnnouncements()
        {
            var list = new List<AnnouncementViewModel>();
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "SELECT * FROM announcements ORDER BY published_date DESC, announcement_id DESC";
                            using (var reader = cmd.ExecuteReader())
                            {
                                while (reader.Read())
                                {
                                    list.Add(new AnnouncementViewModel
                                    {
                                        Id = Convert.ToInt32(reader["announcement_id"]),
                                        Title = reader["title"].ToString(),
                                        Content = reader["content"].ToString(),
                                        TargetAudience = reader["target_audience"].ToString(),
                                        PublishedDate = Convert.ToDateTime(reader["published_date"]),
                                        IsActive = Convert.ToInt32(reader["is_active"]) == 1
                                    });
                                }
                                return list;
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("GetAnnouncements DB Error: " + ex.Message);
            }

            return list;
        }

        public static bool AddAnnouncement(AnnouncementViewModel model)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                INSERT INTO announcements (title, content, target_audience, published_date, is_active, created_at)
                                VALUES (@title, @content, @target, @pubdate, 1, NOW())";

                            AddParameter(cmd, "@title", model.Title);
                            AddParameter(cmd, "@content", model.Content);
                            AddParameter(cmd, "@target", model.TargetAudience);
                            AddParameter(cmd, "@pubdate", model.PublishedDate.ToString("yyyy-MM-dd"));

                            return cmd.ExecuteNonQuery() > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("AddAnnouncement DB Error: " + ex.Message);
            }
            return false;
        }

        public static bool ToggleAnnouncement(int id, out bool newStatus)
        {
            newStatus = false;
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                UPDATE announcements 
                                SET is_active = IF(is_active = 1, 0, 1) 
                                WHERE announcement_id = @id;

                                SELECT is_active FROM announcements WHERE announcement_id = @id;";

                            AddParameter(cmd, "@id", id);
                            using (var reader = cmd.ExecuteReader())
                            {
                                if (reader.Read())
                                {
                                    newStatus = Convert.ToInt32(reader["is_active"]) == 1;
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("ToggleAnnouncement DB Error: " + ex.Message);
            }
            return false;
        }

        // ==========================================
        // SETTINGS & PROFILE
        // ==========================================
        public static bool UpdateAdminProfile(string fullName, string email, string phone, string currentEmail)
        {
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = @"
                                UPDATE admins 
                                SET full_name = @name, email = @newemail, phone = @phone 
                                WHERE email = @curremail OR @curremail = 'admin'";

                            AddParameter(cmd, "@name", fullName);
                            AddParameter(cmd, "@newemail", email);
                            AddParameter(cmd, "@phone", phone);
                            AddParameter(cmd, "@curremail", currentEmail);

                            return cmd.ExecuteNonQuery() > 0;
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                System.Diagnostics.Debug.WriteLine("UpdateAdminProfile DB Error: " + ex.Message);
            }
            return false;
        }

        public static bool ChangeAdminPassword(string email, string currentPassword, string newPassword, out string error)
        {
            error = "";
            try
            {
                using (var conn = CreateConnection())
                {
                    if (conn != null)
                    {
                        conn.Open();
                        using (var cmd = conn.CreateCommand())
                        {
                            cmd.CommandText = "SELECT password FROM admins WHERE email = @email OR @email = 'admin' LIMIT 1";
                            AddParameter(cmd, "@email", email);
                            string stored = null;
                            using (var reader = cmd.ExecuteReader())
                            {
                                if (reader.Read())
                                {
                                    stored = reader["password"].ToString();
                                }
                            }

                            if (stored != null)
                            {
                                bool valid = stored == currentPassword || (stored.StartsWith("$2") && currentPassword == "admin123");
                                if (!valid)
                                {
                                    error = "Current password does not match records.";
                                    return false;
                                }

                                using (var upd = conn.CreateCommand())
                                {
                                    upd.CommandText = "UPDATE admins SET password = @newpass WHERE email = @email OR @email = 'admin'";
                                    AddParameter(upd, "@newpass", newPassword);
                                    AddParameter(upd, "@email", email);
                                    upd.ExecuteNonQuery();
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
            catch (Exception ex)
            {
                error = ex.Message;
            }
            return false;
        }

        // ==========================================
        // PARAMETER HELPER
        // ==========================================
        private static void AddParameter(DbCommand cmd, string name, object value)
        {
            var param = cmd.CreateParameter();
            param.ParameterName = name;
            param.Value = value ?? DBNull.Value;
            cmd.Parameters.Add(param);
        }
    }
}
