import FormController from './controllers/FormController.js';
import { isEmail, isTwoOrMoreWords, arePasswordsSame, isPasswordStrong, redirectToTargetOrDefault } from './utils.js'

class BasicFormController extends FormController {
    constructor(formElement) {
        super(formElement);
        this.loader = document.querySelector('.custom-loader');
    }
    beforeSend() {
        this.loader.classList.remove('success');
        this.form.classList.add('submitting');
    }

    handleError(error) {
        this.form.classList.remove('submitting');
        this.showOutput(error, true);
    }

    onSuccess() {
        setTimeout(() => {
            this.loader.classList.add('success')
        }, 1000);
    }
}

class LoginForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/signin'
    }

    validate(formData) {
        if (!isEmail(formData.get('login-email'))) {
            this.markInput('login-email', true);
            this.showOutput('Podaj adres email we właściwym formacie', true);
            return false;
        }
        if (!formData.get('login-password')) {
            this.markInput('login-password', true);
            this.showOutput('Podaj hasło', true);
            return false;
        }
        return true;
    }

    handleResponse(data) {
        if (data.success) {
            setTimeout(() => {
                redirectToTargetOrDefault()
            }, 2000);
            this.onSuccess();
        }
    }
}

class RegisterForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/signup'
    }

    validate(formData) {
        if (!isTwoOrMoreWords(formData.get('register-names'))) {
            this.markInput('register-names', true);
            this.showOutput('Podaj imię i nazwisko', true);
            return false;
        }
        if (!isEmail(formData.get('register-email'))) {
            this.markInput('register-email', true);
            this.showOutput('Podaj adres email we właściwym formacie', true);
            return false;
        }
        if (!isPasswordStrong(formData.get('register-password'))) {
            this.markInput('register-password', true);
            this.showOutput('Hasło musi mieć co najmniej 8 znaków i zawierać duże i małe litery, cyfrę oraz znak specjalny', true);
            return false;
        }
        if (!arePasswordsSame(formData.get('register-password'), formData.get('register-repassword'))) {
            this.markInput('register-repassword', true);
            this.showOutput('Hasła różnią się od siebie', true);
            return false;
        }
        return true;
    }

    handleResponse(data) {
        if (data.success) {
            setTimeout(() => {
                redirectToTargetOrDefault()
            }, 2000);
            this.onSuccess();
        }
    }
}

class ForgotPasswordForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/forgot-password'
    }

    validate(formData) {
        if (!isEmail(formData.get('forgot-password-email'))) {
            this.markInput('forgot-password-email', true);
            this.showOutput('Podaj adres email we właściwym formacie', true);
            return false;
        }
        return true;
    }

    handleResponse(data) {
        if (data.success) {
            setTimeout(() => {
                // TODO
            }, 2000);
            this.onSuccess();
        }
    }
}

new RegisterForm(document.getElementById('register-form'));
new LoginForm(document.getElementById('login-form'));
new ForgotPasswordForm(document.getElementById('forgot-password-form'));


const formsConatiner = document.querySelector('.forms-container');
document.querySelectorAll('.switch-form').forEach(button => {
    button.addEventListener('click', () => {
        if (button.classList.contains('forgot-password')) {
            formsConatiner.classList.toggle('forgot-password')
        }
        else {
            formsConatiner.classList.toggle('register');
        }
    });
});