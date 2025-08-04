<?php

namespace App\Models;

use App\Controllers\IterateController;
use App\Models\DTO\Mapping;
use App\Models\DTO\Message;
use PDOException;

class Import extends Base
{
  public function __construct()
  {
    parent::__construct();
  }

  // Reset chat DB
  public function initializeTable()
  {
    $this->conn->exec('
      CREATE TABLE IF NOT EXISTS conversations(
        conversationId TEXT PRIMARY KEY,
        createTime INTEGER NOT NULL,
        updateTime INTEGER,
        originalTitle TEXT NOT NULL,
        personalTitle TEXT
      );

      CREATE TABLE IF NOT EXISTS mappings(
        mappingId TEXT PRIMARY KEY,
        conversationId TEXT,
        parent TEXT,
        children TEXT,
        FOREIGN KEY(conversationId) REFERENCES conversations(conversationId)
      );

      CREATE TABLE IF NOT EXISTS messages(
        messageId TEXT PRIMARY KEY,
        mappingId TEXT,
        authorRole TEXT,
        createdTime INTEGER,
        updatedTime INTEGER,
        contentType TEXT,
        contentParts TEXT,
        FOREIGN KEY(mappingId) REFERENCES mappings(mappingId)
      );
    ');
  }

  // Insert mapping from its DTO
  public function insertMapping(Mapping $mapping): void {
    try {
      $stmt = $this->conn->prepare('
        INSERT OR REPLACE INTO mappings
        VALUES (:mapid, :cid, :parent, :children);
      ');
      $stmt->execute([
        'mapid'    => $mapping->mappingId,
        'cid'      => $mapping->conversationId,
        'parent'   => $mapping->parent,
        'children' => $mapping->getChildrenAsString()
      ]);
    } catch (PDOException $e) {
      logErrorWithTimestamp($e, __FILE__);
    }
  }

  // Insert message from its DTO
  public function insertMessage(Message $message): void {
    try {
      $stmt = $this->conn->prepare('
        INSERT OR REPLACE INTO messages
        VALUES (:msgid, :mapid, :role, :created, :updated, :type, :parts);
      ');
      $stmt->execute([
        'msgid'   => $message->messageId,
        'mapid'   => $message->mappingId,
        'role'    => $message->authorRole,
        'created' => $message->createTime,
        'updated' => $message->updateTime,
        'type'    => $message->contentType,
        'parts'   => $message->getContentAsString()
      ]);
    } catch (PDOException $e) {
      logErrorWithTimestamp($e, __FILE__);
    }
  }

  public function parseFileContent(array $fileContent)
  {    
    // Initialize table
    $this->initializeTable();

    foreach ($fileContent as $conversation) {
      // Prepare conversations table
      $updated = ($conversation->update_time !== null) ? (int) floor($conversation->update_time) : null;
      $stmt = $this->conn->prepare('
        INSERT OR REPLACE INTO conversations (conversationId, createTime, updateTime, originalTitle)
        VALUES (:cid, :created, :updated, :title);
      ');
      $stmt->execute([
        'cid'     => (string) $conversation->id,
        'created' => (int)    floor($conversation->create_time),
        'updated' => $updated,
        'title'   => (string) $conversation->title
      ]);

      // Prepare mapping table
      $mappingCollection = $conversation->mapping;
      $map = [];

      foreach ($mappingCollection as $node) {
        $map[$node->id] = $node;
      }

      // For mappings and messages, organize commits in chunks of 100
      $count = 0;
      $this->conn->beginTransaction();

      foreach ($mappingCollection as $node) {
        if (is_null($node->parent)) {
          $count++;
          IterateController::iterateTree($node->id, $map, $conversation->id, [$this, 'insertMapping'], [$this, 'insertMessage']);
        }

        if ($count === 100) {
          $this->conn->commit();
          $this->conn->beginTransaction();
          $count = 0;
        }
      }

      $this->conn->commit();
    }
  }

  public function __destruct()
  {
    parent::__destruct();
  }
}
