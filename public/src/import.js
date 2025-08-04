const BASE_URL   = window.location.pathname.replace('import', '');
const btnImport  = document.getElementById('btn-import');
const btnUpdate  = document.getElementById('btn-update');
const statusInfo = document.getElementById('status');
const prgImport  = document.getElementById('prg-import');

// Parse files
let eventSource;

btnImport.addEventListener('click', () => {
  btnImport.disabled     = true;
  prgImport.style.width  = 0;
  statusInfo.textContent = '';

  alert('Import will start now. Please ensure you have valid \'conversation.json\' ChatGPT files in the /storage/json/ folder.');
  statusInfo.textContent = 'Processing... Don\'t close the window.\n';

  if (eventSource) {
    eventSource.close();
  }

  eventSource = new EventSource(BASE_URL + 'feeder');

  eventSource.onmessage = function(event) {
    statusInfo.textContent += event.data + "\n";
    statusInfo.scrollTop    = statusInfo.scrollHeight;
  }

  eventSource.onerror = function() {
    statusInfo.textContent += 'Oops!\n';
    eventSource.close();
    btnImport.disabled = false;
  }

  eventSource.addEventListener('progress', (event) => {
    const percent         = parseInt(event.data, 10);
    prgImport.style.width = percent + '%';
  });

  eventSource.addEventListener('close', () => {
    eventSource.close();
    btnImport.disabled      = false;
    statusInfo.textContent += 'Finished.';
  });
});