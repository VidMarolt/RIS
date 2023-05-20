import {izvediTranzakcijo} from "./fake_baza.js"

var id = localStorage.getItem("id")

async function izvedi(){    
    var ime = document.getElementById("ime").value;
    if(ime == ""){
        document.getElementById("error-msg").innerHTML = "Vnesite prejemnikovo ime!"
        return
    }    
    var priimek = document.getElementById("priimek").value;
    if(priimek == ""){
        document.getElementById("error-msg").innerHTML = "Vnesite prejemnikov priimek!"
        return
    }   
    var trr = document.getElementById("trr").value;
    if(trr == ""){
        document.getElementById("error-msg").innerHTML = "Vnesite prejemnikov trr!"
        return
    }   
    var vst = document.getElementById("vst").value;
    if(vst == ""){
        document.getElementById("error-msg").innerHTML = "Vnesite vsoto!"
        return
    }

    let uspesno = await izvediTranzakcijo(id, ime, priimek, trr, vst)

    if(uspesno) window.location.href = "index.html"
    else document.getElementById("error-msg").innerHTML = "Tranzakcija ni bila uspešna!"

    
}


document.getElementById("izvedi").addEventListener("click", izvedi);
document.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        izvedi()
    }
  });