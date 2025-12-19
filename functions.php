<?php
// functions.php

// Funzione per creare slug URL-friendly (es. "Mal di Gola" -> "mal-di-gola")
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return strtolower(trim($text, '-'));
}

/**
 * Costruisce l'albero passando il "path" accumulato dai genitori.
 * $currentPathUrl: es. "/it/cardiologia"
 */
function costruisciAlbero($conn, $parentId = null, $activeId = null, $currentPathUrl = '') {
    // Se è la radice, il path base è vuoto (o solo lingua gestita altrove, ma qui lo riceviamo pulito)
    $sql = "SELECT * FROM categorieMalattie WHERE parent_id " . ($parentId === null ? "IS NULL" : "= " . (int)$parentId) . " ORDER BY nome ASC";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo '<ul class="pl-4 border-l border-slate-200 dark:border-slate-700 ml-2 space-y-1">';
        while($row = $result->fetch_assoc()) {
            echo '<li>';
            
            // Calcoliamo il path per QUESTA categoria
            // Se lo slug è vuoto (vecchi record), usiamo slugify sul nome al volo
            $catSlug = !empty($row['slug']) ? $row['slug'] : slugify($row['nome']);
            $thisCatPath = $currentPathUrl . '/' . $catSlug;

            $checkChild = $conn->query("SELECT id FROM categorieMalattie WHERE parent_id = " . $row['id']);
            $hasChild = $checkChild->num_rows > 0;

            if ($hasChild) {
                echo '<details class="group" id="cat-details-'.$row['id'].'">';
                echo '<summary class="flex justify-between items-center py-1 px-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded cursor-pointer select-none text-sm transition-colors">';
                echo '<span>' . htmlspecialchars($row['nome']) . '</span>';
                echo '<span class="text-xs text-slate-400 group-open:rotate-180 transition-transform">▼</span>';
                echo '</summary>';
                // RICORSIONE: Passiamo il path aggiornato ai figli
                costruisciAlbero($conn, $row['id'], $activeId, $thisCatPath);
                echo '</details>';
            } else {
                // Categoria finale: stampiamo il nome e cerchiamo le malattie
                echo '<div class="py-1 px-2 text-slate-700 dark:text-slate-200 font-medium text-sm">' . htmlspecialchars($row['nome']) . '</div>';
                
                $malattie = $conn->query("SELECT id, nome, slug FROM malattie WHERE categoria_id = " . $row['id']);
                if($malattie->num_rows > 0){
                   echo '<ul class="pl-2 mt-1">';
                   while($m = $malattie->fetch_assoc()){
                       $mSlug = !empty($m['slug']) ? $m['slug'] : slugify($m['nome']);
                       // URL COMPLETO: /it + path_categorie + slug_malattia
                       $fullUrl = "/it" . $thisCatPath . "/" . $mSlug;
                       
                       $isActive = ($activeId == $m['id']);
                       $activeClass = $isActive 
                            ? "bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 font-bold border-r-2 border-teal-500" 
                            : "text-teal-600 dark:text-teal-400 hover:underline";
                       $linkId = $isActive ? 'id="active-disease-link"' : '';

                       echo '<li><a href="'.$fullUrl.'" '.$linkId.' class="block py-1 px-2 text-xs '.$activeClass.'">'.htmlspecialchars($m['nome']).'</a></li>';
                   }
                   echo '</ul>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
    }
}

// Helper per le select (invariato)
function getCategoryOptions($conn, $parentId = null, $prefix = '', $selectedId = null) {
    $sql = "SELECT * FROM categorieMalattie WHERE parent_id " . ($parentId === null ? "IS NULL" : "= " . (int)$parentId);
    $res = $conn->query($sql);
    $html = '';
    while ($row = $res->fetch_assoc()) {
        $selected = ($row['id'] == $selectedId) ? 'selected' : '';
        $html .= '<option value="'.$row['id'].'" '.$selected.'>'.$prefix . htmlspecialchars($row['nome']).'</option>';
        $html .= getCategoryOptions($conn, $row['id'], $prefix . '&mdash; ', $selectedId);
    }
    return $html;
}

function renderDeleteButton($id) {
    ?>
    <form method="POST" onsubmit="return confirm('Eliminare questa categoria?');" class="flex items-center">
        <input type="hidden" name="action" value="delete_category">
        <input type="hidden" name="cat_id" value="<?php echo (int)$id; ?>">
        <button type="submit" 
                class="text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 w-6 h-6 rounded flex items-center justify-center transition-colors" 
                title="Elimina">
            ✕
        </button>
    </form>
    <?php
}

function renderCategoryLevel($parentId, $level, $category_tree) {
    if (!isset($category_tree[$parentId])) return;

    foreach ($category_tree[$parentId] as $cat) {
        $indent = $level * 4; // Increases padding-left based on depth
        $isParent = ($level === 0);
        ?>
        
        <div class="flex justify-between items-center text-sm border-b dark:border-slate-700 pb-1 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/30 p-1 rounded" 
             style="padding-left: <?php echo $indent; ?>px;">
            
            <span class="truncate <?php echo $isParent ? 'font-semibold text-slate-700 dark:text-slate-300' : 'text-slate-500'; ?>">
                <?php 
                    echo ($level > 0 ? str_repeat('  ', $level) . '↳ ' : ''); 
                    echo htmlspecialchars($cat['nome']); 
                ?>
            </span>

            <form method="POST" onsubmit="return confirm('Eliminare questa categoria?');" class="flex items-center">
                <input type="hidden" name="action" value="delete_category">
                <input type="hidden" name="cat_id" value="<?php echo (int)$cat['id']; ?>">
                <button type="submit" class="text-red-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 w-6 h-6 rounded flex items-center justify-center transition-colors">
                    ✕
                </button>
            </form>
        </div>

        <?php
        // RECURSION: Look for children of the category we just printed
        renderCategoryLevel($cat['id'], $level + 1, $category_tree);
    }
}

?>
