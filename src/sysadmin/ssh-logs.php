<?php

$output = [];
exec('journalctl -u ssh -n 100 --no-pager | grep "Failed"', $output);

foreach ($output as $line) {
    echo htmlspecialchars($line) . "<br>";
}