<?php

// directories
const MODULES_DIR = "./app/modules/";
const CONFIG_DIR= "./app/config/";
const DATA_DIR  = "./app/data/";

$config  = load_configs();
$modules = load_modules($config['modules']);

if (empty($modules)) {
    error_message("Empty modules directory", __LINE__);
} else {
    file_put_contents(DATA_DIR . "data.json", json_encode($modules, JSON_PRETTY_PRINT));
}

function load_configs() {
    if (file_exists(CONFIG_DIR . 'config.json')) {
        $config = file_get_contents(CONFIG_DIR . 'config.json');
        $config = json_decode($config, true);
        
        return $config;
    } else {
        error_message("config file don't exist.", __LINE__);
    }
}

function load_modules($modules) {
    success_message("Carregando módulos...");
    $modules_map = [];
    foreach ($modules as $module => $status) {
        if ($status) {
            map_modules($module, $modules_map);
        }
    }

    return isset($modules_map) ? $modules_map : [];
}

function map_modules($module, &$modules_map) {
    success_message("  Importando módulos...");
    switch ($module) {
        case "posts": return map_posts($modules_map); break;
    }

}
    
function map_posts(&$modules_map) {
    $posts_dir = $posts['path'] = MODULES_DIR . "posts/";
    
    $modules_map['posts'] = map_dir($posts_dir);
    success_message('    Posts importados com sucesso.');
}

function map_dir ($path) {
    $map = null;
    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            
            $file_path = $path . $file;
            
            if (is_dir($file_path))
            {
                $map[$file . '/'] = map_dir($file_path . "/");
            } else {
                $map[$file]["name"] = $file;
                $map[$file]["file_type"] = filetype($file_path);
                $map[$file]["modification_time"] = filemtime($file_path);
            }             
        }
        
        return $map;
    } else {
        error_message("\$path não é um diretório.", __LINE__);
    }
}

function error_message($error, $line = "?") {
    echo ("Error on line " . $line . ": " . $error . "\n");
    die();
}

function success_message($msg) {
    echo $msg . "\n";
}