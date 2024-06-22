document.addEventListener("DOMContentLoaded", function() {
    // Fonction pour effacer le canvas
    function clearCanvas() {
        context.clearRect(0, 0, canvas.width, canvas.height);
    }

    // Fonction pour obtenir les coordonnées du canvas lors d'un événement de souris
    function getCanvasCoordinates(event) {
        var canvasRect = canvas.getBoundingClientRect();
        return {
            x: event.clientX - canvasRect.left,
            y: event.clientY - canvasRect.top
        };
    }

    // Fonction pour obtenir les coordonnées du canvas lors d'un événement tactile
    function getCanvasCoordinatesTouch(event) {
        var canvasRect = canvas.getBoundingClientRect();
        return {
            x: event.touches[0].clientX - canvasRect.left,
            y: event.touches[0].clientY - canvasRect.top
        };
    }

    var canvas = document.getElementById('canvas');
    var context = canvas.getContext('2d');
    var isDrawing = true;

    canvas.addEventListener('mousedown', function(e) {
        if (isDrawing) {
            var coordinates = getCanvasCoordinates(e);
            context.beginPath();
            context.moveTo(coordinates.x, coordinates.y);
            canvas.addEventListener('mousemove', draw);
            e.preventDefault();
        }
    });

    canvas.addEventListener('mouseup', function() {
        canvas.removeEventListener('mousemove', draw);
    });

    function draw(e) {
        if (isDrawing) {
            var coordinates = getCanvasCoordinates(e);
            context.lineTo(coordinates.x, coordinates.y);
            context.stroke();
        }
    }

    canvas.addEventListener('touchstart', function(e) {
        if (isDrawing) {
            var coordinates = getCanvasCoordinatesTouch(e);
            context.beginPath();
            context.moveTo(coordinates.x, coordinates.y);
            canvas.addEventListener('touchmove', drawTouch);
            e.preventDefault();
        }
    });

    canvas.addEventListener('touchend', function() {
        canvas.removeEventListener('touchmove', drawTouch);
    });

    function drawTouch(e) {
        if (isDrawing) {
            var coordinates = getCanvasCoordinatesTouch(e);
            context.lineTo(coordinates.x, coordinates.y);
            context.stroke();
        }
    }

    var signatureModal = document.getElementById('signatureModal');
    if (signatureModal) {
        signatureModal.addEventListener('hidden.bs.modal', function() {
            clearCanvas();
        });
    }

    var clearBtn = document.getElementById('clearCanvasBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            clearCanvas();
        });
    }

    var saveSignatureBtn = document.getElementById('saveSignatureBtn');
    if (saveSignatureBtn) {
        saveSignatureBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le formulaire de se soumettre automatiquement

            var imageData = canvas.toDataURL('image/png'); // Conversion du canvas en base64 PNG

            // Création du formulaire
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = ''; // Laissez vide pour envoyer à la même page

            // Création de l'input pour l'image
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'sign'; // Assurez-vous que le nom correspond à celui attendu dans le script PHP
            input.value = imageData;

            // Ajout de l'input au formulaire
            form.appendChild(input);

            // Ajout du formulaire à la page
            document.body.appendChild(form);

            // Soumission du formulaire
            form.submit();
        });
    }
});


const dt = new DataTransfer();
const MAX_SIZE = 2 * 1024 * 1024;

document.getElementById('attachment').addEventListener('change', function(e) {
    let totalSize = 0;

    for (let file of dt.files) {
        totalSize += file.size;
    }

    for (let file of this.files) {
        if (totalSize + file.size > MAX_SIZE) {
            alert('La taille totale des fichiers dépasse 2 Mo.');
            return;
        }

        totalSize += file.size;

        let fileBloc = document.createElement('span');
        fileBloc.classList.add('file-block');

        let fileName = document.createElement('span');
        fileName.classList.add('name');
        fileName.textContent = file.name;

        let br = document.createElement('br');

        let fileDelete = document.createElement('span');
        fileDelete.classList.add('file-delete');

        let deleteBtn = document.createElement('button');
        deleteBtn.classList.add('btn', 'btn-danger');
        deleteBtn.textContent = '-';
        deleteBtn.addEventListener('click', function() {
            let name = this.parentElement.nextElementSibling.textContent;
            this.parentElement.parentElement.remove();

            for (let i = 0; i < dt.items.length; i++) {
                if (name === dt.items[i].getAsFile().name) {
                    dt.items.remove(i);
                    break;
                }
            }

            document.getElementById('attachment').files = dt.files;
            let totalSize = 0;
            for (let file of dt.files) {
                totalSize += file.size;
            }

            document.getElementById('total-size').textContent = `Taille totale : ${(totalSize / 1024).toFixed(2)} Mo`;

            if (totalSize > MAX_SIZE) {
                alert('La taille totale des fichiers dépasse 2 Mo.');
            }
        });

        fileDelete.appendChild(deleteBtn);
        fileBloc.appendChild(fileDelete);
        fileBloc.appendChild(document.createTextNode('\u00A0\u00A0')); // Ajout d'espaces entre le bouton et le nom du fichier
        fileBloc.appendChild(fileName);
        fileBloc.appendChild(br);

        document.getElementById('files-names').appendChild(fileBloc);

        dt.items.add(file);
    }

    this.files = dt.files;

    document.getElementById('total-size').textContent = `Taille totale : ${(totalSize / 1024 / 1024).toFixed(2)} Mo`;
});
