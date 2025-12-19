    </div> <script>
    document.addEventListener("DOMContentLoaded", () => {
        const sidebar = document.getElementById('sidebar');
        const storageKey = 'medwiki_tree_state';
        const scrollKey = 'medwiki_sidebar_scroll';

        // Ripristina stato apertura
        const savedState = JSON.parse(localStorage.getItem(storageKey) || "[]");
        savedState.forEach(id => {
            const el = document.getElementById(id);
            if(el) el.open = true;
        });

        // Auto-expand per malattia attiva
        const activeLink = document.getElementById('active-disease-link');
        if (activeLink) {
            let parent = activeLink.closest('details');
            while (parent) {
                parent.open = true;
                if (!savedState.includes(parent.id)) savedState.push(parent.id);
                parent = parent.parentElement.closest('details');
            }
            localStorage.setItem(storageKey, JSON.stringify(savedState));
        }

        // Ripristina scroll
        const savedScroll = localStorage.getItem(scrollKey);
        if(sidebar && savedScroll) sidebar.scrollTop = parseInt(savedScroll);

        // Salva stato al click
        document.querySelectorAll('details').forEach(detail => {
            detail.addEventListener('toggle', () => {
                let currentState = JSON.parse(localStorage.getItem(storageKey) || "[]");
                if(detail.open) {
                    if(!currentState.includes(detail.id)) currentState.push(detail.id);
                } else {
                    currentState = currentState.filter(id => id !== detail.id);
                }
                localStorage.setItem(storageKey, JSON.stringify(currentState));
            });
        });

        // Salva scroll
        if(sidebar) {
            sidebar.addEventListener('scroll', () => {
                localStorage.setItem(scrollKey, sidebar.scrollTop);
            });
        }
    });

    const htmlEl = document.documentElement; 
    const toggleButton = document.getElementById('darkModeToggle');

    // --- Persistence Check (on page load) ---
    // Check local storage for the saved theme
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlEl.classList.add('dark');
    } else {
        htmlEl.classList.remove('dark');
    }

    // --- Toggle Logic (on button click) ---
    toggleButton.addEventListener('click', () => {
        if (htmlEl.classList.contains('dark')) {
            // Switch to light mode
            htmlEl.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            // Switch to dark mode
            htmlEl.classList.add('dark');
            localStorage.theme = 'dark';
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleButton = document.getElementById('sidebarToggle');
    
    // Aggiunge un listener di evento al click del pulsante
    toggleButton.addEventListener('click', function() {
        // Controlla se la sidebar ha la classe 'sidebar-expanded'
        if (sidebar.classList.contains('sidebar-expanded')) {
            // Se è espansa, la comprime
            sidebar.classList.remove('sidebar-expanded');
            sidebar.classList.add('sidebar-collapsed');
            
        } else {
            // Se è compressa, la espande
            sidebar.classList.remove('sidebar-collapsed');
            sidebar.classList.add('sidebar-expanded');
        }
        
        // OPZIONALE: Puoi cambiare l'icona del pulsante
        const icon = toggleButton.querySelector('.material-symbols-outlined');
        if (icon) {
             // Qua volendo si può cambiare il secondo view_sidebar con altra icona
             icon.textContent = sidebar.classList.contains('sidebar-expanded') ? 'view_sidebar' : 'view_sidebar';
        }
    });
});



    </script>
</body>
</html>
