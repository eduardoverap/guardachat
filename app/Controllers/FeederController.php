<?php

namespace App\Controllers;

use App\Models\FTS;
use App\Models\Import;
use Throwable;

class FeederController
{
  private function importFiles(): void
  {
    $files = glob(APP_ROOT . '/storage/json/*.json');
    
    if ($files !== false) {
      try {
        // Get file count
        $count = count($files);
        echo "data: Found {$count} JSON files.\n\n";
        flush();

        // Get total size
        $totalSize = 0;
        foreach ($files as $file) {
          $totalSize += filesize($file);
        }

        // Sort by last modified
        usort($files, function($a, $b) {
          return filemtime($a) - filemtime($b);
        });

        // Get file content
        $currSum = 0;
        foreach ($files as $file) {
          echo "data: Processing file {$file}...\n\n";
          flush();

          $currSum    += filesize($file);
          $content     = file_get_contents($file);
          $importModel = new Import();
          $importModel->parseFileContent(json_decode($content));

          $percent = round(($currSum / $totalSize) * 100);
          echo "event: progress\ndata: {$percent}\n\n";
          flush();
        }
        echo "data: All files were processed.\n\n";
        flush();

        $this->createFTSTable();
      } catch (Throwable $e) {
        logErrorWithTimestamp($e, __FILE__);
      }
    } else {
      echo "data: No JSON files were found.\n\n";
      flush();
    }
  }

  // Create FTS table for advanced searching
  private function createFTSTable(): void
  {
    $ftsModel = new FTS();

    try {
      echo "data: Creating FTS table...\n\n";
      flush();
      $ftsModel->createFTSTable();
      
      echo "data: Reindexing FTS table...\n\n";
      flush();
      $ftsModel->reindexFTS();
    } catch (Throwable $e) {
      logErrorWithTimestamp($e, __FILE__);
    }
  }

  public function index(): void
  {
    // Verify if it is a request from import.js
    if (
      $_SERVER['REQUEST_METHOD'] === 'GET' &&
      isset($_SERVER['HTTP_ACCEPT']) &&
      strpos($_SERVER['HTTP_ACCEPT'], 'text/event-stream') !== false
    ) {
      // SSE headers
      header('Content-Type: text/event-stream');
      header('Cache-Control: no-cache');
      header('Connection: keep-alive');

      while (ob_get_level() > 0) ob_end_flush();
      ob_implicit_flush(true);

      echo "data: Starting...\n\n";
      flush();

      // Perform file import
      $this->importFiles();

      echo "event: close\ndata: done\n\n";
      flush();
      die();
    } else {
      http_response_code(404);
      goHome();
    }
  }
}
