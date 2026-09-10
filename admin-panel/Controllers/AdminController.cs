using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Mvc;
using GovernmentScholarshipPortal.Models;

namespace GovernmentScholarshipPortal.Controllers
{
    public class AdminController : Controller
    {
        // Portal dynamic settings
        private static string AdminFullName = "Abc kumar";
        private static string AdminEmail = "admin.scholarships@gov.in";
        private static string AdminPhone = "+91 11-23382345";
        private static string PortalTitle = "National Scholarship Portal (NSP) Admin Dashboard";
        private static string PrimaryContactEmail = "helpdesk-nsp@gov.in";
        private static bool MaintenanceMode = false;
        private static int MinimumGpaThreshold = 6;

        // Helper to check login status
        private bool IsAdminAuthenticated()
        {
            return Session["AdminLoggedIn"] != null && (bool)Session["AdminLoggedIn"];
        }

        // 1. ADMIN LOGIN (GET)
        [HttpGet]
        public ActionResult Login()
        {
            if (IsAdminAuthenticated())
            {
                return RedirectToAction("Dashboard");
            }
            return View();
        }

        // 1. ADMIN LOGIN (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult Login(string username, string password)
        {
            string fullName, email, phone;
            if (DatabaseHelper.ValidateAdmin(username, password, out fullName, out email, out phone))
            {
                Session["AdminLoggedIn"] = true;
                Session["AdminName"] = fullName;
                Session["AdminEmail"] = email;
                Session["PortalTitle"] = PortalTitle;

                AdminFullName = fullName;
                AdminEmail = email;
                AdminPhone = phone;

                TempData["SuccessMessage"] = "Welcome back, " + fullName + "!";
                return RedirectToAction("Dashboard");
            }

            ViewBag.ErrorMessage = "Invalid administrative credentials. Please verify username and password.";
            return View();
        }

        // LOGOUT
        public ActionResult Logout()
        {
            Session.Clear();
            TempData["SuccessMessage"] = "You have been logged out successfully.";
            return RedirectToAction("Login");
        }

        // 2. DASHBOARD
        public ActionResult Dashboard()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            var stats = DatabaseHelper.GetDashboardStats();
            var allApplications = DatabaseHelper.GetApplications();

            ViewBag.TotalStudents = stats["TotalStudents"];
            ViewBag.TotalScholarships = stats["TotalScholarships"];
            ViewBag.PendingApplications = stats["PendingApplications"];
            ViewBag.ApprovedApplications = stats["ApprovedApplications"];
            ViewBag.RejectedApplications = stats["RejectedApplications"];
            ViewBag.PortalTitle = PortalTitle;
            ViewBag.MaintenanceMode = MaintenanceMode;

            // Pass Recent Applications to Dashboard (latest 5)
            var recentApps = allApplications.OrderByDescending(a => a.AppliedDate).Take(5).ToList();
            return View(recentApps);
        }

