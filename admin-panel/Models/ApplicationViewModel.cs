using System;
using System.ComponentModel.DataAnnotations;

namespace GovernmentScholarshipPortal.Models
{
    public class ApplicationViewModel
    {
        public int Id { get; set; }

        [Required]
        [Display(Name = "Student Name")]
        public string StudentName { get; set; }

        [Required]
        [Display(Name = "Scholarship Name")]
        public string ScholarshipName { get; set; }

        [Required]
        public string Category { get; set; }

        [DisplayFormat(DataFormatString = "{0:C0}")]
        public decimal Amount { get; set; }

        [Required]
        public string Status { get; set; } // "Pending", "Approved", "Rejected"

        [Required]
        [Display(Name = "Applied Date")]
        [DataType(DataType.Date)]
        public DateTime AppliedDate { get; set; }
    }
}
