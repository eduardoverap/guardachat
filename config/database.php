<?php
// Set PDO connection
try {
  $conn = new PDO('sqlite:' . DB_PATH);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $conn;
} catch (PDOException $e) {
  logErrorWithTimestamp($e, __FILE__);
  die($e->getMessage() . "\n" . DB_PATH);
}
