import FormControllerWithLoader from './controllers/form-controller-loader.js';
import { TwoOrMoreWordsValidation, EmailValidation, PasswordValidation, ArePasswordsSameValidation, PhoneNumberValidation } from './validation/ValidationStrategy.js'
import { FetchController } from './controllers/fetch-controller.js';

class ProfileEditForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/api_profile_edit');

        this.initializeUIElements();
        this.initializeAvatarHandlers();

        this.loader.setupAbsolute()

        // Inputs section
        this.registerInput('edit-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'))
        this.registerInput('edit-email', new EmailValidation('Wprowadź adres e-mail we właściwym formacie'))
        this.registerInput('edit-phone', new PhoneNumberValidation('Wprowadź prawidłowy numer telefonu'))
        this.registerInput('edit-password', new PasswordValidation(true))
        this.registerInput('edit-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('edit-password'), true))
    }

    initializeUIElements() {
        this.isNewFile = false;
        this.previousAvatarUrl = null;
        this.avatarRemoveButton = this.form.querySelector('label.avatar-action.remove');
        this.avatar = this.form.querySelector('div.avatar.resp');
        this.headerAvatar = document.querySelector('header div.avatar');
        this.mobileAvatarCheckbox = this.form.querySelector('#mobile-avatar-checkbox');
        this.newAvatarTip = this.form.querySelector('div.avatar-tip');
        this.inputAttachment = this.form.querySelector('input[type="file"]');
    }

    initializeAvatarHandlers() {
        this.fileReader = new FileReader();
        this.fileReader.onload = this.handleAvatarReaderLoad.bind(this);

        this.inputAttachment.addEventListener('change', this.handleAvatarChange.bind(this));
        this.avatarRemoveButton.addEventListener('click', this.handleAvatarRemove.bind(this));
    }

    handleAvatarReaderLoad(e) {
        this.previousAvatarUrl = this.avatar.style.backgroundImage;
        this.avatar.style.backgroundImage = `url(${e.target.result})`;
        this.headerAvatar.style.backgroundImage = `url(${e.target.result})`;
    }

    handleAvatarChange(e) {
        this.mobileAvatarCheckbox.checked = false;
        const file = e.target.files[0];
        if (file) {
            this.hideOutput();
            this.isNewFile = true;
            this.avatarRemoveButton.classList.remove('hidden');
            this.fileReader.readAsDataURL(file);
            this.newAvatarTip.classList.remove('hidden');
        }
    }

    async handleAvatarRemove() {
        this.getInputByName('edit-avatar').value = '';
        this.newAvatarTip.classList.add('hidden');
        this.hideOutput();

        if (this.isNewFile) {
            this.isNewFile = false;
            if (this.previousAvatarUrl) {
                this.avatar.style.backgroundImage = this.previousAvatarUrl;
                this.headerAvatar.style.backgroundImage = this.previousAvatarUrl;
            } else {
                this.avatar.style.backgroundImage = '';
                this.headerAvatar.style.backgroundImage = '';
                this.avatarRemoveButton.classList.add('hidden');
            }
            this.mobileAvatarCheckbox.checked = false;
        } else {
            this.loader.show();
            new FetchController('/api_profile_avatar_delete').post()
                .then(() => {
                    this.avatar.style.backgroundImage = '';
                    this.headerAvatar.style.backgroundImage = '';
                    this.avatarRemoveButton.classList.add('hidden');
                    this.mobileAvatarCheckbox.checked = false;
                    this.isNewFile = false;
                    this.onSuccess();
                })
                .catch((error) => {
                    this.showOutput(error.message, true);
                    this.loader.hide();
                });
        }
    }

    onSuccess() {
        this.getInputByName('edit-avatar').value = '';
        this.isNewFile = false;
        this.newAvatarTip.classList.add('hidden');
        
        this.getInputByName('edit-password').value = '';
        this.getInputByName('edit-repassword').value = '';
        this.loader.completeLoadingAsync().then(this.loader.hide)
    }

    handleResponse(data) {
        if (data.status === 200) {
            this.onSuccess();
        }
    }

    handleError() {
        this.loader.hide();
    }
}

new ProfileEditForm(document.getElementById('profile-edit-form'));