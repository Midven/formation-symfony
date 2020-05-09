$('#add-image').click(function () { 
    // je récupère le numéro des futurs champs que je vais créer
    const index = +$('#widgets-counter').val();
    // le + devant le $ est la pour préciser que la valeur qu'on veut récupérer est un nombre et pas un string
    // console.log(index)

    // je récupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    // J'injecte ce code au sein de la div
    $('#ad_images').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // je gère le bouton supprimer
    handleDeleteButtons();
});

function handleDeleteButtons(){
$('button[data-action="delete"]').click(function(){
    // this est l'élément sur lequel on a cliqué
    const target = this.dataset.target;
    // console.log(target);
    $(target).remove();
});
}

function updateCounter() {
const count = +$('#ad_images div.form-group').length;

$('#widgets-counter').val(count);
}

updateCounter();

handleDeleteButtons();