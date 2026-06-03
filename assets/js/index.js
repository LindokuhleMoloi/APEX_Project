document.addEventListener('DOMContentLoaded', function () {
  const STORAGE_KEY = 'ss_portal_completed_modules';
  const completeButtons = document.querySelectorAll('.complete-module');
  const moduleCards = document.querySelectorAll('.module-card');

  function loadCompletedModules() {
    try {
      const saved = localStorage.getItem(STORAGE_KEY);
      const parsed = saved ? JSON.parse(saved) : null;
      if (Array.isArray(parsed)) {
        return new Set(parsed.map(Number));
      }
    } catch (error) {
      console.warn('Failed to load completed module state:', error);
    }
    return new Set();
  }

  function saveCompletedModules(set) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(set)));
  }

  function updateModuleState(set) {
    moduleCards.forEach((card) => {
      const step = Number(card.dataset.step);
      const completed = set.has(step);
      const isUnlocked = completed || set.has(step - 1) || step === 1;
      const status = card.querySelector('.module-status');
      const completeButton = card.querySelector('.complete-module');
      const quizLink = card.querySelector('.quiz-link');

      if (completed) {
        card.classList.add('module-complete');
        card.classList.remove('module-locked');
        if (status) status.textContent = 'Completed';
        if (completeButton) {
          completeButton.textContent = 'Completed';
          completeButton.disabled = true;
        }
        if (quizLink) {
          quizLink.classList.remove('btn-disabled');
          quizLink.removeAttribute('aria-disabled');
          quizLink.removeAttribute('tabindex');
        }
      } else if (isUnlocked) {
        card.classList.remove('module-locked');
        if (status && status.textContent === 'Locked') status.textContent = 'Ready';
        if (completeButton) completeButton.disabled = false;
        if (quizLink) quizLink.classList.remove('btn-disabled');
      } else {
        card.classList.add('module-locked');
        if (status) status.textContent = 'Locked';
        if (completeButton) completeButton.disabled = true;
        if (quizLink) {
          quizLink.classList.add('btn-disabled');
          quizLink.setAttribute('aria-disabled', 'true');
          quizLink.setAttribute('tabindex', '-1');
        }
      }
    });
  }

  const completedModules = loadCompletedModules();
  updateModuleState(completedModules);

  completeButtons.forEach((button) => {
    button.addEventListener('click', function () {
      const card = button.closest('.module-card');
      if (!card || button.disabled) {
        return;
      }

      const step = Number(card.dataset.step);
      completedModules.add(step);
      saveCompletedModules(completedModules);
      updateModuleState(completedModules);
    });
  });
});
