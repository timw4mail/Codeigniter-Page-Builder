<? $q_num = $this->page->num_queries() ?>	
	<footer class="footer">
		Generated in <?= $this->benchmark->elapsed_time();?> seconds, <?= $q_num ?> quer<?= ($q_num == 1) ? "y": "ies" ?>
		<? if($foot_js != ""): ?>
		<?= $foot_js ?>
		<? endif ?>
	</footer>
</body>
</html>