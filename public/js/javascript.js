const btnAddCollection = document.querySelector('.tienda-attributes-btn-add');
if (btnAddCollection != null && btnAddCollection != undefined) {
    let target = btnAddCollection.getAttribute('target');
    if (target !== null && target !== undefined && target !== '') {
        let collection = document.querySelector('.' + target);
        btnAddCollection.addEventListener("click", function () { addCollectionField(collection) });
    }
}
var actualFieldsItems = 0;

addListenerToDeleteCollection();
function addCollectionField(fieldPrototype) {
    const item = document.createElement('div');
    item.setAttribute('class', 'tienda-field');
    item.innerHTML = fieldPrototype.getAttribute('data-prototype').replace(/__name__/g, 'n-' + actualFieldsItems);

    fieldPrototype.parentNode.appendChild(item);
    fieldPrototype.appendChild(item);
    //refresh
    addListenerToDeleteCollection();
    //refresh

    actualFieldsItems++;
    return item;
}

function addListenerToDeleteCollection() {
    const btnsDelete = document.querySelectorAll('.tienda-attributes-btn-delete');
    for (let db = 0; db < btnsDelete.length; db++) {
        const btnDelete = btnsDelete[db];
        btnDelete.addEventListener("click", deleteCollectionField);
    }
}

function deleteCollectionField(e) {
    const btn = e.target;
    const parentDiv = btn.parentNode.parentNode.parentNode;
    parentDiv.remove();
}