import FormController from './controllers/FormController.js';
import { TwoOrMoreWordsValidation, EmailValidation, NotEmptyValidation, PasswordValidation, ArePasswordsSameValidation } from './controllers/ValidationStrategy.js'
import { redirectToTargetOrDefault } from './utils.js'

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
        this.showOutput(error, true);
        setTimeout(() => {
            this.form.classList.remove('submitting');
        }, 1000);
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

        this.registerInput('login-email', new EmailValidation('Podaj adres email we właściwym formacie'))
        this.registerInput('login-password', new PasswordValidation(false, ''))
    }

    handleResponse(data) {
        setTimeout(() => {
            redirectToTargetOrDefault()
        }, 2000);
        this.onSuccess();
    }
}

class RegisterForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/signup'

        this.registerInput('register-names', new TwoOrMoreWordsValidation('Podaj imię i nazwisko'))
        this.registerInput('register-email', new EmailValidation('Podaj adres email we właściwym formacie'))
        this.registerInput('register-phone', new NotEmptyValidation('Podaj numer telefonu'))
        this.registerInput('register-password', new PasswordValidation())
        this.registerInput('register-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('register-password'), true))
    }

    handleResponse(data) {
        etTimeout(() => {
            redirectToTargetOrDefault()
        }, 2000);
        this.onSuccess();
    }
}

class ForgotPasswordForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/forgot-password'

        this.registerInput('forgot-password-email', new EmailValidation('Podaj adres email we właściwym formacie'))
    }

    handleResponse(data) {
        // TODO
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