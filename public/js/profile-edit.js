import FormController from './controllers/FormController.js';
import { TwoOrMoreWordsValidation, EmailValidation, NotEmptyValidation, PasswordValidation, ArePasswordsSameValidation } from './controllers/ValidationStrategy.js'

class ProfileEditForm extends FormController {
    constructor(formElement) {
        super(formElement);
        this.url = '/profile_edit'

        // Avatar section
        this.avatar = this.form.querySelector('div.avatar.resp');

        const reader = new FileReader();
        reader.onload = (e) => {
            this.avatar.style.backgroundImage = `url(${e.target.result})`
        }

        const avatarTip = this.form.querySelector('div.avatar-tip');

        this.form.querySelector('input[type="file"]').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                reader.readAsDataURL(file);
                avatarTip.classList.remove('hidden');
            }
        });


        // Inputs section
        this.registerInput('edit-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'))
        this.registerInput('edit-email', new EmailValidation('Wprowadź adres e-mail we właściwym formacie'))
        this.registerInput('edit-phone', new NotEmptyValidation('Wprowadź numer telefonu'))
        this.registerInput('edit-password', new PasswordValidation(true))
        this.registerInput('edit-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('edit-password'), true))
    }

    handleResponse(data) {
        //TODO
    }
}

new ProfileEditForm(document.getElementById('profile-edit-form'));