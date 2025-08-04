    <section class="content-section">
      <div class="sub-container">
        <h2>About GuardaChat</h2>
        <article>
          <p>GuardaChat is a lightweight PHP MVC application that allows users to import chat history from JSON files into an SQLite database. It provides a user-friendly interface to browse and query conversations through a dynamic frontend.</p>
          <h3>Installation</h3>
          <p><strong>Requirements:</strong></p>
          <ul>
            <li>PHP 8.0+</li>
            <li>Apache server (or a compatible setup like XAMPP, MAMP, etc.)</li>
            <li>Composer</li>
          </ul>
          <p><strong>Setup steps:</strong></p>
          <ol>
            <li>Clone the repository</li>
            <pre><code>git clone https://github.com/eduardoverap/guardachat.git</code></pre>
            <li>Install dependencies</li>
            <pre><code>composer install</code></pre>
            <li>Deploy
              <ul>
                <li>Option 1: Copy the project folder into the <code>/htdocs/</code> directory of your XAMPP/MAMP setup.</li>
                <li>Option 2: Mount the folder in a PHP + Apache container.</li>
              </ul>
            </li>
            <li>
              Change your timezone<br />
              In your project folder, open <code>/config/config.php</code> with a text editor (Notepad will do), replace <code>America/Lima</code> with your local timezone (find it on <a href="http://php.net/manual/en/timezones.php" target="_blank">PHP documentation</a>), and save the file.
            </li>
            <pre><code>// Set timezone
date_default_timezone_set('America/Lima');</code></pre>
            <li>
              Launch the application<br />
              Open your browser and navigate to <code>http://localhost/guardachat</code>
            </li>
          </ol>
          <h3>Import your chats</h3>
          <p>To start using GuardaChat, you must import your previous conversations.</p>
          <ol>
            <li>Recover the TAR.GZ files you downloaded when exporting your ChatGPT conversations and extract their contents.</li>
            <li>Copy the <code>conversation.json</code> files to the application's <code>/storage/json/</code> folder. As a suggestion, rename each file according to its creation or modification date so you can recognize and sort them more easily.</li>
            <li>Start the application on your server. If you haven't created your chat database, the content import screen should appear. Otherwise, click <a title="Go to Import page" href="<?= url('import') ?>">Import</a> in the left sidebar.</li>
            <li>Double-check that you have the JSON files ready in the <code>/storage/json/</code> folder, click <code>Import my chats</code> (or <code>Reset database</code> if you already have one), and accept the pop-up message.</li>
            <li>Depending on the size of your files and the number of conversations in them, the import may take several minutes. Do not change pages until the progress bar reaches the end and the box indicates that the process completed successfully.</li>
          </ol>
          <h3>View your chats</h3>
          <p>To view your past conversations, go to <a title="Go to My chats page" href="<?= url() ?>">My chats</a> in the left sidebar. You'll see a list of all your conversations, sorted by the date and time of your last interaction, from most recent to oldest. Click on any title to access its content.</p>
          <h3>Search your chats</h3>
          <p>If you want to search for content, go to the <a title="Go to Search page" href="<?= url('search') ?>">Search</a> option in the left sidebar. Enter a few words (at least three characters) in the search box and press <code>Enter</code> or click the <code>Search</code> button. Messages containing the desired words will appear. Click <code>View full chat >></code> to go to the full conversation.</p>
          <h3>Dependencies</h3>
          <p>This app uses <a title="Composer" href="https://getcomposer.org/" target="_blank">Composer</a> to handle autoload and <a title="Fastdown Markdown Parser" href="https://github.com/fastvolt/markdown" target="_blank">Fastdown Markdown Parser</a> for Markdown-to-HTML conversions.</p>
          <h3>About the author</h3>
          <p>Developed by Eduardo Vera Palomino<br />Ica, <?= date('Y'); ?> &mdash; <span class="emojis">☀️🍇🧜🏽‍♀️</span></p>
        </article>
      </div>
    </section>
