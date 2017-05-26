		<?php
		if (auth())
		{
			echo '</main>';
		}
		if (auth())
		{
		?>
			<footer class="footer">
				<div class="footer_content">
				</div>
			</footer>
			<a class="scroll_to_top w-button" href="#top_bar"></a>
		<?php
		}
		if (isset($array_listes_modal))
		{
			foreach ($array_listes_modal as $id => $modal)
			{
				echo $modal;
			}
		}
		?>
		<div class="modal_message">
			<div class="modal_container">
				<div class="w-container">
					<h1 id="title_modal"></h1>
					<p id="message_modal"></p>
					<div class="bottom_btn"><a class="primary_btn space_beetween_btn w-button" href="#" id="refuse_button">Non</a><a class="primary_btn w-button" href="#" id="confirm_button">Confirmer</a>
					</div>
					<div class="btn_close_modal" data-ix="close-modal-msg"></div>
				</div>
			</div>
		</div>
		<script type="text/javascript" src="js/tooltip.js"></script>
	</body>
</html>