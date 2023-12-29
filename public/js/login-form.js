import BasicFormController from './controllers/BasicFormController.js';
import { TwoOrMoreWordsValidation, EmailValidation, NotEmptyValidation, PasswordValidation, ArePasswordsSameValidation, PhoneNumberValidation } from './validation/ValidationStrategy.js'
import { redirectToTargetOrDefault } from './utils.js'

class LoginForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/signin'

        this.registerInput('login-email', new EmailValidation('Wprowadź adres email we właściwym formacie'))
        this.registerInput('login-password', new NotEmptyValidation(''))
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

        this.registerInput('register-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'))
        this.registerInput('register-email', new EmailValidation('Wprowadź adres email we właściwym formacie'))
        this.registerInput('register-phone', new PhoneNumberValidation('Wprowadź prawidłowy numer telefonu'))
        this.registerInput('register-password', new PasswordValidation())
        this.registerInput('register-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('register-password'), true))
    }

    handleResponse(data) {
        setTimeout(() => {
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

export default BasicFormController;