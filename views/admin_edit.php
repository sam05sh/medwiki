<div class="max-w-4xl mx-auto">
    <a href="/admin" class="text-sm text-slate-500 mb-4 inline-flex items-center gap-1 hover:text-admin-500 hover:underline transition-colors">
        <span>←</span> Torna alla Dashboard
    </a>
    
    <div class="flex justify-between items-end mb-6">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
            <?php echo $editData ? 'Modifica Patologia' : 'Nuova Patologia'; ?>
        </h1>
        <?php if($editData): ?>
            <span class="text-xs text-slate-400 font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">ID: <?php echo $editData['id']; ?></span>
        <?php endif; ?>
    </div>
    
    <form method="POST" class="bg-white dark:bg-slate-800 p-8 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 space-y-6">
        <input type="hidden" name="action" value="save_malattia">
        <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-100 dark:border-slate-700">
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700 dark:text-slate-300">Nome Patologia <span class="text-red-500">*</span></label>
                <input type="text" name="nome" value="<?php echo htmlspecialchars($editData['nome'] ?? ''); ?>" required 
                       class="w-full p-3 border rounded-lg dark:bg-slate-700 dark:border-slate-600 focus:ring-2 focus:ring-admin-500 focus:outline-none transition-all font-medium text-lg">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2 text-slate-700 dark:text-slate-300">Categoria <span class="text-red-500">*</span></label>
                <select name="categoria_id" required class="w-full p-3 border rounded-lg dark:bg-slate-700 dark:border-slate-600 focus:ring-2 focus:ring-admin-500 focus:outline-none transition-all">
                    <?php echo getCategoryOptions($conn, null, '', $editData['categoria_id'] ?? null); ?>
                </select>
            </div>
        </div>

        <hr class="dark:border-slate-700 border-slate-100">
        
        <div class="space-y-6">
            <div class="relative">
                <label class="block text-sm font-bold mb-2 text-medical-600 dark:text-medical-400">Descrizione Generale</label>
                <textarea name="descrizione" rows="4" class="w-full p-4 border rounded-lg dark:bg-slate-700 dark:border-slate-600 focus:border-medical-500 focus:ring-1 focus:ring-medical-500 outline-none transition-colors"><?php echo htmlspecialchars($editDetails['Descrizione'] ?? ''); ?></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold mb-2 text-orange-500">Sintomi</label>
                    <textarea name="sintomi" rows="5" class="w-full p-4 border rounded-lg dark:bg-slate-700 dark:border-slate-600 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 outline-none transition-colors"><?php echo htmlspecialchars($editDetails['Sintomi'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2 text-blue-500">Diagnosi</label>
                    <textarea name="diagnosi" rows="5" class="w-full p-4 border rounded-lg dark:bg-slate-700 dark:border-slate-600 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-colors"><?php echo htmlspecialchars($editDetails['Diagnosi'] ?? ''); ?></textarea>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold mb-2 text-green-500">Terapia</label>
                <textarea name="terapia" rows="3" class="w-full p-4 border rounded-lg dark:bg-slate-700 dark:border-slate-600 focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition-colors"><?php echo htmlspecialchars($editDetails['Terapia'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="pt-4">
            <button class="w-full bg-admin-500 hover:bg-admin-700 text-white font-bold py-4 rounded-lg text-lg transition-all shadow-lg shadow-admin-500/30 flex justify-center items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Salva Patologia
            </button>
        </div>
    </form>
</div>
