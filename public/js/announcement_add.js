import FormControllerWithLoader from './controllers/form-controller-loader.js';
import { TwoOrMoreWordsValidation, EmailValidation, PasswordValidation, ArePasswordsSameValidation, PhoneNumberValidation } from './validation/ValidationStrategy.js'
import { FetchController } from './controllers/fetch-controller.js';
import AttachmentDragDropController from './controllers/attachment-drop-controller.js';

class AnnoucementAddForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/profile_edit');

        this.attachmentController = new AttachmentDragDropController(
            this.form.querySelector('.attachment-dropdown'), 3)

        this.loader.setupAbsoluteCenteredPOV()

        // Inputs section
        // this.registerInput('edit-names', new TwoOrMoreWordsValidation('Wprowadź imię i nazwisko'))
        // this.registerInput('edit-email', new EmailValidation('Wprowadź adres e-mail we właściwym formacie'))
        // this.registerInput('edit-phone', new PhoneNumberValidation('Wprowadź prawidłowy numer telefonu'))
        // this.registerInput('edit-password', new PasswordValidation(true))
        // this.registerInput('edit-repassword', new ArePasswordsSameValidation('Hasła nie są identyczne', this.getInputByName('edit-password'), true))
    }

    onSuccess() {

    }

    handleResponse(data) {

    }
}

new AnnoucementAddForm(document.getElementById('announcement-add-form'));