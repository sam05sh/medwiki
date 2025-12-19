<?php
// --- LOGICA SPECIFICA HOME ---

// 1. GESTIONE RICERCA
$searchResults = [];
$searchQuery = '';

if (isset($_GET['q'])) {
    $searchQuery = trim($_GET['q']);
    if (!empty($searchQuery)) {
        $safeQ = $conn->real_escape_string($searchQuery);
        // Facciamo una JOIN per prendere anche lo slug della categoria, così l'URL è più carino
        $sql = "SELECT m.*, c.slug as cat_slug, c.nome as cat_nome 
                FROM malattie m 
                LEFT JOIN categorieMalattie c ON m.categoria_id = c.id 
                WHERE m.nome LIKE '%$safeQ%' 
                LIMIT 10";
        $res = $conn->query($sql);
        while($r = $res->fetch_assoc()) {
            $searchResults[] = $r;
        }
    }
}

// 2. GESTIONE TOP MALATTIE (Più consultate)
$topMalattie = [];
// Anche qui JOIN per avere lo slug della categoria
$res = $conn->query("SELECT m.*, c.slug as cat_slug 
                     FROM malattie m 
                     LEFT JOIN categorieMalattie c ON m.categoria_id = c.id 
                     ORDER BY m.visite DESC 
                     LIMIT 6");
if($res) {
    while($r = $res->fetch_assoc()) {
        $topMalattie[] = $r;
    }
}
?>

<div class="text-center py-12 lg:py-20 animate-fade-in">
    <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 dark:text-white mb-6">
        Enciclopedia <span class="text-transparent bg-clip-text bg-gradient-to-r from-medical-500 to-blue-600">Medica</span>
    </h1>
    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-2xl mx-auto">
        Cerca sintomi, diagnosi e terapie in modo rapido e intuitivo.
    </p>

    <form action="/" method="GET" class="relative max-w-xl mx-auto mb-12">
        <input type="text" name="q" 
               value="<?php echo htmlspecialchars($searchQuery); ?>" 
               placeholder="Cerca es. 'Ipertensione'..." 
               class="w-full text-lg shadow-xl shadow-medical-500/10 border border-slate-200 dark:border-slate-700 rounded-2xl py-4 px-6 pl-14 focus:ring-4 focus:ring-medical-500/30 outline-none bg-white dark:bg-slate-800 transition-all text-slate-800 dark:text-slate-200"
               autocomplete="off">
        <svg class="w-6 h-6 absolute left-5 top-5 text-medical-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </form>

    <?php if(!empty($searchResults)): ?>
        <div class="text-left max-w-xl mx-auto mb-12">
            <h3 class="text-sm font-bold text-slate-400 mb-2 uppercase tracking-wide">Risultati Ricerca</h3>
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
                <?php foreach($searchResults as $res): 
                    // Calcolo URL: /it/slug-categoria/slug-malattia
                    $mSlug = !empty($res['slug']) ? $res['slug'] : slugify($res['nome']);
                    $cSlug = !empty($res['cat_slug']) ? $res['cat_slug'] : 'generale';
                    $url = "/it/" . $cSlug . "/" . $mSlug;
                ?>
                    <a href="<?php echo $url; ?>" class="block px-6 py-3 border-b border-slate-100 dark:border-slate-700 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-medical-600 dark:text-medical-400 group-hover:text-medical-700 dark:group-hover:text-medical-300">
                                <?php echo htmlspecialchars($res['nome']); ?>
                            </span>
                            <span class="text-xs text-slate-400 font-light bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-full">
                                <?php echo htmlspecialchars($res['cat_nome'] ?? 'Generale'); ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php elseif(isset($_GET['q']) && !empty($searchQuery)): ?>
        <div class="mb-12 text-slate-500">
            Nessun risultato trovato per "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>".
        </div>
    <?php endif; ?>
</div>

<div class="mb-8">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-slate-800 dark:text-white">
        <span class="text-orange-500">🔥</span> Più Consultate
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($topMalattie as $tm): 
             // Calcolo URL
             $mSlug = !empty($tm['slug']) ? $tm['slug'] : slugify($tm['nome']);
             $cSlug = !empty($tm['cat_slug']) ? $tm['cat_slug'] : 'consultazione'; // Fallback se manca cat
             $url = "/it/" . $cSlug . "/" . $mSlug;
        ?>
        <a href="<?php echo $url; ?>" class="group block p-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-medical-500 dark:hover:border-medical-500 transition-all hover:shadow-lg hover:shadow-medical-500/10 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-medical-100 to-transparent dark:from-medical-900/20 opacity-0 group-hover:opacity-100 transition-opacity rounded-bl-full"></div>
            
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 group-hover:text-medical-600 dark:group-hover:text-medical-400 transition-colors relative z-10">
                <?php echo htmlspecialchars($tm['nome']); ?>
            </h3>
            
            <div class="mt-4 flex items-center gap-2 text-xs text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                <span><?php echo $tm['visite']; ?> visite</span>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</div>
