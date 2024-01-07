import CustomContentLoaderController from '../custom-loader.js';
import { FetchController } from '../fetch-controller.js';
import { InputLengthValidation } from '../../validation/ValidationStrategy.js'

class EditableList {
    constructor(parent, listName) {
        this.listName = listName;
        this.parent = parent;

        this.loader = new CustomContentLoaderController();
        this.loader.setupAbsoluteCenteredPOV()

        this.fetchController = new FetchController('/api_fast_list_update');

        this.items = [];

        this.input = this.parent.parentNode.querySelector('input');
        this.output = this.parent.parentNode.querySelector('.input-error');
        this.validation = new InputLengthValidation(3, 20, 'Minimum 3 znaki maksymalnie 20')

        this.parent.parentNode.querySelector('.action-add').addEventListener('click', (e) => {
            this.handleAdd();
        });

        Array.from(this.parent.children).forEach(element => {
            element.querySelector('.action i')?.addEventListener('click', (e) => {
                this.handleDelete(e.target.dataset.id, element);
            });
        });
    }

    async sendQuery(data) {
        this.output.innerText = '';
        this.loader.show();
        try {
            const response = await this.fetchController.post({ ...data, list_name: this.listName });
            return response;
        } catch (error) {
            this.output.innerText = error.message
        } finally {
            this.loader.hide();
        }
    }

    handleDelete = async (id, element) => {
        const response = await this.sendQuery({ action: 'delete', id });
        if (response) element.remove();
    }

    async handleAdd() {
        let value = this.input.value;
        if (!this.validation.validate(value)) {
            console.log(this.validation.errorMessage);
            this.output.innerText = this.validation.errorMessage;
            return;
        }
        const response = await this.sendQuery({ action: 'add', value: value });
        if (response) {
            this.createNewElement(response.data.id, value);
        };
    }

    createNewElement = (id, value) => {
        const elementHTML = `
            <div class="action">
                <i class="material-icons action-delete" data-id="${id}">delete_forever</i>
                <span>${value}</span>
            </div>
            <div>
                <span>0</span>
            </div>`;

        const element = document.createElement('div');
        element.innerHTML = elementHTML;

        const icon = element.querySelector('.action-delete');
        icon.addEventListener('click', () => {
            this.handleDelete(id, element);
        });

        this.parent.insertBefore(element, this.parent.children[1]);
    }
}

const typesList = document.querySelector('.list.types')
if (typesList) {
    new EditableList(typesList, 'animal_types')
}

const featuresList = document.querySelector('.list.features')
if (featuresList) {
    new EditableList(featuresList, 'animal_features')
}