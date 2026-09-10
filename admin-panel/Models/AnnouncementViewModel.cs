using System;
using System.ComponentModel.DataAnnotations;

namespace GovernmentScholarshipPortal.Models
{
    public class AnnouncementViewModel
    {
        public int Id { get; set; }

        [Required]
        [StringLength(100, MinimumLength = 5)]
        public string Title { get; set; }

        [Required]
        [DataType(DataType.MultilineText)]
        public string Content { get; set; }

        [Required]
        [Display(Name = "Target Audience")]
        public string TargetAudience { get; set; } // "All Students", "SC/ST Students", "OBC Students", "Minority Students"

        [Required]
        [Display(Name = "Published Date")]
        public DateTime PublishedDate { get; set; }

        public bool IsActive { get; set; }
    }
}
