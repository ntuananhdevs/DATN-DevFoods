<?php
// Simple healthcheck endpoint - doesn't require Laravel
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK';

