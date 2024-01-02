import FormControllerWithLoader from './controllers/form-controller-loader.js';
import { TwoOrMoreWordsValidation, EmailValidation, NotEmptyValidation, PasswordValidation, ArePasswordsSameValidation, PhoneNumberValidation } from './validation/ValidationStrategy.js'
import { redirectToTargetOrDefault } from './utils.js'

class LoginForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/api_login');
        this.outerOutput = document.querySelector('.login-successful')

        this.registerInput('login-email', new EmailValidation('Wprowadź adres email we właściwym formacie'))
        this.registerInput('login-password', new NotEmptyValidation(''))
    }

    showLoginSuccess(userName) {
        document.querySelector('.login-successful').innerText += ','
        let loginSuccessfulName = document.querySelector('.login-successful-name')
        loginSuccessfulName.innerText = `${userName}`
        loginSuccessfulName.style.fontSize = '1.2em'
    }

    handleResponse(responseData) {
        this.showLoginSuccess(responseData.data.userName)
        this.loader.completeLoadingAsync().then(redirectToTargetOrDefault)
    }
}

class RegisterForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/api_register');

        this.registerInput('register-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'))
        this.registerInput('register-email', new EmailValidation('Wprowadź adres email we właściwym formacie'))
        this.registerInput('register-phone', new PhoneNumberValidation('Wprowadź prawidłowy numer telefonu'))
        this.registerInput('register-password', new PasswordValidation())
        this.registerInput('register-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('register-password'), true))
    }
}

class ForgotPasswordForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/api_forgot_password');

        this.registerInput('forgot-password-email', new EmailValidation('Podaj adres email we właściwym formacie'))
    }
}

new RegisterForm(document.getElementById('register-form'));
new LoginForm(document.getElementById('login-form'));
new ForgotPasswordForm(document.getElementById('forgot-password-form'));


const formsConatiner = document.querySelector('.forms-container');
const registerSection = formsConatiner.querySelector('.register-section > div');
document.querySelectorAll('.switch-form').forEach(button => {
    button.addEventListener('click', () => {
        if (button.classList.contains('forgot-password')) {
            formsConatiner.classList.toggle('forgot-password')
        }
        else {
            formsConatiner.classList.toggle('register');
        }
        setTimeout(() => {
            registerSection.scroll(0, 0)
        }, 350)
    });
});
