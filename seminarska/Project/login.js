import {preveriUporabnika} from "./fake_baza.js"

async function login(){
    var usr = document.getElementById("username").value;
    if(usr == ""){ //preveri ce je usr prazen
        document.getElementById("error-msg").innerHTML = "Vnesite uporabniško ime!"
        return
    }
    var psw = document.getElementById("password").value;
    if(psw == ""){ //preveri ce je pass prazen
        document.getElementById("error-msg").innerHTML = "Vnesite geslo!"
        return
    }
    var pswHash = returnHash(psw) //hashed password
    console.log(pswHash)

    var id = await preveriUporabnika(usr, pswHash) //vrne false ce ni uporabnika, id pa ce je geslo ok
    console.log(id)
    if(!id){
        document.getElementById("error-msg").innerHTML = "Uporabniško ime ali geslo je napačno!"
        return
    }
    localStorage.setItem("id", id)
    console.log(localStorage.getItem("id"))
    window.location.href = "index.html"
}

function returnHash(val){ //some hashing stuff
    var hashObj = new jsSHA("SHA-512", "TEXT", {numRounds: 1});
    hashObj.update(val);
    return hashObj.getHash("HEX")
}


document.getElementById("submit").addEventListener("click", login);

document.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
      login()
    }
  });