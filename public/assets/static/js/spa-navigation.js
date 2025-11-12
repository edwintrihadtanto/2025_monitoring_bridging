document.addEventListener('DOMContentLoaded', function() {
    // Tangani klik pada sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar-link[data-page]');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const url = this.getAttribute('href');
            const page = this.getAttribute('data-page');
            
            // Tambahkan loading state
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Update active menu
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.classList.remove('active');
            });
            this.closest('.sidebar-item').classList.add('active');
            
            // Fetch content via AJAX
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;
                
                // Update URL in browser without page reload
                history.pushState({page: page}, '', url);
                
                // Reinitialize any JavaScript components that might be needed
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mainContent.innerHTML = '<div class="alert alert-danger">Error loading content. Please try again.</div>';
            });
        });
    });
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function(e) {
        if (e.state && e.state.page) {
            const url = window.location.href;
            
            // Tambahkan loading state
            const mainContent = document.getElementById('main-content');
            mainContent.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
            
            // Fetch content via AJAX
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                mainContent.innerHTML = html;
                
                // Update active menu
                document.querySelectorAll('.sidebar-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                const activeLink = document.querySelector(`.sidebar-link[data-page="${e.state.page}"]`);
                if (activeLink) {
                    activeLink.closest('.sidebar-item').classList.add('active');
                }
                
                // Reinitialize any JavaScript components that might be needed
                if (typeof initializePageComponents === 'function') {
                    initializePageComponents();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mainContent.innerHTML = '<div class="alert alert-danger">Error loading content. Please try again.</div>';
            });
        }
    });
});

// Function to reinitialize page-specific components
function initializePageComponents() {
    // Reinitialize DataTables if needed
    if (typeof simpleDatatables !== 'undefined') {
        const datatables = document.querySelectorAll('.datatable');
        datatables.forEach(table => {
            if (!table.DataTable) {
                new simpleDatatables.DataTable(table);
            }
        });
    }
    
    // Add any other component initializations here
}