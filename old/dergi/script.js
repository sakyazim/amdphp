document.addEventListener('DOMContentLoaded', function() {
    // Dropdown menüler için hover etkinleştirme (masaüstü için)
    const dropdowns = document.querySelectorAll('.dropdown');
    
    if (window.innerWidth >= 768) {
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('mouseenter', function() {
                this.querySelector('.dropdown-toggle').click();
            });
            
            dropdown.addEventListener('mouseleave', function() {
                this.querySelector('.dropdown-toggle').click();
            });
        });
    }
    
    // Sekme değişiminde animasyon
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function(event) {
            const targetPane = document.querySelector(event.target.dataset.bsTarget);
            targetPane.classList.add('animate-fade-in');
            
            setTimeout(() => {
                targetPane.classList.remove('animate-fade-in');
            }, 500);
        });
    });
    
    // Arama kutusu için gelişmiş işlevsellik
    const searchInput = document.querySelector('.search');
    
    if (searchInput) {
        searchInput.addEventListener('focus', function() {
            this.setAttribute('placeholder', 'Arama yapmak için yazın...');
        });
        
        searchInput.addEventListener('blur', function() {
            this.setAttribute('placeholder', 'Search');
        });
        
        // Basit arama formu gönderimi
        const searchForm = searchInput.closest('form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const searchTerm = searchInput.value.trim();
                
                if (searchTerm.length > 0) {
                    alert(`"${searchTerm}" için arama yapılıyor...`);
                    // Gerçek uygulamada burada arama sayfasına yönlendirme yapılabilir
                    // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
                }
            });
        }
    }
    
    // Makale listesinde hover efekti
    const articleItems = document.querySelectorAll('.article-item');
    
    articleItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transition = 'background-color 0.3s';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // "Back to top" butonu (sayfanın altına gelindiğinde görünür olur)
    const createBackToTopButton = () => {
        const button = document.createElement('button');
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        button.className = 'back-to-top';
        button.style.position = 'fixed';
        button.style.bottom = '20px';
        button.style.right = '20px';
        button.style.display = 'none';
        button.style.padding = '10px 15px';
        button.style.backgroundColor = 'var(--primary-color)';
        button.style.color = 'white';
        button.style.border = 'none';
        button.style.borderRadius = '50%';
        button.style.cursor = 'pointer';
        button.style.zIndex = '1000';
        button.style.boxShadow = '0 2px 5px rgba(0,0,0,0.2)';
        
        document.body.appendChild(button);
        
        window.addEventListener('
