<?php

$output = [];
$return_var = 0;

exec('journalctl -u ssh -n 100 --no-pager 2>&1', $output, $return_var);

echo "<pre>";
print_r($output);
echo "</pre>";

echo "Code retour: " . $return_var;