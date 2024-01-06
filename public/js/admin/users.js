import CustomContentLoaderController from '../controllers/custom-loader.js';
import { FetchController } from '../controllers/fetch-controller.js';
import GalleryTransformerController from '../controllers/gallery-transformer.js';

class AdminUsers {
    constructor() {
        this.loader = new CustomContentLoaderController();
        this.loader.setupAbsoluteCenteredPOV()
        this.output = document.querySelector('span.input-error');
        this.referrer = document.referrer;

        document.querySelectorAll('div.user').forEach(userElement => {
            new GalleryTransformerController(userElement.querySelector('.avatar'))
        })
    }
}

new AdminUsers();