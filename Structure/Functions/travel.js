function confirmDelete() {

    var reason = prompt("Veuillez saisir la raison de la suppression du voyage :");

    if (reason !== null && reason !== "") {
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'delete_reason=' + encodeURIComponent(reason),
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la suppression du voyage');
                }
                location.reload();
            })
            .catch(error => {
                console.error('Erreur :', error);
            });
    } else {
        alert("Veuillez saisir une raison valide.");
    }
}

function showReplyForm(commentId) {
    const replyForm = document.getElementById('replyForm' + commentId);
    replyForm.classList.toggle('d-none');
}

function toggleSubmitButton() {
    var commentInput = document.querySelector("textarea[name='commentaire']");
    var submitButton = document.querySelector("button[name='Envoyer']");

    if (commentInput.value.trim() !== "") {
        submitButton.classList.remove("d-none");
    } else {
        submitButton.classList.add("d-none");
    }
}
function showReplies(commentId, idSession, idCreator, rank) {
    const repliesDiv = document.getElementById('replies' + commentId);
    const repliesButton = document.getElementById('repliesButton' + commentId);

    if (repliesDiv) {
        if (repliesDiv.classList.contains('d-none')) {
            let url = `Includes/getComments.php?commentId=${commentId}&idSession=${idSession}&idCreator=${idCreator}&rank=${rank}`;

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    repliesDiv.innerHTML = data;
                    repliesDiv.classList.remove('d-none');
                    toggleRepliesButton(commentId);
                })
                .catch(error => console.error('Erreur lors du chargement des réponses :', error));
        } else {
            repliesDiv.classList.add('d-none');
            toggleRepliesButton(commentId);
        }
    } else {
        console.error('Element with ID "replies' + commentId + '" not found.');
    }
}

function toggleRepliesButton(commentId) {
    const repliesButton = document.getElementById('repliesButton' + commentId);

    if (repliesButton) {
        if (repliesButton.textContent.trim() === 'Afficher les réponses') {
            repliesButton.textContent = 'Masquer les réponses';
        } else {
            repliesButton.textContent = 'Afficher les réponses';
        }
    } else {
        console.error('Element with ID "repliesButton' + commentId + '" not found.');
    }
}



function handleLikeClick(travelId, userId) {
    var likeButton = document.getElementById('likeButton');
    var likeCountElement = document.querySelector('.like-count');

    // Obtient l'état actuel du like
    var isLiked = likeButton.getAttribute('data-is-liked') === '1';

    fetch('Includes/likesTreatment.php?travelId=' + encodeURIComponent(travelId) + '&userId=' + encodeURIComponent(userId), {
        method: 'GET',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (isLiked) {
                    likeButton.setAttribute('data-is-liked', '0');
                    likeButton.querySelector('.material-symbols-outlined').textContent = 'favorite_border';
                    likeCountElement.textContent = parseInt(likeCountElement.textContent) - 1;
                } else {
                    likeButton.setAttribute('data-is-liked', '1');
                    likeButton.querySelector('.material-symbols-outlined').textContent = 'favorite';
                    likeCountElement.textContent = parseInt(likeCountElement.textContent) + 1;
                }
            } else {
                console.error('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des données:', error);
        });
}


function checkImageSize(input, maxSizeInMB) {
    if (input.files && input.files[0]) {
        const fileSize = input.files[0].size;
        const maxSize = maxSizeInMB * 1024 * 1024;

        if (fileSize > maxSize) {
            alert("La taille de l'image est trop grande. Veuillez choisir un fichier de taille inférieure à " + maxSizeInMB + " Mo.");
            input.value = '';
            return false;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = input.id === 'banner' ? document.getElementById('banner-preview') : document.getElementById('miniature-preview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function validateForm() {
    const bannerInput = document.getElementById("banner");
    const miniatureInput = document.getElementById("miniature");

    if (bannerInput.files.length > 0 && bannerInput.files[0].size > 2 * 1024 * 1024) {
        alert("La taille de la bannière est trop grande, merci de prendre une image de 2Mo maximum.");
        return false;
    }

    if (miniatureInput.files.length > 0 && miniatureInput.files[0].size > 1 * 1024 * 1024) {
        alert("La taille de la miniature est trop grande, merci de prendre une image de 1 Mo maximum.");
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var travelId = this.getAttribute('data-travel-id');
            document.getElementById('travelIdToDelete').value = travelId;
        });
    });
});
