	</main>
	<footer class="v2-statusbar">
		<span><span class="v2-dot v2-dot-good"></span> en línea</span>
		<span class="v2-hide-mobile">usuario <b><?= e($user) ?></b></span>
		<span>sesión hasta <b><?= date('H:i', ($_SESSION['login_at'] ?? time()) + SESSION_LIFETIME) ?></b></span>
		<span class="v2-spacer"></span>
		<span class="v2-hide-mobile"><?= e(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '')) ?></span>
		<span data-clock>--:--:--</span>
	</footer>
</body>
</html>
