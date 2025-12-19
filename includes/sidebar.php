<aside id="sidebar" class="sidebar-expanded bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200 font-sans transition-colors duration-300">
    
    <div class="flex justify-start items-center mb-2">
        <button id="sidebarToggle" class="bg-transparent border-none text-white cursor-pointer p-0 flex items-center">
            <span class="material-symbols-outlined text-slate-700 dark:text-slate-200 font-medium text-2xl">view_sidebar</span>
            <span id="sidebarTitle" class="py-1 px-2 text-slate-700 dark:text-slate-200 font-medium text-sm">Malattie</span>
        </button>
    </div>

    <div id="sidebarContent">
    <?php 
        // Usiamo la variabile globale definita nel router
        $activeId = isset($currentDiseaseId) ? $currentDiseaseId : null;
        costruisciAlbero($conn, null, $activeId); 
    ?>
    </div>
</aside>
