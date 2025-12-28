<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
?>
<?php require __DIR__ . '/../views/layout/footer.php'; ?>