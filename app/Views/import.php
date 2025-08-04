    <section class="content-section">
      <div class="sub-container">
        <h2>Import chats</h2>
        <?= $button ?><br />
        <textarea id="status" placeholder="Before clicking, please ensure you have valid 'conversation.json' ChatGPT files in the /storage/json/ folder." readonly></textarea>
        <div class="progress-bar">
          <div id="prg-import" class="current-progress"></div>
        </div>
      </div>
    </section>
    <script src="src/import.js"></script>
