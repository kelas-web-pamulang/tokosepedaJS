@@ -0,0 +1,7 @@
<?php
session_start();
session_destroy();
setcookie('clientId', '', time() - 3600, '/');
setcookie('clientSecret', '', time() - 3600, '/');

header('Location: login.php');