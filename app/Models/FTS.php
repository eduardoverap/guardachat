<?php

namespace App\Models;

use App\Models\Traits\MessageProcessing;
use PDO;
use PDOException;

class FTS extends Base
{
  use MessageProcessing;

  // Create FTS table from scratch
  public function createFTSTable(): void
  {
    try {
      $this->conn->exec('DROP TABLE IF EXISTS messages_fts;');
      $this->conn->exec('
        CREATE VIRTUAL TABLE messages_fts USING fts5(
          contentParts,
          messageId UNINDEXED,
          tokenize=\'unicode61 remove_diacritics 2\'
        );
      ');
    } catch (PDOException $e) {
      logErrorWithTimestamp($e, __FILE__);
    }
  }

  // Reindex all from messages table
  public function reindexFTS(): void
  {
    try {
      $stmt   = $this->conn->query('SELECT messageId, contentParts FROM messages');
      $insert = $this->conn->prepare('
        INSERT INTO messages_fts(contentParts, messageId)
        VALUES (:contentParts, :messageId);
      ');

      $this->conn->beginTransaction();

      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $cleanText = markdownToText(decodeUnicodeEscapes($row['contentParts']));
        $insert->execute([
          ':messageId'    => $row['messageId'],
          ':contentParts' => $cleanText
        ]);
      }

      $this->conn->commit();

      // Optimize after reindexing
      $this->conn->exec('INSERT INTO messages_fts(messages_fts) VALUES (\'optimize\');');
    } catch (PDOException $e) {
      logErrorWithTimestamp($e, __FILE__);
    }
  }

  // FTS search
  public function search(string $query): ?string
  {
    if (mb_strlen($query) >= 3) {
      try {
        $stmt = $this->conn->prepare('
          SELECT messages.*, mappings.conversationId
          FROM messages_fts
          JOIN messages ON messages_fts.messageId = messages.messageId
          JOIN mappings ON messages.mappingId = mappings.mappingId
          WHERE messages_fts.contentParts MATCH :q
        ');
        $stmt->execute([':q' => $query]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = '';
        foreach ($results as $r) {
          $html .= $this->createMessageBlock($r, true);
        }
        return $html;
      } catch (PDOException $e) {
        logErrorWithTimestamp($e, __FILE__);
        return null;
      }
    } else {
      return 'Please enter at least three characters.';
    }
  }
}