<?php
	if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,";
	require($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		exit();
	}
	$user=new User($_SESSION["id"]);
	$cuser=new Controller_User($user);
	$database=new Database();
	if(!isStaff())
	{
		exit();
	}
?>
<div class="blur_bg"></div>
<div class="welcome_msg" data-ix="onpageload">
	<h1 class="h1_color">Les comptes</h1>
</div>
<div class="hero_section">
	<div class="w-container">
		<div class="section_title_wrapper">
			<h2 class="heading">Rechercher un compte</h2>
			<div class="section_divide"></div>
			<div class="fliter_block">
				<div class="w-form">
					<form class="horizontal_form" id="search" name="search">
						<input class="campandjoy_input w-input" id="nom" maxlength="256" name="nom" placeholder="Nom ou prénom de l'utilisateur" type="text"> 
						<select id="type" name="type" class="w-select campandjoy_input">
							<option value="-1">Le type de compte</option>
							<?php
								foreach ($listesTypes as $k => $v)
								{
									?>
									<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
								}
							?>
						</select> 
						<input class="campandjoy_input w-input" id="date_arrive" name="date_arrive" placeholder="Date d'arrivée" type="text" />
						<input class="campandjoy_input w-input" id="date_depart" name="date_depart" placeholder="Date de départ" type="text" />
						<input class="campandjoy_input w-input" id="emplacement" name="emplacement" placeholder="Emplacement" type="text" />
					</form>
					<?php if ($cuser->can("CAN_CREATE_ACCOUNT_STAFF")) { ?>
						<a class="primary_btn w-button wrapp_btn_null" href="/demo/administration/compte/ajout">Créer un compte</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var delay;
	function launchSearch()
	{
		delay=Date.now();
		setTimeout(function(){
			if (parseInt(delay+500)<=Date.now())
			{
				loadTo("<?php echo str_replace($_SERVER["DOCUMENT_ROOT"], "", i('searchComptes.php')); ?>", $("form[id='search']").serialize(), ".section_title_wrapper", "after");
			}
		}, 500);
	}
	$("input[id='nom'], input[id='emplacement']").on("keypress", function(){
		launchSearch();
	});
	$("select[id='type']").on("change", function(){
		launchSearch();
	});
	$.datetimepicker.setLocale('fr');
	$('#date_arrive, #date_depart').datetimepicker({
		timepicker:false,
		formatDate:'d.m.y',
		format:'d/m/y',
		onSelectDate:function(ct,$i){
		  launchSearch();
		}
	});
</script>