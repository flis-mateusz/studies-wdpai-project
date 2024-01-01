import FormControllerWithLoader from './controllers/form-controller-loader.js';
import { NumberValidation, EmailValidation, PasswordValidation, ArePasswordsSameValidation, InputMinLengthValidation, NotEmptyValidation } from './validation/ValidationStrategy.js'
import { FetchController } from './controllers/fetch-controller.js';
import AttachmentDragDropController from './controllers/attachment-drop-controller.js';

class AnnoucementAddForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/add_announcement');

        this.attachmentController = new AttachmentDragDropController(
            this.form.querySelector('.attachment-dropdown'), 3)

        this.loader.setupAbsoluteCenteredPOV()

        // Inputs section
        this.registerInput('pet-name', new NotEmptyValidation('Wprowadź imię zwierzaka'))
        this.registerInput('pet-age')
        this.registerInput('pet-gender')
        this.registerInput('pet-avatar', new NotEmptyValidation('Dodaj zdjęcie zwierzaka'))
        this.registerInput('pet-type', new NotEmptyValidation('Wyszukaj typ zwierzaka'))
        this.registerInput('pet-description', new InputMinLengthValidation(1, 'Opis powinien zawierać conajmniej 1 znaków'))
        this.registerInput('pet-price')
    }

    onSuccess() {

    }

    handleResponse(data) {
        
    }
}

new AnnoucementAddForm(document.getElementById('announcement-add-form'));