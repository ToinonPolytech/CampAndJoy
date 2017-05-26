<script type="text/javascript">
	loadTo('<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i("headerChat.php")); ?>', {}, "body", "prepend");
</script>
<div class="top_bar" id="top_bar">
    <div class="nav_bar w-nav" data-animation="default" data-collapse="all" data-duration="400" data-no-scroll="1">
		<div class="nav_bar_container w-container">
			<nav class="nav_menu w-nav-menu" role="navigation">
				<?php
				if (isStaff())
				{
					?>
					<div id="admin_menu" style="display:none;">
						<a class="nav_link w-nav-link logout-link" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('connexionUser.controller.php')); ?>', {}, 'body', '/demo/home', 'prepend'); return false;">Se Déconnecter <span class="nav_link_subtitle"></span></a>
						<span class="nav_link w-nav-link" href="#" onclick="$('#admin_menu').toggle('fast'); $('#client_menu').toggle('fast'); return false;">Retour<span class="nav_link_subtitle"></span></span>
						<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/administration/activites">Activités <span class="nav_link_subtitle">Créer, modifier les activités</span></a>
						<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/administration/problemesTechniques">Problèmes techniques<span class="nav_link_subtitle">Gérer les problèmes signalés</span></a>
						<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/administration/service">Vos Services<span class="nav_link_subtitle">Ajouter, modifier votre liste de restaurants, installations ect...</span></a>
						<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/administration/compte">Les comptes<span class="nav_link_subtitle">Ajouter, modifier, voir les actions...</span></a>
						<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/administration/FAQ/gestion">La FAQ<span class="nav_link_subtitle">Ajouter ou modifier des question fréquemment posées</span></a>
					</div>					
					<?php
				}
				?>
				<div id="client_menu">
					<a class="nav_link w-nav-link logout-link" href="#" onclick="loadTo('<?php echo str_replace($_SERVER['DOCUMENT_ROOT'], '', i('connexionUser.controller.php')); ?>', {}, 'body', 'prepend'); return false;">Se Déconnecter <span class="nav_link_subtitle"></span></a>
					<?php if (isStaff()){ ?><span class="nav_link w-nav-link" href="#" onclick="$('#admin_menu').toggle('fast'); $('#client_menu').toggle('fast'); return false;">Administration<span class="nav_link_subtitle"></span></span><?php } ?>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/home">Accueil <span class="nav_link_subtitle">Revenir à la page principale</span></a>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/compte">Mon compte <span class="nav_link_subtitle">Historiques, Comptes affiliés...</span></a>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/planning">Activités <span class="nav_link_subtitle">Les activités du camping</span></a>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/service">Nos Services <span class="nav_link_subtitle">Restaurants, Partenaires, réservations...</span></a>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/probleme">Nous contacter<span class="nav_link_subtitle">Nous contacter directement, pour tout problème ou question</span></a>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/leCamping">Le camping<span class="nav_link_subtitle">Consulter les informations générales : règlement, présentation de l'équipe ...</span></a>
					<a class="nav_link w-nav-link" href="/demo/<?php echo LANG_USER; ?>/equipe/mesEquipes">Mes équipes<span class="nav_link_subtitle">Consulter et gérer ses équipes</span></a>
				</div>
			</nav>
			<div class="menu-button w-nav-button" data-ix="onmenubtnhover">
				<div class="w-icon-nav-menu"></div>
			</div>
			<div class="logo">
				<h2 class="black_colors site_name" data-ix="onload">CampAndJoy&nbsp;<span class="page_name">Accueil</span></h2>
			</div>
		</div>
    </div>
</div>
<script type="text/javascript">
	$("#top_bar").removeAttr("data-ix");
</script>
<main class="page_bg_color page_container">