<!DOCTYPE html>
<html lang="it" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedWiki - Enciclopedia</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=view_sidebar" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=dark_mode" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: { colors: {
                medical: { 50: '#f0fdfa', 100: '#ccfbf1', 500: '#14b8a6', 700: '#0f766e', 900: '#134e4a' },
                admin: { 500: '#6366f1', 700: '#4338ca' }
            }}}
        }
    </script>
    <style>
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 3px; }
        .dark .custom-scroll::-webkit-scrollbar-thumb { background-color: #475569; }
        
        /* Stile di base per la sidebar (espansa) */
        #sidebar.sidebar-expanded {
            width: 280px; /* Larghezza standard */
            transition: width 0.3s ease, padding 0.3s ease;
            overflow-x: hidden; /* Evita scrollbar inutili */
        }

        /* Stile per la sidebar compressa (MINIMIZZATA) */
        #sidebar.sidebar-collapsed {
            width: 60px; /* Larghezza minima per contenere solo l'icona */
            padding: 15px 10px; /* Riduce il padding per stringere */
            transition: width 0.3s ease, padding 0.3s ease;
        }

        /* Nasconde il titolo e il contenuto quando è compressa */
        #sidebar.sidebar-collapsed #sidebarTitle,
        #sidebar.sidebar-collapsed #sidebarContent {
            display: none;
        }

        /* Aggiustamenti per il pulsante */
        #sidebarToggle {
            display: flex;
            align-items: center;
            /* Assicurati che il pulsante sia visibile e ben posizionato */
            width: 100%;
            justify-content: flex-start;
        }

        /* Stile per l'icona e il testo nel pulsante (se hai usato la struttura sopra) */
        #sidebarToggle .material-symbols-outlined {
            /* Stili per l'icona */
        }

    </style>
</head>
<body class="bg-slate-50 text-slate-800 dark:bg-slate-900 dark:text-slate-200 font-sans transition-colors duration-300">
    <nav class="fixed top-0 w-full z-50 border-b border-slate-200 dark:border-slate-800 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md h-16 flex items-center justify-between px-4 lg:px-8">
        <div class="flex items-center gap-4">
            <?php if(isset($_SESSION['is_admin']) && $page == 'admin'): ?>
            <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="p-2 text-slate-600 dark:text-slate-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <?php endif; ?>
            <a href="/" class="text-3xl font-bold tracking-tight text-medical-700 dark:text-medical-500 flex items-center gap-2">
                MedWiki
            </a>
        </div>
        <?php if ($page != 'admin'): ?>
        <a href=""> Definizioni </a>
        
        <a href=""> Casi </a>
        
        <a href=""> Malattie </a>
        <div class="flex items-center gap-4">
            <form action="/" method="GET" class="hidden md:block relative w-96">
                <input type="text" name="q" placeholder="Cerca..." class="w-full bg-slate-100 dark:bg-slate-800 border-none rounded-full py-2 px-4 pl-10 focus:ring-2 focus:ring-medical-500 outline-none">
            </form>
            <button id="darkModeToggle" title="Change Theme color"><span class="material-symbols-outlined">dark_mode</span></button>
            <!-- <a href="/admin" class="p-2 rounded-full hover:bg-slate-100 text-slate-500 hover:text-admin-500">Admin</a> -->
        </div>
        <?php endif; ?>
		<?php if(isset($_SESSION['is_admin']) && $page == 'admin'): ?>
			<form method="POST" class="inline"><input type="hidden" name="action" value="logout"><button class="text-xs text-red-500 hover:underline">Esci dalla sezione admin</button></form>
		<?php endif; ?>
    </nav>
    <div class="pt-16 flex min-h-screen">
