<?php
// URL function
if (!function_exists('url')) {
  function url(string $path = ''): string
  {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
  }
}

if (!function_exists('goHome')) {
  function goHome(): void
  {
    header('Location: ' . BASE_URL);
    die();
  }
}

if (!function_exists('timestampToFormattedDate')) {
  function timestampToFormattedDate(?string $timestamp, string $format = 'l, F j, Y, \a\t h:i A'): ?string
  {
    if ($timestamp !== null) {
      return date($format, floor($timestamp));
    } else {
      return null;
    }
  }
}

// Get and decode JSON content
if (!function_exists('importJSON')) {
  function importJSON($file): ?array
  {
    try {
      $rawContent = file_get_contents($file);
      return json_decode($rawContent);
    } catch (Throwable $e) {
      logErrorWithTimestamp($e, 'helpers.php');
      return null;
    }
  }
}

// Log error with date and time
if (!function_exists('logErrorWithTimestamp')) {
  function logErrorWithTimestamp(Throwable|PDOException $error, string $file): void
  {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[{$timestamp}] Error from $file: {$error->getMessage()}\n", 3, JOURNAL_LOG);
  }
}

// Decode \uXXXX sequences to real UTF-8 characters
if (!function_exists('decodeUnicodeEscapes')) {
  function decodeUnicodeEscapes(string $str): string
  {
    return preg_replace_callback(
      '/\\\\u([0-9a-fA-F]{4})/',
      fn($m) => mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE'),
      $str
    );
  }
}

// Clean Markdown and leave only text
if (!function_exists('markdownToText')) {
  function markdownToText(string $markdown): string
  {
    $text = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $markdown);
    $text = preg_replace('/[#>*_`~\-]+/', '', $text);
    $text = strip_tags($text);
    return trim(preg_replace('/\s+/', ' ', $text));
  }
}
