<?php
session_start();
require 'config.php';
require 'functions.php';

// --- 1. ROUTING AVANZATO ---
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Usa array_filter per rimuovere segmenti vuoti (risolve il problema della home page)
$segments = array_values(array_filter(explode('/', trim($requestUri, '/'))));

// Gestione Lingua (Default 'it')
$lang = 'it';
if (!empty($segments[0]) && strlen($segments[0]) == 2) {
    $lang = array_shift($segments); // Rimuove 'it' dall'array
}

// Definisci la pagina in base ai segmenti rimasti
$page = 'home';
$subpage = '';
$currentDiseaseId = null;
$adminEditId = null;

// CASO 1: Home Page (nessun segmento rimasto)
if (empty($segments)) {
    $page = 'home';
}
// CASO 2: Sezione Admin
elseif ($segments[0] == 'admin') {
    $page = 'admin';
    $subpage = $segments[1] ?? '';
    $adminEditId = $segments[2] ?? null; 
}
// CASO 3: Pagina Malattia (cerca per slug)
else {
    $lastSlug = end($segments);
    
    // Cerchiamo nel DB tramite SLUG
    $stmt = $conn->prepare("SELECT id FROM malattie WHERE slug = ? LIMIT 1");
    $stmt->bind_param("s", $lastSlug);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $page = 'malattia';
        $currentDiseaseId = $row['id'];
    } else {
        $page = '404';
    }
}

// --- 2. LOGICA POST (LOGIN, SALVATAGGIO, ECC.) ---
// Questa parte deve essere esguita PRIMA di caricare l'HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    
    // LOGIN
    if ($act === 'login') {
        if (isset($_POST['password']) && $_POST['password'] === $admin_password) {
            $_SESSION['is_admin'] = true;
            header("Location: /admin"); 
            exit;
        } else {
            $login_error = "Password errata";
        }
    }

    // LOGOUT
    if ($act === 'logout') {
        session_destroy();
        header("Location: /"); 
        exit;
    }
    
    // AZIONI ADMIN (Solo se loggato)
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        
        // Salva Malattia
        if ($act === 'save_malattia') {
            $id = $_POST['id'] ?? null;
            $nome = $conn->real_escape_string($_POST['nome']);
            $cat_id = (int)$_POST['categoria_id'];
            
            // Genera slug
            $slug = slugify($nome);
            
            $dettagli = json_encode(array_filter([
                "Descrizione" => $_POST['descrizione'] ?? '',
                "Sintomi" => $_POST['sintomi'] ?? '',
                "Diagnosi" => $_POST['diagnosi'] ?? '',
                "Terapia" => $_POST['terapia'] ?? ''
            ]), JSON_UNESCAPED_UNICODE);

            if ($id) {
                $stmt = $conn->prepare("UPDATE malattie SET nome=?, slug=?, categoria_id=?, dettagli=? WHERE id=?");
                $stmt->bind_param("ssisi", $nome, $slug, $cat_id, $dettagli, $id);
            } else {
                $stmt = $conn->prepare("INSERT INTO malattie (nome, slug, categoria_id, dettagli) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssis", $nome, $slug, $cat_id, $dettagli);
            }

            $stmt->execute();
            header("Location: /admin"); exit;
        }

        // Elimina Malattia
        if ($act === 'delete_malattia') {
            $conn->query("DELETE FROM malattie WHERE id=".(int)$_POST['id']);
            header("Location: /admin"); exit;
        }

        // Aggiungi Categoria
        if ($act === 'add_category') {
            $nome = $conn->real_escape_string($_POST['cat_nome']);
            $slug = slugify($nome);
            $parent = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : "NULL";
            $conn->query("INSERT INTO categorieMalattie (nome, slug, parent_id) VALUES ('$nome', '$slug', $parent)");
            header("Location: /admin"); exit;
        }

        // Elimina Categoria
        if ($act === 'delete_category') {
            $cat_id = (int)$_POST['cat_id'];
            $check = $conn->query("SELECT id FROM malattie WHERE categoria_id=$cat_id UNION SELECT id FROM categorieMalattie WHERE parent_id=$cat_id");
            if ($check->num_rows == 0) {
                $conn->query("DELETE FROM categorieMalattie WHERE id=$cat_id");
            }
            header("Location: /admin"); exit;
        }
    }
}

// --- 3. GESTIONE VISTE (OUTPUT HTML) ---
include 'includes/header.php';


if ($page == 'admin') {
    // Passiamo l'ID corrente per aprire l'albero al punto giusto
    $activeId = isset($currentDiseaseId) ? $currentDiseaseId : null;
    
    // Nota: include sidebar direttamente qui dentro per avere accesso a $activeId
    include 'includes/sidebar.php';
}

echo '<main class="flex-1 p-4 lg:p-8 w-full max-w-5xl mx-auto">';

if ($page == 'home') {
    include 'views/home.php';
} 
elseif ($page == 'malattia' && $currentDiseaseId) {
    $id = $currentDiseaseId; 
    include 'views/malattia.php';
} 
elseif ($page == 'admin') {
    if (!isset($_SESSION['is_admin'])) {
        include 'views/admin_login.php';
    } else {
        // Router interno Admin
        if ($subpage === 'edit' || $subpage === 'new') {
            $editData = null; 
            $editDetails = [];
            if ($subpage === 'edit' && $adminEditId) {
                $res = $conn->query("SELECT * FROM malattie WHERE id=".(int)$adminEditId);
                $editData = $res->fetch_assoc();
                $editDetails = json_decode($editData['dettagli'], true);
            }
            include 'views/admin_edit.php';
        } else {
            // Dashboard principale
            $allMalattie = $conn->query("SELECT m.*, c.nome as cat_nome FROM malattie m LEFT JOIN categorieMalattie c ON m.categoria_id = c.id ORDER BY m.id DESC");
            $allCats = $conn->query("SELECT * FROM categorieMalattie ORDER BY parent_id ASC, nome ASC");

            $all_rows = [];
            while($row = $allCats->fetch_assoc()) {
                $all_rows[] = $row;
            }

            $category_tree = [];
            foreach ($all_rows as $cat) {
                // If parent_id is NULL, we group it under the key 'root'
                $parentId = ($cat['parent_id'] === null) ? 'root' : $cat['parent_id'];
                $category_tree[$parentId][] = $cat;
            }

            include 'views/admin_dashboard.php';

        }
    }
} else {
    // 404
    echo "<div class='text-center py-20'>
            <h1 class='text-6xl font-bold text-slate-200 dark:text-slate-700'>404</h1>
            <p class='text-xl mt-4'>Pagina non trovata.</p>
            <a href='/' class='mt-6 inline-block text-medical-500 hover:underline'>Torna alla Home</a>
          </div>";
}

echo '</main>';
include 'includes/footer.php';
?>
