let questionCount = 1;

function updateQuestionIDs(questionElement, index) {
    questionElement.id = `question${index}`;
    questionElement.querySelector('label[for^="question"]').setAttribute('for', `question${index}`);
    questionElement.querySelector('input[id^="question"]').id = `question${index}`;
    questionElement.querySelector('input[name^="question"]').name = `question${index}`;
    questionElement.querySelector('label[for^="image_question"]').setAttribute('for', `image_question${index}`);
    questionElement.querySelector('input[id^="image_question"]').id = `image_question${index}`;
    questionElement.querySelector('input[name^="image_question"]').name = `image_question${index}`;
    questionElement.querySelectorAll('input[id^="correct"]').forEach((input, i) => {
        input.id = `correct${index}_${i + 1}`;
        input.name = `correct${index}_${i + 1}`;
    });
    questionElement.querySelectorAll('input[id^="answer"]').forEach((input, i) => {
        input.id = `answer${index}_${i + 1}`;
        input.name = `answer${index}_${i + 1}`;
    });
    questionElement.querySelector('label[for^="question"]').textContent = `Question ${index}`;
    questionElement.querySelector('.remove-question').style.display = index > 1 ? 'block' : 'none';
}

function clearQuestionInputs(questionElement) {
    questionElement.querySelector('input[id^="question"]').value = "";
    questionElement.querySelector('input[id^="image_question"]').value = "";
    questionElement.querySelectorAll('input[id^="answer"]').forEach(input => {
        input.value = "";
    });
    questionElement.querySelectorAll('input[id^="correct"]').forEach(input => {
        input.checked = false;
    });
}

function addQuestion() {
    if (questionCount >= 10) {
        alert("Vous avez atteint le nombre maximal de questions (10)");
        return;
    }

    questionCount++;
    const questionContainer = document.querySelector('.questionContainer');
    const newQuestion = questionContainer.cloneNode(true);

    clearQuestionInputs(newQuestion); // Purge les champs d'entrée de la nouvelle question clonée

    updateQuestionIDs(newQuestion, questionCount);

    newQuestion.querySelector('.remove-question').addEventListener('click', () => {
        newQuestion.remove();
        questionCount--;
        document.querySelectorAll('.questionContainer').forEach((element, i) => {
            updateQuestionIDs(element, i + 1);
        });
    });

    document.getElementById('newQuestionsContainer').appendChild(newQuestion);
}

document.querySelectorAll('.remove-question').forEach(button => {
    button.addEventListener('click', (event) => {
        const questionElement = event.target.closest('.questionContainer');
        if (questionElement) {
            questionElement.remove();
            questionCount--;
            document.querySelectorAll('.questionContainer').forEach((element, i) => {
                updateQuestionIDs(element, i + 1);
            });
        }
    });
});



function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?')) {
        var form = document.createElement('form');
        form.method = 'post';
        form.action = '';
        form.style.display = 'none';

        var inputAction = document.createElement('input');
        inputAction.type = 'hidden';
        inputAction.name = 'action';
        inputAction.value = 'delete_quiz';

        form.appendChild(inputAction);

        document.body.appendChild(form);

        form.submit();
    }
}