<a class="nav_link w-nav-link logout-link" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('connexionUser.controller.php')); ?>', {}, 'body', '/demo/home', 'prepend'); return false;">Se Déconnecter <span class="nav_link_subtitle"></span></a>
<a class="nav_link w-nav-link" href="/demo/home" onclick="loadToMain('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('accueil.php')); ?>', {}, '/demo/home'); return false;">Accueil <span class="nav_link_subtitle">Revenir à la page principale</span></a>
<a class="nav_link w-nav-link" href="/demo/planning" onclick="loadToMain('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('activitesCamping.php')); ?>', {}, '/demo/planning'); return false;">Activités <span class="nav_link_subtitle">Les activités du camping</span></a>
<a class="nav_link w-nav-link" href="/demo/service" onclick="loadToMain('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('services.php')); ?>', {}, '/demo/service'); return false;">Nos Services <span class="nav_link_subtitle">Restaurants, Partenaires, réservations...</span></a>