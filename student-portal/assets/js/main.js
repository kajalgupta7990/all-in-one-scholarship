// Government Scholarship Portal - Main JavaScript

document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle Functionality
    const themeToggleBtn = document.getElementById('themeToggle');
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply current theme on load
    if (currentTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
        updateThemeIcon('dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
        updateThemeIcon('light');
    }

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                updateThemeIcon('light');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                updateThemeIcon('dark');
            }
        });
    }

    function updateThemeIcon(theme) {
        if (!themeToggleBtn) return;
        const icon = themeToggleBtn.querySelector('i');
        if (theme === 'dark') {
            icon.className = 'fas fa-sun text-warning';
        } else {
            icon.className = 'fas fa-moon text-white';
        }
    }

    // Scroll-to-Top Button
    const scrollTopBtn = document.getElementById('scrollTop');
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollTopBtn.style.display = 'flex';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Dynamic Search Filter for Scholarship List Page
    const searchInput = document.getElementById('searchScholarship');
    const categorySelect = document.getElementById('categoryFilter');
    const scholarshipCards = document.querySelectorAll('.scholarship-item');

    function filterScholarships() {
        const query = searchInput ? searchInput.value.toLowerCase() : '';
        const selectedCategory = categorySelect ? categorySelect.value : 'all';

        scholarshipCards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const category = card.dataset.category;
            
            const matchesSearch = title.includes(query);
            const matchesCategory = selectedCategory === 'all' || category === selectedCategory;

            if (matchesSearch && matchesCategory) {
                card.style.display = 'block';
                card.classList.add('fade-in-up');
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterScholarships);
    if (categorySelect) categorySelect.addEventListener('change', filterScholarships);

    // Mock Eligibility Checker Logic
    const checkerForm = document.getElementById('eligibilityForm');
    const checkerResult = document.getElementById('eligibilityResult');

    if (checkerForm && checkerResult) {
        checkerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Show loading spinner
            checkerResult.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Analyzing portal database databases and criteria...</p>
                </div>
            `;
            checkerResult.scrollIntoView({ behavior: 'smooth' });

            setTimeout(() => {
                const income = parseFloat(document.getElementById('annualIncome').value);
                const marks = parseFloat(document.getElementById('percentage').value);
                const category = document.getElementById('studentCategory').value;
                
                let matches = [];

                if (marks >= 80 && income <= 500000) {
                    matches.push({
                        name: "National Merit Scholarship Scheme (NMSS)",
                        amount: "$1,200 / Year",
                        desc: "For high-performing students across general/minority groups."
                    });
                }
                if (income <= 250000) {
                    matches.push({
                        name: "Post-Matric Scholarship for Low Income Families",
                        amount: "Full Tuition Coverage",
                        desc: "Tuition waiver for courses under certified government colleges."
                    });
                }
                if (category === 'SC' || category === 'ST') {
                    matches.push({
                        name: "Special ST/SC Higher Education Assistance",
                        amount: "$800 / Year + Book Allowance",
                        desc: "Targeted support to enable students to pursue professional degrees."
                    });
                }
                if (marks >= 60 && category === 'OBC' && income <= 300000) {
                    matches.push({
                        name: "OBC Professional Course Fellowship",
                        amount: "$1,000 / Year",
                        desc: "Incentive scheme for students of OBC categories enrolled in Technical Courses."
                    });
                }

                if (matches.length > 0) {
                    let cardsHtml = matches.map(scheme => `
                        <div class="card border-success border-2 mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-success"><i class="fas fa-check-circle me-2"></i>${scheme.name}</h5>
                                <p class="card-text">${scheme.desc}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="badge bg-success fs-6">Benefit: ${scheme.amount}</span>
                                    <a href="scholarships.php" class="btn btn-outline-success btn-sm">View Details & Apply</a>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    checkerResult.innerHTML = `
                        <div class="alert alert-success border-0 shadow-sm p-4 rounded-3 fade-in-up">
                            <h4 class="alert-heading font-weight-bold"><i class="fas fa-laugh-beam me-2"></i>Congratulations! You are Eligible!</h4>
                            <p class="mb-4">Based on your academic profile and annual income, you qualify for <strong>${matches.length}</strong> government scholarship schemes listed below.</p>
                            ${cardsHtml}
                        </div>
                    `;
                } else {
                    checkerResult.innerHTML = `
                        <div class="alert alert-warning border-0 shadow-sm p-4 rounded-3 fade-in-up">
                            <h4 class="alert-heading text-warning"><i class="fas fa-info-circle me-2"></i>No Direct Matches Found</h4>
                            <p class="mb-2">We couldn't match you to a specific scheme automatically. This is usually due to one of the following reasons:</p>
                            <ul>
                                <li>Annual household income exceeds limits.</li>
                                <li>Your graduation marks/percentage is below 60%.</li>
                            </ul>
                            <p class="mb-0">You can still browse the full list of active scholarships in the portal for specific department schemes.</p>
                            <a href="scholarships.php" class="btn btn-gov-primary mt-3">Browse Scholarship Catalog</a>
                        </div>
                    `;
                }
                checkerResult.scrollIntoView({ behavior: 'smooth' });
            }, 1200);
        });
    }
});
