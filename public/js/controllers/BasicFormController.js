import FormController from './FormController.js';


class BasicFormController extends FormController {
    constructor(formElement) {
        super(formElement);
        this.loader = document.querySelector('.custom-loader');
    }

    beforeSend() {
        this.loader.classList.remove('success');
        this.submited()
    }

    handleError(error) {
        this.submited()
    }

    onSuccess() {
        setTimeout(() => {
            this.loader.classList.add('success')
        }, 1000);
    }
}

export default BasicFormController;
