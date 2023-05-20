import {getUporabnikById, getStanjeById, getTranzakcijeById} from "./fake_baza.js"

var id = localStorage.getItem("id")

async function onload(){
    if(window.location.href.includes("index")){
    let uporabnik = await getUporabnikById(id)
    document.getElementById("ime-usr").innerHTML = uporabnik.ime + " " + uporabnik.priimek
    document.getElementById("mail-usr").innerHTML = uporabnik.email

    let stanje = await getStanjeById(id)
    document.getElementById("balance").innerHTML = stanje + " eur"
    }

    nalozitranzakcije()
}

onload()

async function nalozitranzakcije(){
    var tranzakcije = await getTranzakcijeById(id)

    for(let tranzakcija of tranzakcije){
    var prejemnik = await getUporabnikById(tranzakcija.prejemnikov_id)
    var posiljatelj = await getUporabnikById(tranzakcija.posiljateljev_id)
    var trans_div = `<div class="transaction"> <p>${prejemnik.ime} ${prejemnik.priimek}</p> <p>${posiljatelj.ime} ${posiljatelj.priimek}</p> <p>${tranzakcija.datum}</p> <p>${tranzakcija.vsota} eur</p></div>`
    document.getElementById("transactions").innerHTML += trans_div}
}