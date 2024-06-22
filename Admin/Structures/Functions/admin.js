async function openModifyModal(id, table) {
    try {
        const response = await fetch(`databaseTreatment.php?id=${id}&table=${table}`);
        const data = await response.json();

        const modifyFormContent = document.getElementById('modifyFormContent');
        modifyFormContent.innerHTML = '';

        for (const [key, value] of Object.entries(data)) {
            const div = document.createElement('div');
            div.classList.add('form-group');
            const label = document.createElement('label');
            label.setAttribute('for', `input${key}`);
            label.textContent = key;
            const input = document.createElement('input');
            input.type = 'text';
            input.classList.add('form-control');
            input.id = `input${key}`;
            input.name = `${id}tab[${key}]`;
            input.value = value;
            div.appendChild(label);
            div.appendChild(input);
            modifyFormContent.appendChild(div);
        }

        document.getElementById('modifyId').value = id;

        const modifyModal = new bootstrap.Modal(document.getElementById('modifyModal'));
        modifyModal.show();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

function openDeleteModal(id) {
    document.getElementById('deleteId').value = id;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function togglePopup($id){
    let popup = document.querySelector($id);
    popup.classList.toggle("openPopup");
}


var editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var customId = button.getAttribute('data-custom-id');
    var name = button.getAttribute('data-name');
    var cost = button.getAttribute('data-cost');
    var promoCode = button.getAttribute('data-promo-code');
    var endDate = button.getAttribute('data-end-date');
    var modal = this;
    modal.querySelector('#editCustomId').value = customId;
    modal.querySelector('#editName').value = name;
    modal.querySelector('#editCost').value = cost;
    modal.querySelector('#editPromoCode').value = promoCode || '';
    modal.querySelector('#editEndDate').value = endDate || '';
});

var deleteModal = document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var customId = button.getAttribute('data-custom-id');
    var inputDelete = deleteModal.querySelector('#deleteCustomId');
    inputDelete.value = customId;
});

document.getElementById('customImage').addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
        if (file.size > 512 * 1024) {
            alert("L'image doit être de 500 Ko maximum.");
            event.target.value = "";
            return;
        }

        const fileType = file.type;
        if (fileType !== 'image/png') {
            alert("Seul le format PNG est autorisé.");
            event.target.value = '';
            return;
        }

        const fileURL = URL.createObjectURL(file);

        let img = new Image();
        img.src = fileURL;
        img.onload = function() {
            if (img.width > 500 || img.height > 500) {
                alert("La résolution de l'image doit être de 500x500 pixels maximum.");
                URL.revokeObjectURL(img.src);
                event.target.value = "";
            } else {
                const preview = document.getElementById('previewImage');
                preview.src = fileURL;
                preview.style.display = 'block';
            }
        };
    }
});


async function openModifyModalUser(id) {
    try {
        const response = await fetch(`databaseTreatment.php?id=${id}&table=userRight`);
        const data = await response.json();

        document.getElementById('modifyId').value = id;

        const modifyFormContent = document.getElementById('modifyFormContent');
        modifyFormContent.innerHTML = '';

        const selectDiv = document.createElement('div');
        selectDiv.classList.add('form-group');
        const selectLabel = document.createElement('label');
        selectLabel.setAttribute('for', 'idrankSelect');
        selectLabel.textContent = 'Rôle';
        const select = document.createElement('select');
        select.classList.add('form-control');
        select.id = 'idrankSelect';
        select.name = `${id}tab[idrank]`;

        const roles = {
            'Utilisateur': 1,
            'Administrateur': 2,
            'Modérateur': 3
        };

        for (const [role, value] of Object.entries(roles)) {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = role;
            select.appendChild(option);
        }

        if (data.idrank) {
            select.value = data.idrank;
        }

        selectDiv.appendChild(selectLabel);
        selectDiv.appendChild(select);
        modifyFormContent.appendChild(selectDiv);

        const modifyModal = new bootstrap.Modal(document.getElementById('modifyModal'));
        modifyModal.show();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
}

function openTempBanModal(id) {
    document.getElementById('tempBanId').value = id;
    const tempBanModal = new bootstrap.Modal(document.getElementById('tempBanModal'));
    tempBanModal.show();
}

function openPermBanModal(id) {
    document.getElementById('permBanId').value = id;
    const permBanModal = new bootstrap.Modal(document.getElementById('permBanModal'));
    permBanModal.show();
}


async function seeNewsletter(id) {
    try {
        const response = await fetch(`databaseTreatment.php?id=${id}&table=newsletter`);
        const responseData = await response.json();

        const htmlContent = responseData.html;

        const seeNewsletterContent = document.getElementById('seeNewsletterContent');
        seeNewsletterContent.innerHTML = htmlContent;

        const seeNewsletterModal = new bootstrap.Modal(document.getElementById('seeNewsletterModal'));
        seeNewsletterModal.show();
    } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
    }
}


