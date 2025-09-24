<!DOCTYPE html> <!-- irimports/includes/header.php -->

<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>IR Importados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            background-color: #060606ff;
            color: #f5f5f5;
            background-image: 
                linear-gradient(rgba(0, 0, 0, 0.58), rgba(0, 0, 0, 0.77)),
                url("assets/img/background/background.png");
            background-repeat: repeat;
            background-position: top center;
            background-size: auto;
        }
        .main-content {
            flex: 1; /* Faz o conteúdo ocupar todo espaço disponível */
            padding-top: 90px; /* Para a navbar fixa */
        }

        /* ===== NAVBAR ===== */
        .custom-navbar { background: rgba(10,10,10,0.8); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); border-bottom: 2px solid #0e72deff; box-shadow: 0 3px 10px rgba(173, 171, 171, 0.3); }
        .navbar-brand { font-family: 'Orbitron', sans-serif; font-size: 1.6rem; font-weight: 700; letter-spacing: 2px; color: #00c8ff !important; text-transform: uppercase; display: flex; align-items: center; margin-right: 0; }
        .navbar-brand img { height: 42px; width: auto; margin-right: 8px; }
        .navbar-nav .nav-link { color: #ddd !important; font-size: 0.95rem; font-weight: 600; letter-spacing: 1px; margin: 0 12px; text-transform: uppercase; position: relative; display: flex; align-items: center; transition: all 0.3s ease; }
        .navbar-nav .nav-link::after { content: ''; position: absolute; width: 0%; height: 2px; bottom: -6px; left: 50%; background: #00c8ff; box-shadow: 0 0 8px #00c8ff; transition: all 0.3s ease; }
        .navbar-nav .nav-link:hover { color: #fff !important; }
        .navbar-nav .nav-link:hover::after { width: 100%; left: 0; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top custom-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="assets/img/logo/logo.png" alt="Logo">
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house-door"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="produtos.php"><i class="bi bi-box-seam"></i> Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="contato.php"><i class="bi bi-envelope"></i> Contato</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- CONTEÚDO PRINCIPAL -->
    <div class="main-content container">
