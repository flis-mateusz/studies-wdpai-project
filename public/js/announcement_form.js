import FormControllerWithLoader from './controllers/form-controller-loader.js';
import { InputLengthValidation, NotEmptyValidation } from './validation/ValidationStrategy.js'
import AttachmentDragDropController from './controllers/attachment-drop-controller.js';

class AnnouncementAddForm extends FormControllerWithLoader {
    constructor(formElement) {
        super(formElement, '/api_add');

        this.attachmentController = new AttachmentDragDropController(
            this.form.querySelector('.attachment-dropdown'), 1)

        this.loader.setupFixed()

        // Inputs section
        this.registerInput('pet-name', new InputLengthValidation(2, 20, 'Imię powinno zaiwerać od 3 do 20 znaków'))
        this.registerInput('pet-age')
        this.registerInput('pet-gender')
        this.registerInput('pet-avatar', !this.getInputByName('announcement-id') ? new NotEmptyValidation('Dodaj zdjęcie zwierzaka') : null)
        this.registerInput('pet-type', new NotEmptyValidation('Wyszukaj typ zwierzaka'))
        this.registerInput('pet-kind')
        this.registerInput('pet-description', new InputLengthValidation(50, 2000, 'Opis powinien zawierać od 50 do 2000 znaków'))
        this.registerInput('pet-price')
        this.registerInput('pet-characteristics', null, true)
        this.registerInput('pet-location', new NotEmptyValidation('Podaj lokalizację'))
    }
}

new AnnouncementAddForm(document.getElementById('announcement-add-form'));