<?php 
//ce fichier ne sert � rien � priori, tout est g�r� dans la "gestionFAQ". 
//Il est gard� pour voir si on peut am�liorer le fichier gestionFAQ qui est assez lourd
if (!isset($require))
	{
		$require="";
	}
	$require.="database.class.php,user.class.php,user.controller.class.php,faq.class.php,";
	require_once($_SERVER['DOCUMENT_ROOT']."/demo/includes/generalIncludes.php");
	if (!auth())
	{
		?>
		<script type="text/javascript">
			$( document ).ready(function() {
				window.location.replace("index.php");
			});
		</script>
		<?php
		exit();
	}
	if(!isStaff() || !isset($_POST["id"]))
	{
		exit();
	}
	$faq = new FAQ($_POST['id']);
?>
<div class="custom_table_col">
	<input class="campandjoy_input w-input" type="text" name="question" value="<?php $faq->getQuestion();?>" id="question"/>
</div>
<div class="custom_table_col">
	<input class="campandjoy_input w-input" type="text" name="reponse" value="<?php $faq->getReponse();?>" id="reponse"/>
</div>
