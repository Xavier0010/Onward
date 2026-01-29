const data = [
  "Kalih welas",
  "Tiga welas",
  "Sekawan welas"
];

const results = [];

async function run() {
  for (const text of data) {
    document.querySelector("#latin").value = text;
    document.querySelector("#btnLatin2Jawa").click();

    await new Promise(r => setTimeout(r, 500));

    results.push(document.querySelector("#jawa").value);
  }
  console.table(results);
}

run();