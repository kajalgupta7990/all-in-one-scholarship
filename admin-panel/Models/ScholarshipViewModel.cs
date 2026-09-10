using System;
using System.ComponentModel.DataAnnotations;

namespace GovernmentScholarshipPortal.Models
{
    public class ScholarshipViewModel
    {
        public int Id { get; set; }

        [Required]
        [Display(Name = "Scholarship Name")]
        public string Name { get; set; }

        [Required]
        public string Category { get; set; }

        [Required]
        [Range(1, 1000000, ErrorMessage = "Amount must be positive")]
        [DisplayFormat(DataFormatString = "{0:C0}")]
        public decimal Amount { get; set; }

        [Required]
        [DataType(DataType.Date)]
        [Display(Name = "Last Date to Apply")]
        public DateTime LastDate { get; set; }

        [Required]
        public string Status { get; set; } // "Active", "Expired", "Draft"
    }
}
