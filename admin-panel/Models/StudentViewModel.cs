using System.ComponentModel.DataAnnotations;

namespace GovernmentScholarshipPortal.Models
{
    public class StudentViewModel
    {
        public int Id { get; set; }

        [Required]
        [Display(Name = "Student Name")]
        public string Name { get; set; }

        [Required]
        [EmailAddress]
        public string Email { get; set; }

        [Required]
        public string Course { get; set; }

        [Required]
        public string Category { get; set; } // "General", "OBC", "SC", "ST", "Minority"

        [Required]
        [Display(Name = "Verification Status")]
        public string VerificationStatus { get; set; } // "Verified", "Pending", "Rejected"

        [Display(Name = "Aadhaar Number")]
        public string AadhaarNumber { get; set; }

        public string Institution { get; set; }
        
        [Display(Name = "Academic GPA")]
        public double Gpa { get; set; }
    }
}
