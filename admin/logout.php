<?php
session_start();
session_destroy();
header("Location: login?logout=1");
exit;
