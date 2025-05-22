<?php
$baseUrl = "/";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>SGC - Sistema de Gestión de Cambios</title> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        #logo-link-header,
        #logout-link-header {
            transition: transform 0.3s ease-in-out;
        }
        @media (min-width: 768px) {
            #header.header-shifted #logo-link-header,
            #header.header-shifted #logout-link-header {
                transform: translateX(-16rem);
            }
        }
    </style>
</head>
<body class="bg-gray-50">

    <aside id="sidebar" class="w-64 bg-gray-800 text-white min-h-screen p-4 space-y-4 transition-transform duration-300 fixed left-0 top-0 transform -translate-x-full z-30">
        <h4 class="text-xl font-bold mb-4 text-center border-b border-gray-700 pb-2">SGC Menú</h4>
        <a href="<?php echo $baseUrl; ?>index.php?c=Metodologia&a=index" class="block py-2.5 px-4 rounded hover:bg-gray-700">
            <i class="fas fa-tachometer-alt mr-2"></i>Metodologías
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=Proyecto&a=index" class="block py-2.5 px-4 rounded hover:bg-gray-700 ">
            <i class="fas fa-project-diagram mr-2"></i>Proyectos
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=SolicitudCambio&a=crear" class="block py-2.5 px-4 rounded hover:bg-gray-700 ">
            <i class="fas fa-file-alt mr-2"></i>Nueva Solicitud
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=SolicitudCambio&a=listar" class="block py-2.5 px-4 rounded hover:bg-gray-700">
            <i class="fas fa-tasks mr-2"></i>Gestionar SC
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=OrdenCambio&a=listar" class="block py-2.5 px-4 rounded hover:bg-gray-700 ">
            <i class="fas fa-cogs mr-2"></i>Órdenes de Cambio
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=Ecs&a=gestionar" class="block py-2.5 px-4 rounded hover:bg-gray-700 ">
            <i class="fas fa-archive mr-2"></i>Repositorio ECS
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=Reporte&a=index" class="block py-2.5 px-4 rounded hover:bg-gray-700 ">
            <i class="fas fa-chart-bar mr-2"></i>Reportes
        </a>
        <a href="<?php echo $baseUrl; ?>index.php?c=Usuario&a=gestionar" class="block py-2.5 px-4 rounded hover:bg-gray-700 ">
            <i class="fas fa-users-cog mr-2"></i>Gestionar Usuarios
        </a>
        </aside>

    <header id="header" class="bg-gray-100 p-4 shadow flex items-center justify-between fixed top-0 left-0 w-full z-20 transition-all duration-300">
        <div class="flex items-center space-x-2">
            <button id="hamburger-btn" class="bg-gray-100 text-black focus:outline-none p-2 rounded-md hover:bg-gray-200" title="Abrir menú">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <a href="#" onclick="history.back(); return false;" title="Atrás" class="p-2 rounded-md hover:bg-gray-200">
                 <img src="<?php echo $baseUrl; ?>assets/imagenes/atras.png" alt="Atrás" class="h-8 w-8" />
            </a>
        </div>

        <a href="/views/DashboardVista.php" id="logo-link-header" class="flex justify-center">
             <img src="<?php echo $baseUrl; ?>assets/imagenes/logo_sgc_placeholder.png" alt="Logo SGC" class="h-12" />
        </a>

        <a href="<?php echo $baseUrl; ?>index.php?c=Login&a=cerrarSesion" id="cerrarSesion-link-header"
           class="bg-gray-800 text-white font-semibold py-2 px-4 rounded hover:bg-gray-700 transition whitespace-nowrap">
            Cerrar Sesión
        </a>
    </header>

    <div id="content-wrapper" class="pt-20 transition-all duration-300">

        <main class="flex-grow p-4 md:p-8">
            <script>
        const menuBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const header = document.getElementById('header'); 
        const contentWrapper = document.getElementById('content-wrapper'); 

        
        if (menuBtn && sidebar && header && contentWrapper) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                header.classList.toggle('md:ml-64');
                contentWrapper.classList.toggle('md:ml-64');
                header.classList.toggle('header-shifted');
            });

            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
                header.classList.remove('md:ml-64', 'header-shifted'); 
                contentWrapper.classList.remove('md:ml-64');
            } else {
                if (sidebar.classList.contains('-translate-x-full')) {
                     header.classList.remove('md:ml-64', 'header-shifted');
                     contentWrapper.classList.remove('md:ml-64');
                } else {
                     header.classList.add('md:ml-64', 'header-shifted');
                     contentWrapper.classList.add('md:ml-64');
                }
            }
        } else {
            console.error("Error JS: Faltan elementos (hamburger-btn, sidebar, header, content-wrapper). Verifica IDs.");
        }
    </script>
    