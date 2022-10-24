
/*
function initListeCampus() {
    fetch(url + '/apicampus/liste', {
        method: "GET",
        headers : {'Accept' : 'application/json'}
    }
    ).then(response => response.json())
        .then(response => {
            let listeCampus = "";
            response.map(campus => {
                listeCampus += `<tr><td> ${campus.nom} </td>
                               <td> <a href="#"> Modifier </a> -  <a href="#"> Supprimer </a></td>                     
                           </tr>`
            })
            document.querySelector('tbody').innerHTML = listeCampus;
        })
}

window.onload = () => {
    initListeCampus();

}*/
