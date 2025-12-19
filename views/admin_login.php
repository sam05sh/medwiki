<div class="max-w-md mx-auto mt-20 bg-white dark:bg-slate-800 p-8 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
    <h2 class="text-2xl font-bold mb-6 text-center text-admin-500">Login Amministratore</h2>
    
    <?php if(isset($login_error)): ?>
        <p class='text-red-500 text-sm mb-4 text-center'><?php echo $login_error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="action" value="login">
        <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Password</label>
            <input type="password" name="password" class="w-full p-2 rounded border dark:bg-slate-700 dark:border-slate-600 focus:outline-none focus:border-admin-500">
        </div>
        <button class="w-full bg-admin-500 hover:bg-admin-700 text-white font-bold py-2 px-4 rounded transition-colors">Accedi</button>
    </form>
    
    <div class="mt-4 text-center">
        <a href="/" class="text-sm text-slate-500 hover:underline">Torna al sito</a>
    </div>
</div>
