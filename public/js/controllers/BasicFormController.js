import FormController from './FormController.js';


class BasicFormController extends FormController {
    constructor(formElement, url, showAllErrors = false) {
        super(formElement, url, showAllErrors);
        this.loader = document.querySelector('.custom-loader');
    }

    showLoader() {
        this.loader.classList.remove('success');
        this.submitting();
    }

    hideLoader() {
        this.submited();
    }

    beforeSend() {
        this.showLoader()
    }

    handleError(error) {
        setTimeout(() => {
            this.submited()
        }, 1500)
    }

    setLoaderSuccess() {
        this.loader.classList.add('success')
    }
}

export default BasicFormController;
