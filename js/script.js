/* ---------- Mobile menu ---------- */
const button = document.getElementById("btn_menu");
const ul = document.getElementById("ul");
if (button && ul) {
    button.addEventListener('click', function () {
        button.classList.toggle("active");
        ul.classList.toggle("is-active");
    });
}

/* ---------- Dark mode toggle ---------- */
const rootElement = document.documentElement;
const toggleButton = document.getElementById('toggle-theme');
if (localStorage.getItem('dpc-theme') === 'dark') {
    rootElement.classList.add('dark');
}
if (toggleButton) {
    toggleButton.addEventListener('click', () => {
        rootElement.classList.toggle('dark');
        localStorage.setItem('dpc-theme', rootElement.classList.contains('dark') ? 'dark' : 'light');
    });
}

/* ---------- IMC calculator ---------- */
function calculateIMC() {
    const height = parseFloat(document.getElementById('height').value) / 100;
    const weight = parseFloat(document.getElementById('weight').value);
    const resultField = document.getElementById('result');
    const adviceField = document.getElementById('advice');

    if (isNaN(height) || isNaN(weight) || height <= 0 || weight <= 0) {
        resultField.value = "Veuillez entrer des valeurs valides";
        adviceField.value = "";
        return;
    }

    const imc = (weight / (height * height)).toFixed(2);
    resultField.value = `Votre IMC : ${imc}`;

    if (imc < 18.5) {
        adviceField.value = "Vous êtes en insuffisance pondérale";
    } else if (imc < 25) {
        adviceField.value = "Votre poids est normal";
    } else if (imc < 30) {
        adviceField.value = "Vous êtes en surpoids";
    } else {
        adviceField.value = "Vous êtes en obésité";
    }
}
window.calculateIMC = calculateIMC;

/* ---------- Scroll reveal (one restrained device, reused site-wide) ---------- */
const revealTargets = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && revealTargets.length) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                io.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    revealTargets.forEach(el => io.observe(el));
} else {
    revealTargets.forEach(el => el.classList.add('is-visible'));
}

/* ---------- Generic form submit -> PHP endpoint ---------- */
function handleFormSubmit(formId, endpoint) {
    const form = document.getElementById(formId);
    if (!form) return;
    const statusBox = form.querySelector('.form-status');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalLabel = submitBtn ? submitBtn.textContent : '';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Envoi en cours...'; }

        try {
            const formData = new FormData(form);
            const response = await fetch(endpoint, { method: 'POST', body: formData });
            const data = await response.json();

            statusBox.classList.remove('ok', 'err');
            if (data.success) {
                statusBox.textContent = data.message || "Merci, votre demande a bien été envoyée.";
                statusBox.classList.add('ok', 'show');
                form.reset();
            } else {
                statusBox.textContent = data.message || "Une erreur est survenue. Merci de réessayer.";
                statusBox.classList.add('err', 'show');
            }
        } catch (err) {
            statusBox.classList.remove('ok');
            statusBox.textContent = "Impossible de contacter le serveur pour le moment.";
            statusBox.classList.add('err', 'show');
        } finally {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = originalLabel; }
        }
    });
}

handleFormSubmit('contact-form', 'php/contact_handler.php');
handleFormSubmit('consultation-form', 'php/consultation_handler.php');