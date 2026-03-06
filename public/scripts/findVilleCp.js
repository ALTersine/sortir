const villeSelected = document.getElementById('sortie_lieuVille');
const cpToFill = document.getElementById('sortie_lieuCP');

document.addEventListener('DOMContentLoaded', function(){
    villeSelected.addEventListener("change", addCp)
})

function addCp(){
    cpToFill.vale="";
    let villeID = villeSelected.value;
    let url = villeSelected.dataset.url;

    if(!villeID){
        cpToFill.value='';
        return;
    }

    fetch(url+"?ville="+villeID)
        .then(response => response.json())
    .then(data => {
        cpToFill.value = data.codePostal;
    })
        .catch(err=>{
            console.error('Problème avec le JS : ', err.message);
        })
}