        // 3. MANAGE SCHOLARSHIPS
        public ActionResult Scholarships()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            var list = DatabaseHelper.GetScholarships();
            return View(list);
        }

        // Add Scholarship (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult AddScholarship(ScholarshipViewModel model)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            if (ModelState.IsValid)
            {
                if (model.LastDate < DateTime.Now && model.Status != "Expired")
                {
                    model.Status = "Expired";
                }

                bool added = DatabaseHelper.AddScholarship(model);
                if (added)
                {
                    TempData["SuccessMessage"] = $"Scholarship '{model.Name}' created successfully in database.";
                }
                else
                {
                    TempData["ErrorMessage"] = "Could not create scholarship. Database error occurred.";
                }
            }
            else
            {
                TempData["ErrorMessage"] = "Failed to add scholarship. Please check input values.";
            }

            return RedirectToAction("Scholarships");
        }

        // Edit Scholarship (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult EditScholarship(ScholarshipViewModel model)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            if (ModelState.IsValid)
            {
                bool updated = DatabaseHelper.UpdateScholarship(model);
                if (updated)
                {
                    TempData["SuccessMessage"] = $"Scholarship '{model.Name}' updated successfully in database.";
                }
                else
                {
                    TempData["ErrorMessage"] = "Failed to update scholarship record in database.";
                }
            }
            else
            {
                TempData["ErrorMessage"] = "Failed to update. Verify inputs.";
            }

            return RedirectToAction("Scholarships");
        }

        // Delete Scholarship (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult DeleteScholarship(int id)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            bool deleted = DatabaseHelper.DeleteScholarship(id);
            if (deleted)
            {
                TempData["SuccessMessage"] = "Scholarship scheme deleted successfully from database.";
            }
            else
            {
                TempData["ErrorMessage"] = "Scholarship not found or failed to delete.";
            }

            return RedirectToAction("Scholarships");
        }

        // 4. MANAGE STUDENTS
        public ActionResult Students()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            var students = DatabaseHelper.GetStudents();
            return View(students);
        }

        // Get student details (JSON for modal display)
        [HttpGet]
        public JsonResult GetStudentDetails(int id)
        {
            var student = DatabaseHelper.GetStudentById(id);
            if (student != null)
            {
                return Json(student, JsonRequestBehavior.AllowGet);
            }
            return Json(null, JsonRequestBehavior.AllowGet);
        }

        // 5. MANAGE APPLICATIONS
        public ActionResult Applications()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            var applications = DatabaseHelper.GetApplications();
            return View(applications);
        }

        // Approve Application (POST/GET)
        [HttpPost]
        public ActionResult ApproveApplication(int id)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            bool approved = DatabaseHelper.ApproveApplication(id);
            if (approved)
            {
                TempData["SuccessMessage"] = $"Application #{id} has been Approved in database.";
                return Json(new { success = true, message = $"Application #{id} approved successfully." });
            }
            return Json(new { success = false, message = "Application not found or database update failed." });
        }

        // Reject Application (POST/GET)
        [HttpPost]
        public ActionResult RejectApplication(int id)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            bool rejected = DatabaseHelper.RejectApplication(id);
            if (rejected)
            {
                TempData["SuccessMessage"] = $"Application #{id} has been Rejected in database.";
                return Json(new { success = true, message = $"Application #{id} rejected successfully." });
            }
            return Json(new { success = false, message = "Application not found or database update failed." });
        }

        // 6. REPORTS
        public ActionResult Reports()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            var applications = DatabaseHelper.GetApplications();
            var scholarships = DatabaseHelper.GetScholarships();

            // Prepare Aggregated Statistics for Charts
            // 1. Application Status distribution (Pie Chart)
            ViewBag.StatusLabels = new string[] { "Approved", "Pending", "Rejected" };
            ViewBag.StatusData = new int[] 
            { 
                applications.Count(a => a.Status == "Approved"),
                applications.Count(a => a.Status == "Pending"),
                applications.Count(a => a.Status == "Rejected")
            };

            // 2. Budget Allocated per Category (Bar Chart)
            var categories = new string[] { "General", "OBC", "SC", "ST", "Minority" };
            var disbursedData = new decimal[categories.Length];
            for (int i = 0; i < categories.Length; i++)
            {
                disbursedData[i] = applications
                    .Where(a => a.Status == "Approved" && string.Equals(a.Category, categories[i], StringComparison.OrdinalIgnoreCase))
                    .Sum(a => a.Amount);
            }
            ViewBag.CategoryLabels = categories;
            ViewBag.CategoryData = disbursedData;

            // 3. Summary Report Table
            var summaryReport = scholarships.Select(s => new {
                ScholarshipName = s.Name,
                Category = s.Category,
                Amount = s.Amount,
                TotalApplied = applications.Count(a => a.ScholarshipName == s.Name),
                TotalApproved = applications.Count(a => a.ScholarshipName == s.Name && a.Status == "Approved"),
                TotalAmountDisbursed = applications.Where(a => a.ScholarshipName == s.Name && a.Status == "Approved").Sum(a => a.Amount)
            }).ToList();

            ViewBag.SummaryReport = summaryReport;

            return View();
        }

        // 7. NOTIFICATIONS / ANNOUNCEMENTS
        public ActionResult Notifications()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            var announcements = DatabaseHelper.GetAnnouncements();
            return View(announcements);
        }

        // Publish Announcement (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult PublishAnnouncement(AnnouncementViewModel model)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            if (ModelState.IsValid)
            {
                model.PublishedDate = DateTime.Now;
                model.IsActive = true;
                bool added = DatabaseHelper.AddAnnouncement(model);

                if (added)
                {
                    TempData["SuccessMessage"] = "Announcement published successfully to MySQL database!";
                }
                else
                {
                    TempData["ErrorMessage"] = "Database error while publishing announcement.";
                }
            }
            else
            {
                TempData["ErrorMessage"] = "Failed to publish announcement. Please fill all fields correctly.";
            }

            return RedirectToAction("Notifications");
        }

        // Toggle Announcement Status
        [HttpPost]
        public ActionResult ToggleAnnouncement(int id)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            bool newStatus;
            bool toggled = DatabaseHelper.ToggleAnnouncement(id, out newStatus);
            if (toggled)
            {
                return Json(new { success = true, isActive = newStatus });
            }
            return Json(new { success = false });
        }

        // 8. SETTINGS
        public ActionResult Settings()
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            ViewBag.AdminFullName = AdminFullName;
            ViewBag.AdminEmail = AdminEmail;
            ViewBag.AdminPhone = AdminPhone;
            ViewBag.PortalTitle = PortalTitle;
            ViewBag.PrimaryContactEmail = PrimaryContactEmail;
            ViewBag.MaintenanceMode = MaintenanceMode;
            ViewBag.MinimumGpaThreshold = MinimumGpaThreshold;

            return View();
        }

        // Save Profile Settings (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult SaveProfile(string fullName, string email, string phone)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            string currentEmail = Session["AdminEmail"] != null ? Session["AdminEmail"].ToString() : AdminEmail;
            DatabaseHelper.UpdateAdminProfile(fullName, email, phone, currentEmail);

            AdminFullName = fullName;
            AdminEmail = email;
            AdminPhone = phone;

            // Sync with session
            Session["AdminName"] = AdminFullName;
            Session["AdminEmail"] = AdminEmail;

            TempData["SuccessMessage"] = "Admin profile updated successfully in database.";
            return RedirectToAction("Settings");
        }

        // Save Portal Settings (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult SavePortalSettings(string portalTitle, string contactEmail, bool maintenanceMode, int gpaThreshold)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            PortalTitle = portalTitle;
            PrimaryContactEmail = contactEmail;
            MaintenanceMode = maintenanceMode;
            MinimumGpaThreshold = gpaThreshold;

            Session["PortalTitle"] = PortalTitle;

            TempData["SuccessMessage"] = "Portal settings and system configuration saved.";
            return RedirectToAction("Settings");
        }

        // Change Password (POST)
        [HttpPost]
        [ValidateAntiForgeryToken]
        public ActionResult ChangePassword(string currentPassword, string newPassword, string confirmPassword)
        {
            if (!IsAdminAuthenticated()) return RedirectToAction("Login");

            if (newPassword != confirmPassword)
            {
                TempData["ErrorMessage"] = "New password and confirmation do not match.";
                return RedirectToAction("Settings");
            }

            if (string.IsNullOrEmpty(newPassword) || newPassword.Length < 6)
            {
                TempData["ErrorMessage"] = "Password must be at least 6 characters long.";
                return RedirectToAction("Settings");
            }

            string currentEmail = Session["AdminEmail"] != null ? Session["AdminEmail"].ToString() : AdminEmail;
            string error;
            bool changed = DatabaseHelper.ChangeAdminPassword(currentEmail, currentPassword, newPassword, out error);

            if (changed)
            {
                TempData["SuccessMessage"] = "Admin password updated successfully in database.";
            }
            else
            {
                TempData["ErrorMessage"] = !string.IsNullOrEmpty(error) ? error : "Could not update password.";
            }

            return RedirectToAction("Settings");
        }
    }
}
