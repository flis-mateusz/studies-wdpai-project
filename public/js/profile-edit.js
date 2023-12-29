import BasicFormController from './controllers/BasicFormController.js';
import { TwoOrMoreWordsValidation, EmailValidation, NotEmptyValidation, PasswordValidation, ArePasswordsSameValidation, PhoneNumberValidation } from './validation/ValidationStrategy.js'


class ProfileEditForm extends BasicFormController {
    constructor(formElement) {
        super(formElement);

        this.url = '/profile_edit'

        // Avatar section
        this.avatar = this.form.querySelector('div.avatar.resp');
        this.headerAvatar = document.querySelector('header div.avatar');

        const reader = new FileReader();
        reader.onload = (e) => {
            this.avatar.style.backgroundImage = `url(${e.target.result})`
            this.headerAvatar.style.backgroundImage = `url(${e.target.result})`
        }

        this.avatarTip = this.form.querySelector('div.avatar-tip');

        this.form.querySelector('input[type="file"]').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                reader.readAsDataURL(file);
                this.avatarTip.classList.remove('hidden');
            }
        });

        // Inputs section
        this.registerInput('edit-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'))
        this.registerInput('edit-email', new EmailValidation('Wprowadź adres e-mail we właściwym formacie'))
        this.registerInput('edit-phone', new PhoneNumberValidation('Wprowadź prawidłowy numer telefonu'))
        this.registerInput('edit-password', new PasswordValidation(true))
        this.registerInput('edit-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('edit-password'), true))
    }

    onSuccess() {
        this.getInputByName('edit-avatar').value = '';
        setTimeout(() => {
            this.avatarTip.classList.add('hidden');
            this.loader.classList.add('success')
            setTimeout(() => {
                this.submited();
            }, 500);
        }, 500);
    }

    handleResponse(data) {
        if (data.status === 200) {
            this.onSuccess();
        }
    }
}

new ProfileEditForm(document.getElementById('profile-edit-form'));