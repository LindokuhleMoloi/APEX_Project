document.addEventListener('DOMContentLoaded', function () {
  const quizForm = document.querySelector('.exit-quiz-card');
  if (!quizForm) {
    return;
  }

  const answerMap = {
    1: [0, 1, 1],
    2: [0, 1, 1],
    3: [1, 1, 1],
    4: [0, 1, 0],
    5: [0, 0, 1],
    6: [1, 1, 1],
  };

  const moduleHeader = document.querySelector('h1');
  const moduleMatch = moduleHeader ? moduleHeader.textContent.match(/Module\s+(\d+)/) : null;
  const moduleId = moduleMatch ? Number(moduleMatch[1]) : null;
  const submitButton = quizForm.querySelector('button.btn-primary');
  const resultBox = document.createElement('div');
  resultBox.className = 'quiz-result';
  resultBox.style.display = 'none';
  quizForm.appendChild(resultBox);

  function getSelectedAnswers() {
    const answers = [];
    quizForm.querySelectorAll('.question-item').forEach((question, index) => {
      const checked = question.querySelector('input[type="radio"]:checked');
      if (checked) {
        answers[index] = Array.from(question.querySelectorAll('input[type="radio"]')).indexOf(checked);
      }
    });
    return answers;
  }

  function renderResult(score, total) {
    const percent = Math.round((score / total) * 100);
    resultBox.textContent = `You scored ${score} out of ${total} (${percent}%).`;
    resultBox.style.display = 'block';
  }

  if (submitButton) {
    submitButton.addEventListener('click', function (event) {
      event.preventDefault();
      if (!moduleId || !answerMap[moduleId]) {
        return;
      }

      const selected = getSelectedAnswers();
      const correct = answerMap[moduleId];
      if (selected.length !== correct.length || selected.includes(undefined)) {
        resultBox.textContent = 'Please answer all questions before submitting.';
        resultBox.style.display = 'block';
        resultBox.classList.add('quiz-warning');
        return;
      }

      let score = 0;
      selected.forEach((choice, index) => {
        if (choice === correct[index]) {
          score += 1;
        }
      });
      renderResult(score, correct.length);
      resultBox.classList.remove('quiz-warning');
      resultBox.classList.add('quiz-success');
    });
  }

  const radioInputs = quizForm.querySelectorAll('input[type="radio"]');
  radioInputs.forEach((input) => {
    input.addEventListener('change', function () {
      resultBox.style.display = 'none';
    });
  });
});
