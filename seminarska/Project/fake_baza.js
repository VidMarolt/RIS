let uporabniki = [];
let racuni = [];
let tranzakcije = [];

async function getUporabniki() {
  await fetch("uporabnik.json")
    .then((response) => response.json())
    .then((jsonData) => {
      // Store the objects in an array
      uporabniki = Array.isArray(jsonData) ? jsonData : [jsonData];
    })
    .catch((error) => {
      console.log("Error loading JSON:", error);
    });
}

async function getRacuni() {
  await fetch("racun.json")
    .then((response) => response.json())
    .then((jsonData) => {
      // Store the objects in an array
      racuni = Array.isArray(jsonData) ? jsonData : [jsonData];
    })
    .catch((error) => {
      console.log("Error loading JSON:", error);
    });
}

function setRacuni() {
  const jsonString = JSON.stringify(racuni, null, 2);
  const blob = new Blob([jsonString], { type: "application/json" });
  const a = document.createElement("a");
  a.style.display = "none";
  a.href = URL.createObjectURL(blob);
  a.download = "racun.json";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(a.href);
}

async function getTranzakcije() {
  await fetch("tranzakcija.json")
    .then((response) => response.json())
    .then((jsonData) => {
      tranzakcije = Array.isArray(jsonData) ? jsonData : [jsonData];
    })
    .catch((error) => {
      console.log("Error loading JSON:", error);
    });
}

function setTranzakcije() {
  console.log("writing: ");
  console.log(tranzakcije);

  const jsonString = JSON.stringify(tranzakcije, null, 2);
  const blob = new Blob([jsonString], { type: "application/json" });
  const a = document.createElement("a");
  a.style.display = "none";
  a.href = URL.createObjectURL(blob);
  a.download = "tranzakcija.json";
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(a.href);
}

async function preveriUporabnika(usr, psw) {
  await getUporabniki();
  let index = uporabniki.findIndex((obj) => obj.uporabnisko_ime === usr);
  if (index == -1) return false;
  if (psw === uporabniki[index].geslo) return uporabniki[index].id;
  return false;
}

async function getUporabnikById(id) {
  await getUporabniki();
  return uporabniki.find((obj) => obj.id == id);
}

async function getStanjeById(id) {
  await getRacuni();
  let racun = racuni.find((obj) => obj.uporabnik_id == id);
  console.log(racuni);
  let stanje = racun.stanje;
  return stanje;
}

async function getTranzakcijeById(id) {
  await getTranzakcije();
  return tranzakcije.filter(
    (obj) => obj.posiljateljev_id == id || obj.prejemnikov_id == id
  );
}

async function izvediTranzakcijo(id, ime, priimek, trr, vst) {
  await getRacuni();
  let Prejemnikov_racun = racuni.find((obj) => obj.trr == trr);
  console.log(Prejemnikov_racun);
  if (!Prejemnikov_racun) return false;

  let prejemnik = await getUporabnikById(Prejemnikov_racun.uporabnik_id);

  if (prejemnik.ime == ime && prejemnik.priimek == priimek) {
    let posiljateljev_racun = racuni.find((obj) => obj.uporabnik_id == id);

    if (vst > racuni.find((obj) => obj.id == posiljateljev_racun.id).stanje)
      return false;
    console.log("tranzakcija izvedena uspešno!");

    racuni.find((obj) => obj.id == Prejemnikov_racun.id).stanje += Number(vst);
    racuni.find((obj) => obj.id == posiljateljev_racun.id).stanje -= vst;
    await getTranzakcije();

    let nova_trans = {
      id: tranzakcije[tranzakcije.length - 1].id + 1,
      posiljateljev_id: id,
      prejemnikov_id: prejemnik.id,
      vsota: vst,
      datum: datum(),
    };

    tranzakcije.push(nova_trans);
    await setRacuni();
    await setTranzakcije();

    return true;
    //return false;
  }
  return false;
}

function datum() {
  const date = new Date();

  let currentDay = String(date.getDate()).padStart(2, "0");

  let currentMonth = String(date.getMonth() + 1).padStart(2, "0");

  let currentYear = date.getFullYear();

  // we will display the date as DD-MM-YYYY

  let currentDate = `${currentDay}.${currentMonth}.${currentYear}`;
  return currentDate;
}

export {
  preveriUporabnika,
  getUporabnikById,
  getStanjeById,
  getTranzakcijeById,
  izvediTranzakcijo,
};
