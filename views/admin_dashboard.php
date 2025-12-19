<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Dashboard Admin</h1>
    <a href="/admin/new" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg flex items-center gap-2 font-bold shadow-lg shadow-green-500/20 transition-all">
        <span>+</span> Nuova Malattia
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 font-bold text-slate-500 text-sm uppercase">Elenco Malattie</div>
            <div class="max-h-[600px] overflow-y-auto custom-scroll">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 dark:bg-slate-700 sticky top-0">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nome</th>
                            <th class="px-4 py-3">Categoria</th>
                            <th class="px-4 py-3 text-right">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($m = $allMalattie->fetch_assoc()): ?>
                        <tr class="border-b dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <td class="px-4 py-3 text-slate-400 font-mono"><?php echo $m['id']; ?></td>
                            <td class="px-4 py-3 font-medium text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($m['nome']); ?></td>
                            <td class="px-4 py-3 text-slate-500"><?php echo htmlspecialchars($m['cat_nome']); ?></td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="/admin/edit/<?php echo $m['id']; ?>" class="text-blue-500 hover:underline font-medium">Modifica</a>
                                <form method="POST" class="inline" onsubmit="return confirm('Sicuro di voler eliminare definitivamente <?php echo addslashes($m['nome']); ?>?');">
                                    <input type="hidden" name="action" value="delete_malattia">
                                    <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                    <button class="text-red-500 hover:underline">Elimina</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div>
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6 mb-6">
            <h3 class="font-bold mb-4 text-slate-700 dark:text-slate-300 border-b pb-2 dark:border-slate-700">Nuova Categoria</h3>
            <form method="POST" class="space-y-3">
                <input type="hidden" name="action" value="add_category">
                
                <input type="text" name="cat_nome" placeholder="Nome Categoria" required 
                       class="w-full p-2 border rounded dark:bg-slate-700 dark:border-slate-600 focus:border-admin-500 focus:outline-none transition-colors">
                
                <select name="parent_id" class="w-full p-2 border rounded dark:bg-slate-700 dark:border-slate-600 text-sm focus:border-admin-500 focus:outline-none">
                    <option value="">-- Nessun Genitore (Root) --</option>
                    <?php echo getCategoryOptions($conn); ?>
                </select>
                
                <button class="w-full bg-slate-700 hover:bg-slate-600 text-white py-2 rounded text-sm font-medium transition-colors">
                    Salva Categoria
                </button>
            </form>
        </div>

        
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-bold mb-4 text-slate-700 dark:text-slate-300 border-b pb-2 dark:border-slate-700">Struttura Categorie</h3>

            <div class="space-y-2 max-h-[250px] overflow-y-auto pr-2 custom-scroll">
                <?php 
                if (isset($category_tree['root'])) {
                    renderCategoryLevel('root', 0, $category_tree);
                } else {
                    echo '<p class="text-sm text-slate-500 text-center py-4">Nessuna categoria trovata.</p>';
                }
                ?>
            </div>

            <p class="text-xs text-slate-400 mt-3 text-center italic">Puoi eliminare solo categorie vuote.</p>
        </div>
    </div>
</div>
