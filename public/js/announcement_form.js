import FormControllerWithLoader from './controllers/form-controller-loader.js';
import { InputMinLengthValidation, NotEmptyValidation } from './validation/ValidationStrategy.js'
import { FetchController } from './controllers/fetch-controller.js';
import AttachmentDragDropController from './controllers/attachment-drop-controller.js';

class AnnouncementAddForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/api_add');

        this.attachmentController = new AttachmentDragDropController(
            this.form.querySelector('.attachment-dropdown'), 1)

        this.loader.setupAbsoluteCenteredPOV()

        // Inputs section
        this.registerInput('pet-name', new NotEmptyValidation('Wprowadź imię zwierzaka'))
        this.registerInput('pet-age')
        this.registerInput('pet-gender')
        this.registerInput('pet-avatar', !this.getInputByName('announcement-id') ? new NotEmptyValidation('Dodaj zdjęcie zwierzaka') : null)
        this.registerInput('pet-type', new NotEmptyValidation('Wyszukaj typ zwierzaka'))
        this.registerInput('pet-kind')
        this.registerInput('pet-description', new InputMinLengthValidation(50, 'Opis powinien zawierać conajmniej 50 znaków'))
        this.registerInput('pet-price')
        this.registerInput('pet-characteristics', null, true)
        this.registerInput('pet-location', new NotEmptyValidation('Podaj lokalizację'))
    }
}

new AnnouncementAddForm(document.getElementById('announcement-add-form'));