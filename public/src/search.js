const BASE_URL      = window.location.pathname;
const txtSearch     = document.getElementById('txt-search');
const btnSearch     = document.getElementById('btn-search');
const searchResults = document.getElementById('search-results');

const search = async (event) => {
  const query = txtSearch.value;

  // Fetch response
  const response = await fetch(BASE_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: `query=${encodeURIComponent(query)}`
  });

  const result = await response.text();
  searchResults.innerHTML = `<section>${result}</section>`;
  console.log('Success');
}

document.addEventListener('DOMContentLoaded', () => {
  txtSearch.addEventListener('keydown', (event) => {
    if (event.key === 'Enter') {
      search();
    }
  });
  btnSearch.addEventListener('click', search);
});